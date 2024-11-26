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
        $isbns = $validated['isbn'] ?? null;
        $offset = $validated['offset'] ?? 0;
        $path = 'history.json';

        // Check if the file exists
        if (!Storage::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        // Retrieve and decode JSON data
        $books = collect(Storage::disk('app')->json($path));
        $filteredBooks = $books->filter(function ($book) use ($author, $title, $isbns) {
            $matchesAuthor = !$author || str_contains(strtolower($book['author']), strtolower($author));
            $matchesTitle = !$title || str_contains(strtolower($book['title']), strtolower($title));
            $isbnMatches = false;
            dd($book['isbns'][0]);
            if ($isbns && count($isbns)) {
                foreach ($isbns as $isbn) {
                    $isbnMatches = in_array($isbn, array_values($book['isbns'][0]));
                }
            }
            $matchesISBN = !$isbns || $isbnMatches;
            return $matchesAuthor && $matchesTitle && $matchesISBN;
        });

        $booksData = $filteredBooks->splice($offset, 20);
        dd($booksData);
        // Return the filtered books
        return $booksData->values();
        $books = $jsonContent->splice($offset, 20);
        dd($booksData);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Error decoding JSON: ' . json_last_error_msg()], 500);
        }


    }
}
