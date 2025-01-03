<?php

it('can create a record', function () {
    $response = $this->connector
        ->records('Tasks', 'Assignees')
        ->createRecord([
            'First Name' => 'John',
            'Last Name' => 'Doe',
            'Email' => 'j.doe@company.com',
        ]);

    expect($response)
        ->toBeArray()
        ->toMatchArray([
            'First Name' => 'John',
            'Last Name' => 'Doe',
            'Email' => 'j.doe@company.com',
        ])
        ->toHaveKeys([
            '@id',
            'PrimaryKey',
        ]);
});
