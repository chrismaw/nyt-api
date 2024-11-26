<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function getBestSellers(GetBookRequest $request) {

        $validated = $request->validated();

        $author = $validated['author'] ?? null;
        $title = $validated['title'] ?? null;
        $isbn[] = $validated['isbn[]'] ?? null;
        $offset = $validated['offset'] ?? 0;

        $path = 'history.json';

        // Check if the file exists
        if (!Storage::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        // Retrieve and decode JSON data
        $jsonContent = collect(Storage::disk('app')->json($path));
        $offsetJSON = $jsonContent->splice($offset, 20);
        dd($offsetJSON);
        $books = json_decode($jsonContent, true);
        $books = Storage::json($filePath);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Error decoding JSON: ' . json_last_error_msg()], 500);
        }


    }
}
