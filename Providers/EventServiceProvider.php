<?php

namespace Modules\Iaccounting\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Iaccounting\Events\Handlers\CallWorkflow;
use Modules\Iaccounting\Events\PurchaseWasCreated;
use Modules\Iaccounting\Events\PurchaseWasUpdated;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    ];

    public function register()
    {
      Event::listen(
        PurchaseWasCreated::class,
        [CallWorkflow::class, 'handle']
      );
      Event::listen(
        PurchaseWasUpdated::class,
        [CallWorkflow::class, 'handle']
      );
    }
}
