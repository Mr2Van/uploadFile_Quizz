<?php

namespace App\Http\Controllers;

use App\Models\files;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StorefilesRequest;
use App\Http\Requests\UpdatefilesRequest;

class FilesController extends Controller
{
    /**
     * affiche la liste des fichier
     */
    public function index()
    {
        $files = Files::all();
        return response()->json($files);
    }


    /**
     * affiche une donne specifique
     */

     public function shows($fileId)
     {
         try {


             $file = Files::findOrFail($fileId);
             
             if (Storage::disk('s3')->exists($file->path)) {
                 // Récupération du contenu du fichier
                 $contents = Storage::disk('s3')->get($file->path);
                 
                 // Détermination du type MIME
                 $mime = Storage::disk('s3')->mimeType($file->path);
                 
                 // Si c'est une image ou un PDF, on peut l'afficher directement
                 if (strpos($mime, 'image/') === 0 || $mime === 'application/pdf') {
                     return response($contents)->header('Content-Type', $mime);
                 }
                 
                 // Pour les autres types de fichiers, proposer le téléchargement
                 return response($contents)
                     ->header('Content-Type', $mime)
                     ->header('Content-Disposition', 'attachment; filename="' . $file->name . '"');
             }
             
             return response()->json([
                 'success' => false,
                 'message' => 'Fichier non trouvé'
             ], 404);
             
         } catch (\Exception $e) {
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de l\'affichage: ' . $e->getMessage()
             ], 500);
         }
     }


     /**
     * les donne dans la BD et AWS S3
     */
    
public function update(Request $request, $id)
{
    try {


        // Récupérer le fichier depuis la base de données
        $file = Files::findOrFail($id);  // Chercher le fichier dans la base de données

        // Validation des données de la requête
        $request->validate([
            'file' => 'nullable|file|max:10240', 
            'name' => 'required|string',       
            'description' => 'required|string' 
        ]);

        // Mise à jour du nom et de la description
        $file->name = $request->name;
        $file->description = $request->description;

        // Si un nouveau fichier est téléchargé
        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier de S3
            if ($file->path) {
                Storage::disk('s3')->delete($file->path);
            }

            // Récupérer le fichier téléchargé
            $newFile = $request->file('file');
            
            // Générer un nom unique pour le fichier
            $originalName = $newFile->getClientOriginalName();
            $uniqueFileName = time() . '_' . Str::random(10) . '_' . $originalName;

            // Télécharger le nouveau fichier sur S3
            $path = Storage::disk('s3')->putFileAs(
                'uploads',             // Dossier où sera stocké le fichier dans S3
                $newFile,              // Le fichier téléchargé
                $uniqueFileName       // Nom unique pour éviter les conflits
            );

            // Mettre à jour le chemin du fichier dans la base de données
            $file->path = $path;

            // Générer l'URL du fichier
            $url = Storage::disk('s3')->url($path);

            // Mettre à jour le type et la taille du fichier
            $file->type = $url;  // Si vous voulez stocker l'URL ou le type MIME
            $file->size = $newFile->getSize();  // Taille en octets
        }

        // Sauvegarder les modifications dans la base de données
        $file->save();

        // Retourner la réponse de succès
        return response()->json([
            'success' => true,
            'message' => 'Fichier mis à jour avec succès',
            'file' => $file
        ]);

    } catch (\Exception $e) {
        // En cas d'erreur, retourner un message d'erreur
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour du fichier : ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Stockee les donne dans la BD et AWS S3
     */
    public function store(Request $request)
    {
        
        try {
            // Validation du fichier
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'name' => 'required|string',
                'description' => 'required|string'
            ]);

            $file = $request->file('file');
            
            // Création d'un nom unique pour le fichier
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . Str::random(10) . '_' . $originalName;
            
            // Upload du fichier sur S3 avec son contenu
            $path = Storage::disk('s3')->putFileAs(
                'uploads', // dossier dans S3
                $file,     // le fichier
                $uniqueFileName // nom unique du fichier
            );

            
            // Génération de l'URL du fichier
            $url = Storage::disk('s3')->url($path);
            
            // Sauvegarde des informations dans la base de données
            $fileModel = Files::create([
                'name' => $request->name,
                'description' => $request->description,
                'path' => $path,
                'type' => $url,
                'size'=>1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès',
                'file' => $fileModel
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], 500);
        }

        
       
        
    } 

    /**
     * telecharge les resource.
     */

    public function download($id)
    {
        try {
            // Récupération du fichier dans la base de données
            $file = Files::findOrFail($id);
            
            // Vérification de l'existence du fichier sur S3
            if (!Storage::disk('s3')->exists($file->path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé sur S3'
                ], 404);
            }
            
            // Récupération du type MIME du fichier
            $mime = Storage::disk('s3')->mimeType($file->path);
            
            // Génération du nom de téléchargement
            $downloadName = $file->name;
            
            // Téléchargement du fichier
            return Storage::disk('s3')->download($file->path, $downloadName, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement: ' . $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * supprimer un fichier dans la bd et amazone s3.
     */
    public function delete($id)
    {
        try {
            $file = Files::findOrFail($id);

            // Supprimer le fichier de S3
            Storage::disk('s3')->delete($file->path);

            // Supprimer l'enregistrement de la BD
            $file->delete();

            return response()->json([
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
