<?php

namespace FourOver\Entities\PaymentProfile;

use FourOver\Entities\BaseList;

class PaymentProfileList extends BaseList
{
    /**
     * @return string
     */
    public static function getType() : string
    {
        return PaymentProfile::class;
    }
}