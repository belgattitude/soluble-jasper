<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use Soluble\Jasper\ReportRunner\ReportRunnerInterface;
use Soluble\Jasper\ReportRunner\JasperReportRunner;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

class ReportRunnerFactory
{
    public const JAPHA_JASPER_V6 = 'japha_jasper_v6';

    public const SUPPORTED_RUNNERS = [
        self::JAPHA_JASPER_V6 => JasperReportRunner::class
    ];

    /**
     * Factory method to create the default JasperReportRunner (V6) engine.
     *
     * @param BridgeAdapter $bridgeAdapter soluble japha bridge adapter
     *
     * @return JasperReportRunner
     */
    public static function getJasperReportRunner(BridgeAdapter $bridgeAdapter): JasperReportRunner
    {
        return new JasperReportRunner($bridgeAdapter);
    }

    /**
     * Factory method to create a custom ReportRunner engine.
     *
     * @param BridgeAdapter $bridgeAdapter soluble japha bridge adapter
     *
     * @return ReportRunnerInterface
     */
    public function __invoke(BridgeAdapter $bridgeAdapter, string $runner = self::JAPHA_JASPER_V6): ReportRunnerInterface
    {
        if (!array_key_exists($runner, self::SUPPORTED_RUNNERS)) {
            throw new Exception\UnsupportedRunnerException(
                sprintf(
                    'Unsupported runner "%s", must be in (%s)',
                    $runner,
                    implode(',', array_keys(self::SUPPORTED_RUNNERS))
                )
            );
        }

        $class = self::SUPPORTED_RUNNERS[$runner];

        return new $class($bridgeAdapter);
    }
}
