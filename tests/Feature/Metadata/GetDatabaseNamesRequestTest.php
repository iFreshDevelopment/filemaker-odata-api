<?php

it('returns a list of databases', function () {
    $response = $this->connector
        ->metadata()
        ->getDatabaseNames();

    expect($response)
        ->toBeArray()
        ->toHaveCount(1)
        ->toContain('Tasks');
});
