<?php

namespace IFresh\FileMakerODataApi\Batch\Parser;

class MultipartParser
{
    public function split(string $raw, string $boundary): array
    {
        $delimiter = '--' . $boundary;

        $parts = explode($delimiter, $raw);

        $clean = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '' || $part === '--') {
                continue;
            }

            $clean[] = $part;
        }

        return $clean;
    }
}
