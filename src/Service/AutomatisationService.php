<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class AutomatisationService
{
    // Exécuter la route app_dataReset
    public function executeDataReset()
    {
        $httpClient = HttpClient::create();
        $request = Request::create('/data/data-reset', 'GET');
        $response = $httpClient->request($request->getMethod(), $request->getUri());

        return $response;
    }

    // Exécuter la route app_dataResetUsers
    public function executeDataResetUsers()
    {
        $httpClient = HttpClient::create();
        $request = Request::create('/data/data-reset-users', 'GET');
        $response = $httpClient->request($request->getMethod(), $request->getUri());

        return $response;
    }

    // Exécuter la route app_dataGet
    public function executeDataGetRoute()
    {
        $httpClient = HttpClient::create();
        $request = Request::create('/data/data-get', 'GET');
        $response = $httpClient->request($request->getMethod(), $request->getUri());

        return $response;
    }

    // Exécuter la route app_dataLoadUsers
    public function executeDataLoadUsersRoute()
    {
        $httpClient = HttpClient::create();
        $request = Request::create('/data/data-load-users', 'GET');
        $response = $httpClient->request($request->getMethod(), $request->getUri());

        return $response;
    }
}