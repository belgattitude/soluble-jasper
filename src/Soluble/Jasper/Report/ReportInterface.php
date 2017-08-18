<?php

declare(strict_types=1);

namespace Soluble\Jasper\Report;

interface ReportInterface
{
    public const STATUS_FRESH = 'FRESH';
    public const STATUS_COMPILED = 'COMPILED';
    public const STATUS_FILLED = 'FILLED';

    /**
     * Return report object status (FRESH, COMPILED, FILLED)s.
     *
     * @return string
     */
    public function getStatus(): string;
}
