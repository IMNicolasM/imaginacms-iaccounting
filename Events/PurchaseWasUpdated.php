<?php

namespace Modules\Iaccounting\Events;

class PurchaseWasUpdated
{

  public $entity;

  public function __construct($providerValue)
  {
    $this->entity = $providerValue;
  }

}
