<?php

namespace FourOver\Services;

use FourOver\Entities\PaymentProfile\PaymentProfile;
use FourOver\Entities\PaymentProfile\PaymentProfileList;

class PaymentProfileService extends AbstractService
{
    /**
     * https://api-users.4over.com/?page_id=131
     *
     * @return PaymentProfileList
     */
    public function getPaymentProfiles()
    {
        return $this->getResource('GET', '/paymentprofiles', [], PaymentProfile::class, PaymentProfileList::class);
    }
}