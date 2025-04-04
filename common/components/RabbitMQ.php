<?php

namespace common\components;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;

class RabbitMQ extends Component
{
    public $host;
    public $port;
    public $user;
    public $password;
    public $vhost;
    private $_connection;
    private $_channel;

    public function init()
    {
        parent::init();
        $this->_connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vhost
        );
        $this->_channel = $this->_connection->channel();
    }

    public function publish($queue, $message)
    {
        $this->_channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->_channel->basic_publish($msg, '', $queue);
    }

    public function consume($queue, $callback, $processAll = false)
    {
        $this->_channel->queue_declare($queue, false, true, false, false);

        if ($processAll) {
            // Получаем количество сообщений в очереди
            $queueInfo = $this->_channel->queue_declare($queue, false, true, false, false, false);
            $messageCount = $queueInfo[1];

            if ($messageCount > 0) {
                $this->_channel->basic_consume($queue, '', false, true, false, false, function ($msg) use ($callback, &$messageCount) {
                    call_user_func($callback, $msg->body);
                    $messageCount--;
                    if ($messageCount <= 0) {
                        $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
                    }
                });

                while ($messageCount > 0 && $this->_channel->is_consuming()) {
                    $this->_channel->wait();
                }
            }
        } else {
            // Оригинальное поведение
            $this->_channel->basic_consume($queue, '', false, true, false, false, function ($msg) use ($callback) {
                call_user_func($callback, $msg->body);
            });
            while ($this->_channel->is_consuming()) {
                $this->_channel->wait();
            }
        }
    }

    public function __destruct()
    {
        $this->_channel->close();
        $this->_connection->close();
    }
}