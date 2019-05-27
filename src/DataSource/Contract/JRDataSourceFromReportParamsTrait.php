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

namespace Soluble\Jasper\DataSource\Contract;

use Soluble\Jasper\ReportParams;

trait JRDataSourceFromReportParamsTrait
{
    abstract public function getDataSourceReportParams(): ReportParams;

    public function assignDataSourceReportParams(ReportParams $reportParams): void
    {
        $reportParams->addParams($this->getDataSourceReportParams());
    }
}
