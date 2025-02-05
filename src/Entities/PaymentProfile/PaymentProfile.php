<?php

namespace FourOver\Entities\PaymentProfile;

use FourOver\Entities\BaseEntity;

class PaymentProfile extends BaseEntity 
{
    private string $profile_token;

    private string $last_four;

    private string $type;

    public function getProfileToken() : string
    {
        return $this->profile_token;
    }

    public function getLastFour() : string
    {
        return $this->last_four;
    }

    public function getType() : string
    {
        return $this->type;
    }
}