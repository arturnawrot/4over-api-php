# 4over-api-php

## 4over API client for PHP

Docs: [https://api-users.4over.com/](https://api-users.4over.com/)

## Installation

```
composer require arturnawrot/4over-api-client
```
or alternatively
```
git clone https://github.com/arturnawrot/4over-api-php
composer install
```

## Example Usage
```php
<?php

require 'vendor/autoload.php';

use FourOver\FourOverApiClient;

$client = new FourOverApiClient('PUBLIC_KEY', 'PRIVATE_KEY', 'SANDBOX'); // or 'LIVE' instead of 'SANDBOX'

/** 
 * Some products don't have turnaround option group so try a different index if it fails
 * @var \FourOver\Entities\Product\Product 
 * */
$product = $client->products->getAllProducts()[12];

/** @var \FourOver\Entities\Product\OptionGroupList */
$optionGroupList = $product->getProductOptionGroups();

/** @var \FourOver\Entities\Product\OptionGroupList */
$turnaroundOptionGroup = $optionGroupList->getTurnaroundOptionGroup();

/** @var \FourOver\Entities\Product\Option */
$turnaroundOption = $turnaroundOptionGroup->getOptions()[0];

/** @var string */
$colorspecUuid = $turnaroundOption->getColorspecUuid();

/** @var string */
$turnaroundUuid = $turnaroundOption->getOptionUuid();

/** @var string */
$runsizeUuid = $turnaroundOption->getRunsizeUuid();

/** @var string */
$productUuid = $product->getProductUuid();

$address  = '4301 Washington Road.';
$address2 = '';
$city = 'Evans';
$state = 'GA';
$country  = 'US';
$zipcode  = '30809';
$sets = 20;

/** @var \FourOver\Entities\Shipping\ShippingQuote */
$shippingQuote = $client->shipping->getShippingQuote(
    $productUuid,
    $runsizeUuid,
    $turnaroundUuid,
    $colorspecUuid,
    $address,
    $address2,
    $city,
    $state,
    $country,
    $zipcode,
    $sets
);

print_r(
    $shippingQuote
);
```
## Licence
The MIT License (MIT)

Copyright (c) 2023 Artur Nawrot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
