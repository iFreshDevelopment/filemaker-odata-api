<?php

it('returns a list of tables for a database', function () {
    $response = $this->connector
        ->metadata()
        ->getTablesList('Tasks');

    expect($response)
        ->toBeArray()
        ->toHaveCount(4)
        ->toContain('Assignees', 'Assignments', 'Attachments', 'Tasks');
});
