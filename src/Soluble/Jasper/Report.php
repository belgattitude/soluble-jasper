<?php

declare(strict_types=1);

namespace Soluble\Jasper;

class Report
{
    /**
     * @var string
     */
    protected $reportFile;

    public function __construct(string $reportFile)
    {
        $this->reportFile = $reportFile;
    }

    public function getReportFile(): string
    {
        return $this->reportFile;
    }
}
