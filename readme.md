# FileMaker OData API Client (ifresh/filemaker-odata-api)

Lightweight PHP client for interacting with FileMaker's OData endpoint using Saloon 4.

## Overview

- Connector: [`IFresh\FileMakerODataApi\FileMakerODataConnector`](src/FileMakerODataConnector.php) — builds the base URL, provides authentication and resources.
- Resources:
  - [`IFresh\FileMakerODataApi\Resources\Resources\MetadataResource`](src/Resources/Resources/MetadataResource.php) — metadata endpoints.
  - [`IFresh\FileMakerODataApi\Resources\Resources\RecordsResource`](src/Resources/Resources/RecordsResource.php) — CRUD operations and fluent querying for entity sets.
- Request classes live under `src/Requests` (metadata and records). Examples:
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseNamesRequest`](src/Requests/Metadata/GetDatabaseNamesRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetTableListRequest`](src/Requests/Metadata/GetTableListRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseMetadataRequest`](src/Requests/Metadata/GetDatabaseMetadataRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\CreateRecordRequest`](src/Requests/Records/CreateRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\UpdateRecordRequest`](src/Requests/Records/UpdateRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\DeleteRecordRequest`](src/Requests/Records/DeleteRecordRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\FetchRecordsRequest`](src/Requests/Records/FetchRecordsRequest.php)
  - [`IFresh\FileMakerODataApi\Requests\Records\FetchSingleRecordRequest`](src/Requests/Records/FetchSingleRecordRequest.php)
- Fluent query builder: [`IFresh\FileMakerODataApi\RecordsQueryBuilder`](src/RecordsQueryBuilder.php)
- Low-level query helper: [`IFresh\FileMakerODataApi\QueryOptions`](src/QueryOptions.php)
- Custom pending request for FileMaker-safe query encoding: [`IFresh\FileMakerODataApi\Requests\PendingFileMakerRequest`](src/Requests/PendingFileMakerRequest.php)

## Installation

```sh
composer require ifresh/filemaker-odata-api:^2.0
```

Copy the environment template and set values:

- [.env.example](.env.example)
  - FM_HOST
  - FM_USERNAME
  - FM_PASSWORD
  - FM_DATABASE (optional, but useful as a default database)

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
$tables = $connector->metadata()->getTablesList('Tasks');
$metadata = $connector->metadata()->getDatabaseMetadata('Tasks');

// Records
$recordsResource = $connector->records('Assignees');
$new = $recordsResource->createRecord(['First Name' => 'John', 'Last Name' => 'Doe']);
$single = $recordsResource->fetchSingleRecord($new['PrimaryKey']);
$all = $recordsResource->fetchRecords();
$deleted = $recordsResource->deleteRecord($new['PrimaryKey']);
```

See [`src/FileMakerODataConnector.php`](src/FileMakerODataConnector.php) and resources for more methods.

## Fluent Querying

For most record filtering, you can use the fluent query builder on `records(...)`.

```php
$records = $connector
    ->records('Assignees')
    ->where('Active', true)
    ->where(fn ($query) => $query
        ->where('First Name', 'Jane')
        ->orWhereContains('Email', '@company.com'))
    ->orderBy('First Name')
    ->select('First Name', 'Last Name', 'Email')
    ->limit(10)
    ->offset(0)
    ->get();

$count = $connector
    ->records('Assignees')
    ->where('Last Name', 'Doe')
    ->count();

$first = $connector
    ->records('Assignees')
    ->orderBy('First Name')
    ->first();
```

Supported fluent methods include:

- `where(...)`
- `orWhere(...)`
- `whereRaw(...)`
- `orWhereRaw(...)`
- `whereStartsWith(...)`
- `orWhereStartsWith(...)`
- `whereContains(...)`
- `orWhereContains(...)`
- `orderBy(...)`
- `select(...)`
- `limit(...)`
- `offset(...)`
- `get()`
- `count()`
- `first()`

### Value Quoting

The builder distinguishes between numeric and string values based on the PHP type you pass:

```php
->where('c_1', 1)   // numeric: c_1 eq 1
->where('c_1', '1') // string:  c_1 eq '1'
```

This matters because FileMaker can expose numeric-looking values in text fields.

### Nested Groups

Nested where groups work like Eloquent:

```php
$records = $connector
    ->records('Assignees')
    ->where(function ($query) {
        $query->where('name', 'test')
            ->where('c_1', 1);
    })
    ->orWhere(function ($query) {
        $query->where('uuid', '6B8634F2-2C3C-48A8-806B-3788AA7B2E55');
    })
    ->get();
```

### Field Names

Use the actual field names that FileMaker exposes through OData.

- A field like `First Name` is valid.
- A FileMaker field ID like `c_1` is valid.
- Generic names like `id` may not exist unless the OData metadata actually exposes them.

To inspect the available fields, use:

```php
$metadata = $connector->metadata()->getDatabaseMetadata('Tasks');
```

## Low-Level Query Options

If you need to construct OData options manually, use [`IFresh\FileMakerODataApi\QueryOptions`](src/QueryOptions.php):

```php
use IFresh\FileMakerODataApi\QueryOptions;

$records = $connector
    ->records('Assignees')
    ->fetchRecords(new QueryOptions(
        filter: "contains(Email,'@company.com')",
        sort: '"First Name" asc',
        limit: 10,
        offset: 0,
        withCount: true,
        select: '"First Name",Email',
    ));
```

## FileMaker Notes

- Query values are encoded centrally through [`PendingFileMakerRequest`](src/Requests/PendingFileMakerRequest.php) using RFC 3986 encoding, which is important for FileMaker OData filters.
- Field names containing spaces or underscores are automatically wrapped in double quotes by the fluent builder.
- `records('Assignees')` uses the default database from the connector constructor.

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
- The mocked test suite does not require a local `.env`, but if present it will be loaded by [tests/TestCase.php](tests/TestCase.php).

## License

See composer.json for package metadata: [composer.json](composer.json)
