<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeploymentRequest;
use App\Http\Requests\UpdateDeploymentRequest;
use App\Models\Deployment;

class DeploymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeploymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Deployment $deployment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deployment $deployment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeploymentRequest $request, Deployment $deployment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deployment $deployment)
    {
        //
    }
}
