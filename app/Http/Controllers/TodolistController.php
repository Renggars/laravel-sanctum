<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\TodoCreateRequest;
use App\Http\Requests\Todo\TodoUpdateRequest;
use App\Http\Resources\TodolistResource;
use App\Models\Todolist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodolistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todolists = Todolist::latest()->get();

        return TodolistResource::collection($todolists);
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
        $todo = Todolist::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Todolist not found'], 404);
        }

        return new TodolistResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TodoUpdateRequest $request, string $id): TodolistResource
    {
        $data = $request->validated();

        $todolist = Todolist::find($id);
        if (!$todolist) {
            return response()->json(['message' => 'Todolist not found'], 404);
        }

        $todolist->fill($data);
        $todolist->save();

        return new TodolistResource($todolist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $todolist = Todolist::find($id);
        if (!$todolist) {
            return response()->json(['message' => 'Todolist not found'], 404);
        }

        $todolist->delete();

        return response()->json([
            'message' => 'Todolist deleted successfully'
        ], 200);
    }
}
