<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\TodoCreateRequest;
use App\Http\Resources\TodolistResource;
use App\Models\Todolist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodolistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): TodolistResource
    {
        $todolists = Todolist::latest()->get();

        return new TodolistResource($todolists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TodoCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $todolist = new Todolist($data);
        $todolist->save();

        return (new TodolistResource($todolist))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
