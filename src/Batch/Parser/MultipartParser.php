<?php

namespace IFresh\FileMakerODataApi\Batch\Parser;

class MultipartParser
{
    /**
     * Split multipart body into sections based on boundary
     */
    public function split(string $raw, string $boundary): array
    {
        $delimiter = '--' . $boundary;

        $parts = explode($delimiter, $raw);

        $clean = [];

        foreach ($parts as $part) {
            $part = trim($part);

            // Skip empty and closing boundary
            if ($part === '' || $part === '--') {
                continue;
            }

            $clean[] = $part;
        }

        return $clean;
    }
}