<?php

it('returns the database metadata information', function () {
    $response = $this->connector
        ->metadata()
        ->getDatabaseMetadata('Tasks');

    expect($response)
        ->toBeArray()
        ->toHaveKeys([
            '$Version',
            'Tasks',
        ]);
});
