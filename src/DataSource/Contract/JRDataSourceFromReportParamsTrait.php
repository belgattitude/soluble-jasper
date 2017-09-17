<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource\Contract;

use Soluble\Jasper\ReportParams;

trait JRDataSourceFromReportParamsTrait
{
    abstract public function getDataSourceReportParams(): ReportParams;

    public function assignDataSourceReportParams(ReportParams $reportParams): void
    {
        $reportParams->addParams($this->getDataSourceReportParams()->getIterator());
    }
}
