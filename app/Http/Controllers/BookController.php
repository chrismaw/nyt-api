<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\json;

class BookController extends Controller
{
    public function getBestSellers(GetBookRequest $request) {

        $validated = $request->validated();
        if ($request->query('test')) {
            return $this->testGetBestSellers($validated);
        }

//        $author = $validated['author'] ?? null;
//        $title = $validated['title'] ?? null;
//        $isbns = $validated['isbn'] ?? null;
//        $offset = $validated['offset'] ?? 0;

        //build query
        $query = null;
        if (count($validated)) {
            $last = array_key_last($validated);
            foreach ($validated as $key => $value) {
                if ($key !== 'isbn') {
                    $query .= ($key . '=' . $value . '&');
                } else { // isbn
                    foreach ($value as $isbn) {
                        $query .= ('isbn[]=' . $isbn . '&');
                    }
                }
                if ($key == $last) {
                    $query = rtrim($query, '&');
                }
            }
        }
        $client = new \GuzzleHttp\Client();
        $response = $client->get(config('nyt-api.api_path') . 'lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key') . ($query ? '&' . $query : ''));
        if ($response->getStatusCode() == 200){
            $contents = json_decode($response->getBody()->getContents(), true);
            return json_encode($contents['results']);

        } else {
            return response()->json(['error' => $response->getStatusCode() . ' error found'], $response->getStatusCode());
        }
    }

    private function testGetBestSellers($validated): string|false
    {
        $author = $validated['author'] ?? null;
        $title = $validated['title'] ?? null;
        $isbns = $validated['isbn'] ?? null;
        $offset = $validated['offset'] ?? 0;
        $path = 'test-history.json';
        // Check if the file exists
        if (!Storage::disk('app')->exists($path)) {

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
            $matchesISBN = !$isbns;
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
        // Return the filtered books
        return $filteredBooks->splice($offset, 20);

    }
}
