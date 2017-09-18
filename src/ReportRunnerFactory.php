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

namespace Soluble\Jasper;

use Psr\Log\LoggerInterface;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Runner\BridgedReportRunner;

class ReportRunnerFactory
{
    /**
     * Factory method to create the default JasperBridgeReportRunner (V6) engine.
     *
     * @param BridgeAdapter $bridgeAdapter soluble japha bridge adapter
     *
     * @return BridgedReportRunner
     */
    public static function getBridgedReportRunner(BridgeAdapter $bridgeAdapter, LoggerInterface $logger = null): BridgedReportRunner
    {
        return new BridgedReportRunner($bridgeAdapter, $logger);
    }
}
