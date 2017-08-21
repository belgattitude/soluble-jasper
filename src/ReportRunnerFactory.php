<?php

declare(strict_types=1);

namespace Soluble\Jasper;

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
    public static function getBridgedReportRunner(BridgeAdapter $bridgeAdapter): BridgedReportRunner
    {
        return new BridgedReportRunner($bridgeAdapter);
    }
}
