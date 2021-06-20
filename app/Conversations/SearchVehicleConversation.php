<?php

namespace App\Conversations;

use App\Services\SendMessageService;
use App\Subscription;
use App\Vehicle;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SearchVehicleConversation extends Conversation
{
    /**
     * @param string $number
     *
     * @return SearchVehicleConversation
     */
    public function askSubscription(string $number)
    {
        $question = Question::create('За цим номером не знайдено транспортний засіб в базі даних. Підписатися на повідомлення?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_subscription')
            ->addButtons([
                Button::create('Так')->value(1),
            ]);

        return $this->ask($question, function (Answer $answer) use ($number) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() == 1) {
                    $this->say('Ви успішно підписалися на номер ' . $number . '. Як тільки буде додано до бази даних, ми вас повідомимо :)');

                    Subscription::insert([
                        'user_id' => $this->bot->getUser()->getId(),
                        'number' => $number
                    ]);
                }
            }
        });
    }

    /**
     * @return SearchVehicleConversation
     */
    public function askNumber()
    {
        return $this->ask('Введіть державний номер, номер кузову або номер двигуну, який потрібно знайти', function (Answer $answer) {
            $number = $answer->getText();
            $vehicle = Vehicle::findByNumber($number);

            if (empty($vehicle)) {
                $this->askSubscription($number);
            } else {
                $this->say(SendMessageService::getText($vehicle));
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askNumber();
    }
}
