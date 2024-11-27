<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function getBestSellers(GetBookRequest $request) {

        $validated = $request->validated();

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
//        dd(config('nyt-api.api_path') . 'lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key') . ($query ? '&' . $query : ''));
        $client = new \GuzzleHttp\Client();
        $response = $client->get(config('nyt-api.api_path') . 'lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key') . ($query ? '&' . $query : ''));
        $json = json_decode($response->getBody()->getContents(), true);
        dd($json);
    }
}
