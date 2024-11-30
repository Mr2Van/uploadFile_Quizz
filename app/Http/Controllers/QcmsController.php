<?php

namespace App\Http\Controllers;

use App\Models\qcms;
use App\Models\questions;
use App\Models\reponses;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreqcmsRequest;
use App\Http\Requests\UpdateqcmsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class QcmsController extends Controller
{
    // affichee les qcm 
    public function index()
    {
        //
        return qcms::with('cours', 'questions.reponses')->get();
    }

    // cree et enregistre les Qcm
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // Validation du QCM
            $validatedQcm = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'cours_id' => 'required|exists:cours,id',
                'questions' => 'required|array',
                'questions.*.enonce' => 'required|string',
                'questions.*.points' => 'nullable|integer',
                'questions.*.reponses' => 'required|array|min:2',
                'questions.*.reponses.*.libelle' => 'required|string',
                'questions.*.reponses.*.est_correcte' => 'required|boolean'
            ]);

            // Création du QCM
            $qcm = qcms::create([
                'titre' => $validatedQcm['titre'],
                'description' => $validatedQcm['description'] ?? null,
                'duration' => $validatedQcm['duration'],
                'start_date' => $validatedQcm['start_date'],
                'end_date' => $validatedQcm['end_date'],
                'cours_id' => $validatedQcm['cours_id']
            ]);

            // Création des questions et réponses
            foreach ($validatedQcm['questions'] as $questionData) {
                $question = questions::create([
                    'qcms_id' => $qcm->id,
                    'enonce' => $questionData['enonce'],
                    'points' => $questionData['points'] ?? 1
                ]);

                // Vérifier qu'il n'y a qu'une seule bonne réponse
                $correctReponses = array_filter($questionData['reponses'], function($reponse) {
                    return $reponse['est_correcte'];
                });


                if (count($correctReponses) !== 1) {
                    throw new \Exception('Chaque question doit avoir exactement une bonne réponse');
                }

                // Création des réponses
                foreach ($questionData['reponses'] as $reponseData) {
                    reponses::create([
                        'question_id' => $question->id,
                        'libelle' => $reponseData['libelle'],
                        'est_correcte' => $reponseData['est_correcte']
                    ]);
                }

            }

            return response()->json($qcm->load('questions.reponses'), 201);
        });
    }

     //affiche un qcm avec les question
     public function show( $id)
     {
         $qcm = qcms::findOrFail($id);
        // return $qcms->load('cours', 'questions.reponses');
         return response()->json([ $qcm->load('cours', 'questions.reponses')]);
     }


      // supprimer un qcm 
    public function destroy($id)
    {
        $qcm = qcms::findOrFail($id);
        $qcm->delete();
        return response()->json(null, 204);
    }
 
















    //////calcule le resultat/////
    public function calculerResultat(Request $request)
    {
        $validated = $request->validate([
            'qcms_id' => 'required|exists:qcms,id',
            'user_id' => 'required|exists:users,id',
            'reponses' => 'required|array'
        ]);

        $qcm = qcms::findOrFail($validated['qcm_id']);
        $reponses = $validated['reponses'];
        $totalPoints = 0;

        foreach ($qcm->questions as $question) {
            // Trouver la réponse de l'étudiant pour cette question
            $reponseEtudiant = collect($reponses)
                ->firstWhere('question_id', $question->id);

            if ($reponseEtudiant) {
                $bonneReponse = $question->reponses->first(function ($reponse) {
                    return $reponse->est_correcte;
                });

                // Vérifier si la réponse de l'étudiant est correcte
                if ($bonneReponse->id == $reponseEtudiant['reponse_id']) {
                    $totalPoints += $question->points;
                }
            }
        }

        
        return response()->json([
            'points' => $totalPoints,
            'total_points' => $qcm->questions->sum('points')
        ]);
    }

    /**
     * noooooooooooooo No No No No No NO no
     */
    public function update(UpdateqcmsRequest $request, qcms $qcms)
    {
        //
                // Logique de mise à jour du QCM
    }

   
}
