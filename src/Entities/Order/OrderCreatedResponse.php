<?php

// @TODO Due to limited time I did not finish exact schemas so I put arrays instead.

namespace FourOver\Entities\Order;

use FourOver\Entities\BaseEntity;

class OrderCreatedResponse extends BaseEntity
{
   public string $order_status;

   public ?string $customer_order_id;

   public array $job_ids;

   public ?array $payment_response;

   public ?string $payment_type;

   public ?array $errors;

   /**
    * If you submitted only one type of an item (it may have multiple sets but the type is the same) then
    * you will get only one Job UUID in the response, so you should be able to use this method safely
    * to retrieve Job UUID.
    *
    * @return string
    */
   public function getFirstJobUuid() : string
   {
      return array_key_first($this->job_ids);
   }
}