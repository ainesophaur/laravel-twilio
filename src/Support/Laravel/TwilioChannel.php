<?php

namespace Aloha\Twilio\Support\Laravel;

use Aloha\Twilio\TwilioInterface;
use Illuminate\Notifications\Notification;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Api;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioChannel
{

    /**
     * @var TwilioInterface
     */
    protected $twilio;
    /**
     * @var array
     */
    protected $settings;

    public function __construct(
         TwilioInterface $twilio,
        array $settings,
         LoggerInterface $logger
    )
    {
        $this->twilio = $twilio;
        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return MessageInstance|null
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $notifiable->routeNotificationFor('twilio', $notification) || ! $notification instanceof TwilioNotifiable) {
            return null;
        }

        $message = $notification->toTwilio($notifiable);

        $to = $notifiable->routeNotificationFor('twilio', $notification) ?: $message->getTo();

        assert(!empty($to), 'cannot send twilio notification to empty recipient');

        $message->usingClient($this->twilio);
        $params = $message->getParameters();
        $connection = $this->settings['connections'][$this->settings['default']];
        if ($connection['sid'] === 'log') {
            return tap(new MessageInstance(new V2010(new Api(new Client('nonsense', 'nonsense'))), $params, '')
                , function () use ($params) {
                    $this->logger->info($params['body']);
                });
        }

        return tap($this->twilio->message(
            $to,
            $params['body'],
            [],
            $params
        ), function($sent) use ($message) {
            $message->setSentMessage($sent);
        });


    }
}