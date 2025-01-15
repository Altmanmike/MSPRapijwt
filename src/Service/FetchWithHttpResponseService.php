<?php

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchWithHttpResponseService
{
    public function __construct(private HttpClientInterface $client){}

    public function getJsonFromAPI($url)
    {
        $response = $this->client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $content = $response->getContent();
            $data = json_decode($content, true);   

            return $data;
        }      
    }
}