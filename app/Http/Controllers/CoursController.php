<?php

namespace App\Http\Controllers;

use App\Models\cours;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorecoursRequest;
use App\Http\Requests\UpdatecoursRequest;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    /**
     * affiche tout les cours de ma bd 
     */
    public function index()
    {
          //

        return Cours::with('professeur')->get();
    }

     /**
     * affiche tout un cours precis en fonction de l'id passe en paramettre
     */
    public function show( $id)
    {
        $cours = Cours::findOrFail($id);
        
         //   return $cours->load('qcms', 'professeur');

         return response()->json($cours);
        
    }

    /**
     * cree un cours dans la bd
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_cours' => 'nullable|date',
            'professeur_id' => 'required|exists:users,id'
        ]);

        $cours = Cours::create($validated);
        return response()->json($cours, 201);
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'date_cours' => 'nullable|date',
            'professeur_id' => 'sometimes|exists:users,id'
        ]);
        $cours = Cours::findOrFail($id);

        $cours->update($validated);

        return response()->json($cours);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        $cours = Cours::findOrFail($id);

        $cours->delete();
        
        return response()->json(null, 204);
    }

    public function qcms(Cours $cours)
    {
        return $cours->qcms;
    }
}
