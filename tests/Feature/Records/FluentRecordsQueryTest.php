<?php

use IFresh\FileMakerODataApi\RecordsQueryBuilder;

it('supports fluent record queries', function () {
    $response = $this->connector
        ->records('Assignees')
        ->where('First Name', 'Jane')
        ->orWhereStartsWith('Title', 'Admin')
        ->orWhereContains('Email', '@company.com')
        ->orderBy('First Name', 'desc')
        ->select('First Name', 'Last Name', 'Email')
        ->limit(2)
        ->offset(0)
        ->get();

    expect($response)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($response[0])->toMatchArray([
            'First Name' => 'Jane',
            'Last Name' => 'Doe',
        ]);
});

it('supports fluent count queries', function () {
    $response = $this->connector
        ->records('Assignees')
        ->where('Last Name', 'Doe')
        ->count();

    expect($response)->toBe(2);
});

it('supports fetching the first record with the fluent query builder', function () {
    $response = $this->connector
        ->records('Assignees')
        ->orderBy('First Name')
        ->first();

    expect($response)
        ->toBeArray()
        ->toMatchArray([
            'First Name' => 'Jane',
            'Last Name' => 'Doe',
        ]);
});

it('supports nested fluent where groups', function () {
    $response = $this->connector
        ->records('Assignees')
        ->where(fn (RecordsQueryBuilder $query) => $query
            ->where('First Name', 'Jane')
            ->orWhereContains('Email', '@company.com'))
        ->get();

    expect($response)
        ->toBeArray()
        ->toHaveCount(2);
});
