<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class SearchService
{
  protected $client;

  public function __construct(Client $client)
  {
    $this->client = $client;
  }

  public function searchOffers($term)
  {
    // Use OpenAI to generate search URLs based on the term
    $urls = $this->generateSearchUrls($term);
    dd($urls);

    $results = [];
    foreach ($urls as $url) {
      $response = $this->client->request('GET', $url);
      $html = (string) $response->getBody();

      // Parse the HTML to extract the required data
      $results = array_merge($results, $this->parseHtml($html));
    }

    // Optionally, use OpenAI to process or enhance the results
    $processedResults = $this->processResultsWithAI($results);

    // Paginate the results
    return $this->paginate($processedResults);
  }

  protected function generateSearchUrls($term)
  {
    // Generate search URLs using OpenAI
    $response = Http::withToken(config('services.openai.secret'))->post(config('services.openai.url') . '/' . config('services.openai.version') . '/chat/completions', [
      'model' => 'gpt-3.5-turbo',
      'prompt' => "Generate a list of URLs that might have offers for {$term}.",
      'max_tokens' => 150,
    ])->json();

    dd($response);

    $urls = explode("\n", $response['choices'][0]['text']);
    return array_filter($urls, fn ($url) => filter_var($url, FILTER_VALIDATE_URL));
  }

  protected function parseHtml($html)
  {
    // Implement the logic to parse the HTML and extract the required data
    $results = [];

    // Dummy logic for parsing
    // You can use DOMDocument, DOMXPath, or other libraries for parsing HTML

    return $results;
  }

  protected function processResultsWithAI($results)
  {
    // Example AI processing, replace with actual logic
    $processedResults = [];
    foreach ($results as $result) {
      $response = Http::withToken(config('services.openai.secret'))->post('/v1/engines/davinci/completions', [
        'prompt' => "Process this offer: {$result['title']}",
        'max_tokens' => 50,
      ]);
      $processedResults[] = [
        'title' => $result['title'],
        'link' => $result['link'],
        'logo' => $result['logo'],
        'processed_text' => $response['choices'][0]['text']
      ];
    }
    return $processedResults;
  }

  protected function paginate($items, $perPage = 10)
  {
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentResults = array_slice($items, ($currentPage - 1) * $perPage, $perPage);
    return new LengthAwarePaginator($currentResults, count($items), $perPage);
  }
}
