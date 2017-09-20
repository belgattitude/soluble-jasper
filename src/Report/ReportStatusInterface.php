<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper\Report;

interface ReportStatusInterface
{
    public const STATUS_FRESH    = 'FRESH';
    public const STATUS_COMPILED = 'COMPILED';
    public const STATUS_FILLED   = 'FILLED';

    /**
     * Return report object status (FRESH, COMPILED, FILLED)s.
     *
     * @return string
     */
    public function getStatus(): string;
}
