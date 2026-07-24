<?php

namespace App\Exceptions;

use RuntimeException;

class BudgetExceededException extends RuntimeException
{
    public function __construct(
        public readonly float $utilized,
        public readonly float $allocated,
        public readonly float $consumed,
        string $message = ''
    ) {
        parent::__construct($message ?: sprintf(
            'Budget limit reached (%.1f%% utilized). The GM must allocate additional budget before new expenses can be processed.',
            $utilized
        ));
    }
}
