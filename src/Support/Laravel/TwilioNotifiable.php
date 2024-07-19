<?php

namespace Aloha\Twilio\Support\Laravel;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

interface TwilioNotifiable
{
    public function toTwilio(Notification $notification): TwilioMessage;
}