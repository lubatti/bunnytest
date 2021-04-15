<?php
declare(strict_types=1);

namespace App\Domain\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CommunicationService
{
    private const DEFAULT_QUEUE = 'default_queue';

    private AMQPStreamConnection $streamConnection;

    public function __construct(AMQPStreamConnection $streamConnection)
    {
        $this->streamConnection = $streamConnection;
    }

    public function spreadMessage(array $data, string $queue = self::DEFAULT_QUEUE): void
    {
        $channel = $this->streamConnection->channel();

        $channel->queue_declare(self::DEFAULT_QUEUE, false, false, false, false);

        $channel->basic_publish(
            new AMQPMessage(json_encode($data)),
            '',
            self::DEFAULT_QUEUE
        );
    }
}
