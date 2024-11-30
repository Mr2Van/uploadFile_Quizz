<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\QcmsController;




///////////////////// routes Test //////////////////////////////////////////////////
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function(){
    return 'API';
});


////////////////Authentification///////////////////////////////////////////////////
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/////////////////// Gestion des fichiers////////////////////////////////

// route pour afficher le contenue d'un fichier 
Route::get('/files/{file}', [FilesController::class, 'shows'])->name('shows');

// route pour  afficher la liste fichier dans la bd
Route::get('/files', action: [FilesController::class, 'index'])->name('index');


// Route pour télécharger le fichier
Route::get('/files/{file}/download', [FilesController::class, 'download'])->name('download');

//uploade les fichier
Route::post('/files', [FilesController::class, 'store'])->name('store');

//uploade les fichier
Route::put('/files/upload', [FilesController::class, 'update'])->name('update');


// supprimer un fichier
Route::delete('/files/{id}', [FilesController::class, 'delete'])->name('delete');




//////////////////////////// Gestion des cours //////////////////////////////////////

Route::apiResource('cours', controller: CoursController::class);




/////////////////Gestion des  Qcms /////////////////////////////

Route::apiResource('qcms', QcmsController::class);
Route::post('qcms/creer', [QcmsController::class, 'store']);
Route::post('qcms/calculer-resultat', [QcmsController::class, 'calculerResultat']);
