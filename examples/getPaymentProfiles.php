<?php

require '../vendor/autoload.php';

use FourOver\FourOverApiClient;

$client = new FourOverApiClient('PUBLIC_KEY', 'PRIVATE_KEY', 'SANDBOX');

$paymentProfiles = $client->paymentProfiles->getPaymentProfiles();

print_r(
    $paymentProfiles
);