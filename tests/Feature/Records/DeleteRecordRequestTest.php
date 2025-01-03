<?php

it('deletes a record', function () {
    $response = $this->connector
        ->records('Tasks', 'Assignees')
        ->deleteRecord('4FFB6DCE-6EC0-46D9-ACB5-21E02CA74806');

    expect($response->successful())
        ->toBeTrue();
});
