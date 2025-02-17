<?php

namespace Modules\Iaccounting\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Media\Support\Traits\MediaRelation;

class Purchase extends CrudModel
{
  use MediaRelation;

  protected $table = 'iaccounting__purchases';
  public $transformer = 'Modules\Iaccounting\Transformers\PurchaseTransformer';
  public $repository = 'Modules\Iaccounting\Repositories\PurchaseRepository';
  public $requestValidation = [
      'create' => 'Modules\Iaccounting\Http\Requests\CreatePurchaseRequest',
      'update' => 'Modules\Iaccounting\Http\Requests\UpdatePurchaseRequest',
    ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [
      ['path' => 'Modules\Iaccounting\Events\PurchaseWasCreated']
    ],
    'creating' => [],
    'updated' => [
      ['path' => 'Modules\Iaccounting\Events\PurchaseWasUpdated']
    ],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];
  public $translatedAttributes = [];
  protected $fillable = [
    'document_type',
    'invoice_date',
    'total',
    'subtotal',
    'currency_code',
    'invoice_items',
    'options',
    'provider_id',
    'payment_method_id',
    'status_id'
  ];

  protected $casts = [
    'invoice_items' => 'array',
    'options' => 'array'
  ];

  public function provider()
  {
    return $this->belongsTo(Provider::class);
  }

  public function getStatusNameAttribute()
  {
    $status = new Status();
    return $status->show($this->status_id);
  }

  public function getPaymentNameAttribute()
  {
    $payment = new PaymentMethod();

    return $payment->show($this->payment_method_id);
  }

  public function getDocumentTypeNameAttribute()
  {
    $payment = new DocumentType();

    return $payment->show($this->document_type);
  }
}
