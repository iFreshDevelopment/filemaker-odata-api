<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

final class ChangeSet
{
    protected array $parts = [];

    protected string $boundary;

    public function __construct(?string $boundary = null)
    {
        $this->boundary = $boundary ?? sprintf('changeset_%s', uniqid());
    }

    public function add(ChangeSetPart $part): ChangeSet
    {
        $this->parts[] = $part;

        return $this;
    }

    public function toString(): string
    {
        $eol = "\r\n";
        $body = '';

        foreach ($this->parts as $part) {
            $body .= sprintf('--%s%s', $this->boundary, $eol);
            $body .= $part->toString();
        }

        $body .= sprintf('--%s--%s', $this->boundary, $eol);

        return implode($eol, [
            sprintf('Content-Type: multipart/mixed; boundary=%s', $this->boundary),
            '',
            $body
        ]);
    }
}
