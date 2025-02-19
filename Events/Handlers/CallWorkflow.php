<?php

namespace Modules\Iaccounting\Events\Handlers;


use Modules\Core\Icrud\Transformers\CrudResource;
use Modules\Iaccounting\Entities\Status;

class CallWorkflow
{
  public function handle($event = null)
  {
    if(!isset($event)) return null;
    $entity = $event->entity;
    if(!isset($entity)) return null;

    $attributes = $entity["data"];
    $model = $entity["model"];

    $attributes["number_invoice"] = $model->id;

    $providerId = $attributes["provider_id"];

    $providerRepository = app("Modules\Iaccounting\Repositories\ProviderRepository");
    $originRepository = app("Modules\Iaccounting\Repositories\OriginRepository");
    $origins = $originRepository->getItemsBy(json_decode(json_encode(['filter' => []])));

    $origin = $origins[0] ?? null;
    $error = [
      'code' => null,
      'message' => null
    ];

    if(!isset($origin)) {
      $updateData = ['status_id' => Status::FAILED];
      $error = [
        'code' => 500,
        'message' => 'Origin not found'
      ];
    }

    if($origin) {
      $attributes["origin"] = CrudResource::transformData($origin);

      $params = ['include' => 'city'];

      $provider = $providerRepository->getItem($providerId, $params);

      if ($provider) {
        $attributes["provider"] = $provider;
        $attributes["city"] = $provider->city;
        $attributes["typeName"] = $provider->typeName;
        $attributes["kindPersonName"] = $provider->kindPersonName;
      }

      $service = app("Modules\Iaccounting\Services\WebhookService");
      $httpResponse = $service->dispatchWebhook(['attributes' => $attributes], ['extra_url' => '/accounting/purchases']);
      $status = $httpResponse['code'];
      $data = $httpResponse['response'];
      if(isset($data->error) && isset($data->error->message)) {
        //Decode error
        $errorString = $data->error->message;
        $jsonPart = substr($errorString, strpos($errorString, '{'), strrpos($errorString, '"') - strpos($errorString, '{'));
        $jsonDecoded = stripslashes($jsonPart);
        $errorObject = json_decode($jsonDecoded);
        $errorsMsg = $errorObject->errors ?? $errorObject->Errors ?? [];
        $firstError = $errorsMsg[0];
        $error = [
          'code' => $firstError->code ?? $firstError->Code ?? '',
          'message' => $firstError->message ?? $firstError->Message ?? '',
          'params' => $firstError->params ?? $firstError->Params ?? '',
          'detail' => $firstError->detail ?? $firstError->Detail ?? ''
        ];
      } elseif (isset($data->errors)) {
        $error = [
          'code' => 500,
          'message' => 'Error not found'
        ];
      }

      $updateData = ['status_id' => Status::FAILED];

      if(isset($data->id)) {
        $updateData = ['status_id' => Status::SENDING];
      }
    }
    $updateData['options'] = array_merge($model->options ?? [], ['error' => json_decode(json_encode($error))]);
    $model->update((array)$updateData);
  }
}
