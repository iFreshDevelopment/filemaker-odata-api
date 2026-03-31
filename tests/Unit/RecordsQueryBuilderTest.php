<?php

use IFresh\FileMakerODataApi\FileMakerODataConnector;
use IFresh\FileMakerODataApi\RecordsQueryBuilder;
use IFresh\FileMakerODataApi\Resources\Resources\RecordsResource;

it('compiles fluent query builder clauses into query options', function () {
    $resource = new RecordsResource(
        connector: new FileMakerODataConnector(
            host: 'https://example.com',
            username: 'user',
            password: 'pass',
            database: 'Tasks',
        ),
        database: 'Tasks',
        table: 'Assignees',
    );

    $queryOptions = $resource
        ->where('First Name', 'Jane')
        ->orWhere('Last_Name', '!=', 'Doe')
        ->orWhereStartsWith('Title', 'Admin')
        ->orWhereContains('Email', '@company.com')
        ->orderBy('First Name', 'desc')
        ->select('First Name', 'Last_Name', 'Products/Website')
        ->limit(10)
        ->offset(5)
        ->withCount()
        ->toQueryOptions();

    expect($queryOptions->toArray())->toBe([
        '$filter' => '"First Name" eq \'Jane\' or "Last_Name" ne \'Doe\' or startswith(Title,\'Admin\') or contains(Email,\'@company.com\')',
        '$orderby' => '"First Name" desc',
        '$top' => 10,
        '$skip' => 5,
        '$count' => 'true',
        '$select' => '"First Name","Last_Name",Products/Website',
    ]);
});

it('supports chaining multiple where clauses', function () {
    $resource = new RecordsResource(
        connector: new FileMakerODataConnector(
            host: 'https://example.com',
            username: 'user',
            password: 'pass',
            database: 'Tasks',
        ),
        database: 'Tasks',
        table: 'Assignees',
    );

    $queryOptions = $resource
        ->where('name', 'test')
        ->where('id', '1')
        ->toQueryOptions();

    expect($queryOptions->toArray())->toBe([
        '$filter' => "name eq 'test' and id eq '1'",
    ]);
});

it('does not quote numeric filter values when they are passed as integers', function () {
    $resource = new RecordsResource(
        connector: new FileMakerODataConnector(
            host: 'https://example.com',
            username: 'user',
            password: 'pass',
            database: 'Tasks',
        ),
        database: 'Tasks',
        table: 'Assignees',
    );

    $queryOptions = $resource
        ->where('id', 1)
        ->toQueryOptions();

    expect($queryOptions->toArray())->toBe([
        '$filter' => 'id eq 1',
    ]);
});

it('supports nested where groups with closures', function () {
    $resource = new RecordsResource(
        connector: new FileMakerODataConnector(
            host: 'https://example.com',
            username: 'user',
            password: 'pass',
            database: 'Tasks',
        ),
        database: 'Tasks',
        table: 'Assignees',
    );

    $queryOptions = $resource
        ->where('Active', true)
        ->where(fn (RecordsQueryBuilder $query) => $query
            ->where('First Name', 'Jane')
            ->orWhere('First Name', 'John'))
        ->toQueryOptions();

    expect($queryOptions->toArray())->toBe([
        '$filter' => 'Active eq true and ("First Name" eq \'Jane\' or "First Name" eq \'John\')',
    ]);
});
