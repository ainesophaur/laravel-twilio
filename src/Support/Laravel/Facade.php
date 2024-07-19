<?php

namespace Aloha\Twilio\Support\Laravel;

use Aloha\Twilio\Manager;
use Aloha\Twilio\TwilioInterface;
use Illuminate\Support\Facades\Facade as BaseFacade;
use Aloha\Twilio\Support\FakeFacade;
use Aloha\Twilio\Dummy;

class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'twilio';
    }
    
    public static function fake(): FakeFacade
    {
        return tap(new FakeFacade(static::getFacadeRoot()), function($fake) {
            static::swap($fake);

            static::$resolvedInstance[Manager::class] = $fake;

            if (isset(static::$app)) {
                static::$app->instance(TwilioInterface::class, $fake);
            }
        });
    }
}
