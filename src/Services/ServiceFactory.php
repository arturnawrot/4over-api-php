<?php

namespace FourOver\Services;

use FourOver\Entities\File\FileCreatedResponse;

class ServiceFactory
{
    /**
     * @var array
     */
    private static $classMap = [
        'products' => ProductService::class,
        'categories' => CategoryService::class,
        'shipping' => ShippingService::class,
        'orders' => OrderService::class,
        'files' => FileService::class
    ];

    /**
     * @param string $name
     * 
     * @return string|null
     */
    public function getServiceClass(string $name) : string
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}