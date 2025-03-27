<?php

namespace Progi\Domain\Model;

final class FeeLineItem
{
    public function __construct(
        public string $name,
        public float $amount
    ) {}
}
