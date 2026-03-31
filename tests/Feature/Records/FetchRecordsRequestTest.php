<?php

use IFresh\FileMakerODataApi\QueryOptions;

it('fetches a list of records', function () {
    $response = $this->connector
        ->records('Assignees')
        ->fetchRecords(new QueryOptions(
            sort: 'Last Name asc',
            limit: 2,
            select: 'First Name,Last Name,Email',
        ));

    expect($response)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($response[0])->toMatchArray([
            'First Name' => 'Jane',
            'Last Name' => 'Doe',
            'Email' => 'jane.doe@company.com',
        ])
        ->and($response[1])->toMatchArray([
            'First Name' => 'John',
            'Last Name' => 'Doe',
            'Email' => 'john.doe@company.com',
        ]);
});

it('returns the record count', function () {
    $response = $this->connector
        ->records('Assignees')
        ->getRecordCount();

    expect($response)->toBe(2);
});
