<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource\Contract;

use Soluble\Jasper\ReportParams;

interface JRDataSourceFromReportParamsInterface extends DataSourceInterface
{
    public function getDataSourceReportParams(): ReportParams;

    public function assignDataSourceReportParams(ReportParams $reportParams): void;
}
