<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

namespace Soluble\Jasper;

interface JRParameter
{
    public const REPORT_CLASS_LOADER    = 'REPORT_CLASS_LOADER';
    public const REPORT_CONNECTION      = 'REPORT_CONNECTION';
    public const REPORT_CONTEXT         = 'REPORT_CONTEXT';
    public const REPORT_DATA_SOURCE     = 'REPORT_DATA_SOURCE';
    public const REPORT_FILE_RESOLVER   = 'REPORT_FILE_RESOLVER';
    public const REPORT_FORMAT_FACTORY  = 'REPORT_FORMAT_FACTORY';
    public const REPORT_LOCALE          = 'REPORT_LOCALE';
    public const REPORT_RESOURCE_BUNDLE = 'REPORT_RESOURCE_BUNDLE';
    public const REPORT_TEMPLATES       = 'REPORT_TEMPLATES';
    public const REPORT_TIME_ZONE       = 'REPORT_TIME_ZONE';
    public const REPORT_VIRTUALIZER     = 'REPORT_VIRTUALIZER';
}
