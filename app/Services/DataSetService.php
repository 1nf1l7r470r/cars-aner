<?php

namespace App\Services;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class DataSetService
{
    public const BASE_URL = 'https://data.gov.ua';
    public const URI_DATASET_VEHICLE = '/dataset/2a746426-b289-4eb2-be8f-aac03e68948c';
    public const DOWLOAD_URL = '/download/';
    public const FILE_NAME = 'data.json';

    /** @var self */
    private static $instance;

    /** @var Client */
    private $client;

    /**
     * @var Dom
     */
    private $dom;

    private function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URL]);
        $this->dom = new Dom();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Загрузить страницу html
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    private function loadHtml(): void
    {
        $response = $this->client->request('GET', self::URI_DATASET_VEHICLE);

        $this->dom->load($response->getBody()->getContents());
    }

    /**
     * Получить URL для загрузки данных
     *
     * @return string
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getUrlDowloadData(): string
    {
        $this->loadHtml();

        $href = $this->dom->find('.resource-item', 0)->find('a', 0)->getAttribute('href');

        return self::BASE_URL . $href . self::DOWLOAD_URL . self::FILE_NAME;
    }
}