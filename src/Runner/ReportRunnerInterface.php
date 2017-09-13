<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner;

use Soluble\Jasper\Exporter\ExportManagerInterface;
use Soluble\Jasper\Report;

interface ReportRunnerInterface
{
    public function getExportManager(Report $report): ExportManagerInterface;
}
