<?php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;

$kernel = new Kernel('prod', false);

$kernel->boot();

$monService = $kernel->getContainer()->get('AutomatisationService');

$monService->executeDataReset();
$monService->executeDataResetUsers();
$monService->executeDataGetRoute();
$monService->executeDataLoadUsersRoute();