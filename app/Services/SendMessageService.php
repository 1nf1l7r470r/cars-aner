<?php

namespace App\Services;

use App\RecordPhone;
use App\RecordSim;
use App\Subscription;
use App\Vehicle;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Collection;

class SendMessageService
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var BotMan
     */
    private $botman;

    /**
     * @return SendMessageService
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * SendMessageService constructor.
     */
    private function __construct()
    {
        $this->botman = app('botman');
    }

    /**
     * @param Collection|Subscription[] $subscriptions
     */
    public function send(Collection $subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            $vehicle = Vehicle::findByNumber($subscription->number);

            $this->botman->say(
                self::getText($vehicle),
                $subscription->user_id,
                TelegramDriver::class
            );

            $subscription->delete();
        }
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return string
     */
    public static function getText(Vehicle $vehicle): string
    {
        $text = 'Інформація про транспорт ' . PHP_EOL . PHP_EOL;
        $text .= $vehicle->__toString() . PHP_EOL;

        return $text;
    }
}