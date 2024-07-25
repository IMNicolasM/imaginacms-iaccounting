<?php

namespace Modules\Iaccounting\Entities;

use Modules\Core\Icrud\Entities\CrudModel;

class Mapping extends CrudModel
{
  protected $table = 'iaccounting__mappings';
  public $transformer = 'Modules\Iaccounting\Transformers\MappingTransformer';
  public $requestValidation = [
      'create' => 'Modules\Iaccounting\Http\Requests\CreateMappingRequest',
      'update' => 'Modules\Iaccounting\Http\Requests\UpdateMappingRequest',
    ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [],
    'creating' => [],
    'updated' => [],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];
  protected $fillable = ['type', 'external_id', 'external_name', 'external_value', 'options', 'apikey_id'];

  protected $casts = [
    'options' => 'array'
  ];

  public function apiKey()
  {
    return $this->belongsTo(ApiKeys::class);
  }
}
