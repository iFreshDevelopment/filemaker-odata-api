<?php

namespace Tests;

use Dotenv\Dotenv;
use IFresh\FileMakerODataApi\FileMakerODataConnector;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public FileMakerODataConnector $connector;

    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(__DIR__.'/..');
        $dotenv->load();

        $connector = new FileMakerODataConnector(
            host: $_ENV['FM_HOST'],
            username: $_ENV['FM_USERNAME'],
            password: $_ENV['FM_PASSWORD'],
        );

        $connector->withMockClient(\mockClient());

        $this->connector = $connector;
    }
}
