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

namespace Soluble\Jasper\Runner;

use Psr\Log\LoggerInterface;
use Soluble\Jasper\Exporter\ExportManagerInterface;
use Soluble\Jasper\Report;

interface ReportRunnerInterface
{
    public function getExportManager(Report $report): ExportManagerInterface;

    public function getLogger(): LoggerInterface;
}
