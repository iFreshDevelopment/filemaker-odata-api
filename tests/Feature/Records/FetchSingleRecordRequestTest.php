<?php

it('fetches a single record', function () {
    $response = $this->connector
        ->records('Assignees')
        ->fetchSingleRecord('BEDEBAFC-9F16-40B8-8118-D56D9ABB91D1');

    expect($response)
        ->toBeArray()
        ->toMatchArray([
            'First Name' => 'Jane',
            'Last Name' => 'Doe',
            'Email' => 'jane.doe@company.com',
        ])
        ->toHaveKeys([
            '@id',
            'PrimaryKey',
        ]);
});
