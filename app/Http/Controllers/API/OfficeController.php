<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $office = Office::create($request->only(['name', 'latitude', 'longitude']));

        return response()->json(['message' => 'Office created', 'office' => $office], 201);
    }

    public function index()
    {
        return Office::all();
    }
}
