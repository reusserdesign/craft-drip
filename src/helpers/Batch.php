<?php

namespace extreme\drip\helpers;

class Batch extends Dataset
{
    public function jsonSerialize()
    {
        return [
            'batches' => [
                [
                    $this->label => $this->data,
                ],
            ],
        ];
    }
}
