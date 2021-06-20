<?php

namespace App\Console\Commands;

use App\Services\DataSetService;
use App\Services\SendMessageService;
use App\Subscription;
use App\Vehicle;
use Illuminate\Console\Command;
use JsonMachine\JsonMachine;

class DataSetCommand extends Command
{
    /**
     * Максимальное количестов элементов для добавления в БД
     */
    private const ITEMS_LIMIT = 1000;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataset:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузить данные из сервиса data.gov.ua';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file    = DataSetService::getInstance()->getUrlDowloadData();
        $records = JsonMachine::fromFile($file);
        $data    = $vehicleNumbers = $bodyNumbers = $engineNumbers = [];

        foreach ($records as $record) {
            $vehicleNumbers[] = $record['VEHICLENUMBER'];
            $bodyNumbers[]    = $record['BODYNUMBER'];
            $engineNumbers[]  = $record['ENGINENUMBER'];

            $data[] = [
                'id'             => $record['ID'],
                'ovd'            => $record['OVD'],
                'brand'          => $record['BRAND'],
                'color'          => $record['COLOR'],
                'vehicle_number' => $record['VEHICLENUMBER'],
                'body_number'    => $record['BODYNUMBER'],
                'chassis_number' => $record['CHASSISNUMBER'],
                'engine_number'  => $record['ENGINENUMBER'],
                'theft_data'     => $record['THEFT_DATA'],
                'insert_data'    => $record['INSERT_DATE'],
            ];

            if (count($data) > self::ITEMS_LIMIT) {
                $this->saveData($data);
                $this->sendMessage($vehicleNumbers, $bodyNumbers, $engineNumbers);

                $data = $vehicleNumbers = $bodyNumbers = $engineNumbers = [];
            }
        }

        $this->saveData($data);
        $this->sendMessage($vehicleNumbers, $bodyNumbers, $engineNumbers);
    }

    private function sendMessage(array $vehicleNumbers, array $bodyNumbers, array $engineNumbers): void
    {
        $subscriptions = Subscription::findByNumbers($vehicleNumbers, $bodyNumbers, $engineNumbers);

        SendMessageService::getInstance()->send($subscriptions);
    }

    /**
     * @param array $data
     */
    private function saveData(array $data): void
    {
        Vehicle::insertOnDuplicateKey($data);
    }
}
