<?php

declare(strict_types=1);

namespace Soluble\Jasper\ReportRunner;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

interface ReportRunnerInterface
{
    public function __construct(BridgeAdapter $bridgeAdapter);
}
