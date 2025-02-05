<?php

namespace FourOver\Services;

use FourOver\Entities\Order\OrderCreatedResponse;
use FourOver\Entities\Order\StatusList;
use FourOver\Entities\Order\Status;
use FourOver\Entities\Order\Tracking;

class OrderService extends AbstractService
{
    /**
     * @todo Pass DTO objects as parameters instead of "raw" arrays and strings
     * https://api-users.4over.com/?page_id=129
     *
     * @return BaseList
     */
    public function createOrder(
        string $order_id,
        $coupon_code,
        bool $skip_conformation,
        int $sets,
        string $product_uuid,
        string $runsize_uuid,
        string $turnaroundtime_uuid,
        string $colorspec_uuid ,
        array $option_uuids,
        bool $dropship,
        array $files, // see their docs for examples. @todo objects instead of raw arrays
        $ship_to_company,
        string $ship_to_firstname,
        string $ship_to_lastname,
        string $ship_to_email,
        string $ship_to_phone,
        string $ship_to_address,
        string $ship_to_address2,
        string $ship_to_city,
        string $ship_to_state,
        string $ship_to_zipcode,
        string $ship_to_country,
        $ship_from_company,
        string $ship_from_firstname,
        string $ship_from_lastname,
        string $ship_from_email,
        string $ship_from_phone,
        string $ship_from_address,
        string $ship_from_address2,
        string $ship_from_city,
        string $ship_from_state,
        string $ship_from_zipcode,
        string $ship_from_country,
        string $shipping_method,
        string $shipping_code,
        ?string $profile_token = null
    ) : OrderCreatedResponse
    {
        $postBodyData = [
            'order_id' => $order_id,
            'is_test_order' => $this->isSandboxMode(),
            'coupon_code' => $coupon_code,
            'skip_confirmation' => $skip_conformation,
            'jobs' => [
                [
                    'product_uuid' => $product_uuid,
                    'runsize_uuid' => $runsize_uuid,
                    'option_uuids' => $option_uuids,
                    'turnaroundtime_uuid' => $turnaroundtime_uuid,
                    'colorspec_uuid' => $colorspec_uuid,
                    'dropship' => $dropship,
                    'sets' => $sets,
                    'files' => $files,
                    'ship_to' => [
                        'company' => $ship_to_company,
                        'firstname' => $ship_to_firstname,
                        'lastname' => $ship_to_lastname,
                        'email' => $ship_to_email,
                        'phone' => $ship_to_phone,
                        'address' => $ship_to_address,
                        'address2' => $ship_to_address2 ? $ship_to_address2 : '',
                        'city' => $ship_to_city,
                        'state' => $ship_to_state,
                        'zipcode' => $ship_to_zipcode,
                        'country' => $ship_to_country
                    ],
                    'ship_from' => [
                        'company' => $ship_from_company,
                        'firstname' => $ship_from_firstname,
                        'lastname' => $ship_from_lastname,
                        'email' => $ship_from_email,
                        'phone' => $ship_from_phone,
                        'address' => $ship_from_address,
                        'address2' => $ship_from_address2 ? $ship_from_address2 : '',
                        'city' => $ship_from_city,
                        'state' => $ship_from_state,
                        'zipcode' => $ship_from_zipcode,
                        'country' => $ship_from_country
                    ],
                    'shipper' => [
                        'shipping_method' => $shipping_method,
                        'shipping_code' => $shipping_code
                    ]
                ]
            ]
        ];

        if($this->isSandboxMode() and $profile_token === null) {
            $profile_token = '1010101010'; // Default dummy token is 1010101010
        }

        if($profile_token !== null) {
            $postBodyData['payment'] = [
                'profile_token' => $profile_token
            ];
        }

        return $this->getResource(
            'POST',
            '/orders',
            ['body' => json_encode($postBodyData)], 
            OrderCreatedResponse::class
        );
    }

    /**
     * https://api-users.4over.com/?page_id=98
     * @param $jobUuid
     *
     * @return StatusList
     */
    public function getOrderStatus(string $jobUuid) : StatusList
    {
        return $this->getResource('GET', "/orders/{$jobUuid}/status", [], Status::class, StatusList::class);
    }

    /**
     * https://api-users.4over.com/?page_id=135
     * @param $jobUuid
     *
     * @return Tracking
     */
    public function getTrackingNumber(string $jobUuid) : Tracking
    {
        return $this->getResource('GET', "/orders/{$jobUuid}/tracking", [], Tracking::class);
    }
}