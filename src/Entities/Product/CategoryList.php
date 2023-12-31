<?php

namespace FourOver\Entities\Product;

use FourOver\Entities\BaseList;

class CategoryList extends BaseList
{
    /**
     * @return string
     */
    public static function getType() : string
    {
        return Category::class;
    }
}