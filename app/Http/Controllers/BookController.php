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
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Error decoding JSON: ' . json_last_error_msg()], 500);
        }
        $filteredBooks = $books->filter(function ($book) use ($author, $title, $isbns) {
            $matchesAuthor = !$author || str_contains(strtolower($book['author']), strtolower($author));
            $matchesTitle = !$title || str_contains(strtolower($book['title']), strtolower($title));
            $matchesISBN = false;
            if ($isbns && count($isbns)) {
                foreach ($book['isbns'] as $isbnSet) {
                    foreach ($isbnSet as $bookIsbn) {
                        if (in_array($bookIsbn, array_values($isbns))) {
                            $matchesISBN = true;
                        }
                    }
                }
            }
            return $matchesAuthor && $matchesTitle && $matchesISBN;
        });
        $booksData = $filteredBooks->splice($offset, 20);
        // Return the filtered books
        return $booksData->values();

    }
}
