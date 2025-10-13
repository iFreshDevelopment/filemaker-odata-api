# FileMaker OData API Client (ifresh/filemaker-odata-api)

Lightweight PHP client for interacting with FileMaker's OData endpoint using Saloon.

## Overview

- Connector: [`IFresh\FileMakerODataApi\FileMakerODataConnector`](src/FileMakerODataConnector.php) — builds the base URL, provides authentication and resources.
- Resources:
  - [`IFresh\FileMakerODataApi\Resources\Resources\MetadataResource`](src/Resources/Resources/MetadataResource.php) — metadata endpoints.
  - [`IFresh\FileMakerODataApi\Resources\Resources\RecordsResource`](src/Resources/Resources/RecordsResource.php) — CRUD operations for entity sets.
- Request classes live under `src/Requests` (metadata and records). Examples:
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseNamesRequest`](src/Requests/Metadata/GetDatabaseNamesRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetTableListRequest`](src/Requests/Metadata/GetTableListRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseMetadataRequest`](src/Requests/Metadata/GetDatabaseMetadataRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\CreateRecordRequest`](src/Requests/Records/CreateRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\UpdateRecordRequest`](src/Requests/Records/UpdateRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\DeleteRecordRequest`](src/Requests/Records/DeleteRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\FetchRecordsRequest`](src/Requests/Records/FetchRecordsRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\FetchSingleRecordRequest`](src/Requests/Records/FetchSingleRecordRequest.php)
- Query helpers: [`IFresh\FileMakerODataApi\QueryOptions`](src/QueryOptions.php)
- Custom pending request to merge query params: [`IFresh\FileMakerODataApi\Requests\PendingFileMakerRequest`](src/Requests/PendingFileMakerRequest.php)

## Installation

```sh
composer require ifresh/filemaker-odata-api
```

Copy environment template and set values:

- [.env.example](.env.example)
  - FM_HOST
  - FM_USERNAME
  - FM_PASSWORD

## Basic Usage

```php
use IFresh\FileMakerODataApi\FileMakerODataConnector;

$connector = new FileMakerODataConnector(
    host: 'https://example.com',
    username: 'user',
    password: 'pass',
    database: 'Tasks' // optional default database
);

// Metadata
$databases = $connector->metadata()->getDatabaseNames();

// Records
$recordsResource = $connector->records('Assignees');
$new = $recordsResource->createRecord(['First Name' => 'John', 'Last Name' => 'Doe']);
$single = $recordsResource->fetchSingleRecord($new['PrimaryKey']);
```

See [`src/FileMakerODataConnector.php`](src/FileMakerODataConnector.php) and resources for more methods.

## Querying

Use [`IFresh\FileMakerODataApi\QueryOptions`](src/QueryOptions.php) to supply OData query options (filter, orderby, top, skip, select, count).

## Tests

This package is tested with Pest. See the test bootstrap and helpers:

- [tests/Pest.php](tests/Pest.php)
- [tests/TestCase.php](tests/TestCase.php)
- Fixtures for faking HTTP responses: [tests/Fixtures/ODataFixture.php](tests/Fixtures/ODataFixture.php) and JSON fixtures in [tests/Fixtures/Saloon](tests/Fixtures/Saloon)

Run tests:

```sh
composer install --dev
./vendor/bin/pest
```

## Notes for Contributors

- Coding standards via Laravel Pint (dev).
- Static analysis: [phpstan.neon](phpstan.neon) (level 6).
- The connector uses Saloon's `BasicAuthenticator` by default (see test: [tests/Feature/FileMakerODataConnectorTest.php](tests/Feature/FileMakerODataConnectorTest.php)).

## License

See composer.json for package metadata: [composer.json](composer.json)