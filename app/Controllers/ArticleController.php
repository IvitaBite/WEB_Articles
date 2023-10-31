<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use App\Response;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ArticleController
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function index(array $vars): Response
    {
        $categories = [
            "general",
            "programming",
            "dad",
            "knock-knock"
        ];
        $currentDateTime = Carbon::now();
        return new Response('index', ['categories' => $categories, 'currentTime' => $currentDateTime]);
    }

    public function show(array $vars): ?Response
    {

        $categories = $vars['type'];

        $apiUrl = "https://official-joke-api.appspot.com/jokes/{$categories}/random";

        try {
            $response = $this->client->get($apiUrl);
        } catch (GuzzleException $e) {
            echo "GuzzleException: " . $e->getMessage() . "\n";
            return null;
        }

        $jokeData = json_decode($response->getBody()->__toString(), true);

        if (!is_array($jokeData) || empty($jokeData)) {
            echo "Error: No jokes found for the given type.\n";
            return null;
        }

        $currentDateTime = Carbon::now();
        $data = [
            'jokes' => new Article($jokeData[0]['type'], $jokeData[0]['setup'], $jokeData[0]['punchline']),
            'currentTime' => $currentDateTime
        ];
        return new Response ('jokes', $data);
    }
}