<?php

namespace console\controllers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;

class RabbitMQRufat extends Component
{
    public string $host;
    public string $port;
    public string $user;
    public string $password;
    public string $vhost;


    public $connection;
    public $channel;

    public function init() {
        parent::init();
        $this->connection = new AMQPStreamConnection(
             $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vhost
        );

        $this->channel = $this->connection->channel();
    }


    public function publishFileData($queue, $fileData) {
        $this->channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage(
            json_encode($fileData),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        $this->channel->basic_publish($msg, '', $queue);
    }

    public function consume($queue, $callback) {
        //объявляем очередь
        $queueInfo = $this->channel->queue_declare($queue, false, true, false, false);
        $countMessage = $queueInfo[1];

        if ($countMessage == 0) {
            return false;
        }

        $this->channel->basic_consume($queue, '', false, true, false, false,
            function ($msg) use($callback, $countMessage) {
                call_user_func($callback, $msg->body);
                $countMessage--;
                if ($countMessage == 0){
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }
            }
        );

        while ($countMessage > 0 && $this->channel->is_consuming()) {
            $this->channel->wait();
        }
        return true;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }


}