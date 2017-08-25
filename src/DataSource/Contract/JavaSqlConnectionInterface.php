<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource\Contract;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

interface JavaSqlConnectionInterface extends DataSourceInterface
{
    public function getJasperReportSqlConnection(BridgeAdapter $bridgeAdapter): JavaObject;
}
