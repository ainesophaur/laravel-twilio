<?php

namespace Aloha\Twilio\Support\Laravel;

use Aloha\Twilio\TwilioInterface;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\TwiML\TwiML;

class TwilioMessage
{
    /**
     * @var string $to
     */
    protected $to;
    /**
     * The body of the message to send
     * @var callable|TwiML|string $body
     */
    protected $body;

    protected $with;

    /**
     * @var ?MessageInstance $message
     */
    protected $message;
    /**
     * @var ?TwilioInterface
     */
    protected $client;
    /**
     * @var ?string $from
     */
    private $from;

    public function __construct(
        $body = '',
        $to = null
    )
    {
        $this->body = $body;
        $this->to = $to;
        $this->from = null;
        $this->with = [];
        $this->message = null;
        $this->client = null;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return is_callable($this->body) ? (string)call_user_func($this->body, $this->message) : (string)$this->body;
    }

    /**
     * @return null
     */
    public function getFrom()
    {
        return $this->from;
    }



    /**
     * @return array<string, scalar|array<string, scalar>>
     */
    public function getWith(): array
    {
        return $this->with;
    }

    public function getSentMessage(): ?MessageInstance
    {
        return $this->message;
    }

    public function setSentMessage(?MessageInstance $message): void
    {
        $this->message = $message;
    }



    public function usingClient(TwilioInterface $twilio): self
    {
        $this->client = $twilio;
        return $this;
    }

    /**
     * @param mixed|string $to
     */
    public function to($to): self
    {
        assert(!empty($to), 'twilio recipient cannot be empty');
        $this->to = $to;
        return $this;
    }



    public function content($content): self
    {
        $this->body = $content;
        return $this;
    }

    public function with(array $with): self
    {
        $this->with = $with;
        return $this;
    }


    /**
     * @return array{ body: string, from: string }
     */
    public function getParameters()
    {
        return array_merge($this->with, [
            'body' => $this->getBody(),
            'from' => $this->from,
        ]);
    }
}