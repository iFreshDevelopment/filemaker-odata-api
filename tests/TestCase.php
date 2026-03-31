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

        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__.'/..');
            $dotenv->load();
        }

        $connector = new FileMakerODataConnector(
            host: $_ENV['FM_HOST'] ?? 'https://example.com',
            username: $_ENV['FM_USERNAME'] ?? 'username',
            password: $_ENV['FM_PASSWORD'] ?? 'password',
            database: $_ENV['FM_DATABASE'] ?? 'Tasks',
        );

        $connector->withMockClient(\mockClient());

        $this->connector = $connector;
    }
}
