<?php

it('updates a record', function () {
    $response = $this->connector
        ->records('Tasks', 'Assignees')
        ->updateRecord(
            'BEDEBAFC-9F16-40B8-8118-D56D9ABB91D1',
            [
                'First Name' => 'Jane',
                'Last Name' => 'Doe',
            ]
        );
    expect($response)
        ->toBeArray()
        ->toMatchArray([
            'First Name' => 'Jane',
            'Last Name' => 'Doe',
        ])
        ->toHaveKeys([
            '@id',
            'PrimaryKey',
        ]);
});
