<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

final class BatchBody
{
    protected array $parts = [];

    public function __construct(
        protected string $boundary
    ) {}

    public function addPart(BatchPart $part): BatchBody
    {
        $this->parts[] = $part;

        return $this;
    }

    public function addChangeSet(ChangeSet $changeSet): BatchBody
    {
        $this->parts[] = $changeSet;

        return $this;
    }

    public function toString(): string
    {
        $eol = "\r\n";
        $body = '';

        foreach ($this->parts as $part) {
            $body .= sprintf('--%s%s', $this->boundary, $eol);

            if ($part instanceof ChangeSet) {
                $body .= $part->toString() . $eol;
            } else {
                $body .= $part->toString();
            }
        }

        $body .= sprintf('--%s--%s', $this->boundary, $eol);

        var_dump($body);exit;

        return $body;
    }
}
