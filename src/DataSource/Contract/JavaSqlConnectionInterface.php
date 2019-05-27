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

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

interface JavaSqlConnectionInterface extends DataSourceInterface
{
    public function getJasperReportSqlConnection(BridgeAdapter $bridgeAdapter): JavaObject;
}
