<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Laravel\Pail\File;

class GetBestSellers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-best-sellers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Complete list of Best Sellers and store in JSON Format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $results = [];
        $count = 0;

        $client = new \GuzzleHttp\Client();
        $response = $client->get(config('nyt-api.api_path') . 'lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key'));

        $json = json_decode($response->getBody()->getContents(), true);
        $num_results = intval($json['num_results']); // 36488

        $this->info('Number of Results: ' . $num_results);
//        $total_requests = ceil($num_results/20);
        $this->info('Total Requests: 5');

        $bar = $this->output->createProgressBar(5); // only limited to 5 requests but 100 is sufficient for project
        $bar->start();

        $results = array_merge($results, $json['results']); // first 20
        $count += count($json['results']);
        $bar->advance();

        for ($i = 20; $i <= 80; $i+=20) {
            $this->info($i);
            $response = $client->get('https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key') . '&offset=' . $i);
            $json = json_decode($response->getBody()->getContents(), true);
            $results = array_merge($json['results'], $results);
            $count += count($json['results']);
            $bar->advance();
        }
//        while ($count <= 80) { // only want 100 total
//            $response = $client->get('https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json?api-key=' . config('nyt-api.api_key') . '&offset=' . $count);
//            $results = array_merge($results, $json['results']); // next 20
//            $count += 20;
//            $bar->advance();
//        }

        Storage::disk('app')->put('history.json',json_encode($results)); // overwrites if file already exists
        $this->newLine(1);
        $this->info($count . ' books stored' . PHP_EOL);
        return true;

    }
}
