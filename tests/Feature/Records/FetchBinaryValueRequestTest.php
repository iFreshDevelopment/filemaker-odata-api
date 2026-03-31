<?php

it('fetches a binary field value', function () {
    $response = $this->connector
        ->records('Assignees')
        ->fetchBinaryValue('BEDEBAFC-9F16-40B8-8118-D56D9ABB91D1', 'Photo');

    expect($response)->toBe('fake-binary-image-content');
});
