<?php

    namespace App\Service;

    use Symfony\Component\HttpClient\HttpClient;

    class FetchWithHttpResponseService
    {
        public function getJsonFromAPI($url, $maxRetries = 200)
        {
            $httpClient = HttpClient::create();
            $retryCount = 0;

            while ($retryCount < $maxRetries) {
                $response = $httpClient->request('GET', $url);               
                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    $content = $response->getContent();
                    $data = json_decode($content, true);
                    return $data;
                }

                $retryCount++;
            }

            throw new \Exception('La requête a échoué après plusieurs tentatives.');
        }
    }