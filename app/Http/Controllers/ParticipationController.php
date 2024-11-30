<?php

namespace App\Http\Controllers;

use App\Models\participation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreparticipationRequest;
use App\Http\Requests\UpdateparticipationRequest;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreparticipationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(participation $participation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateparticipationRequest $request, participation $participation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(participation $participation)
    {
        //
    }
}
