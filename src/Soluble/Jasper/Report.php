<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use Soluble\Jasper\Exception\ReportFileNotFoundException;
use Soluble\Jasper\Report\ReportInterface;

class Report implements ReportInterface
{
    /**
     * @var string
     */
    protected $reportFile;

    /**
     * @var ReportParams
     */
    protected $reportParams;

    /**
     * Report constructor.
     *
     * @param string $reportJRXMLFile Jasper report jrxml report file
     */
    public function __construct(string $reportJRXMLFile)
    {
        if (!file_exists($reportJRXMLFile)) {
            throw new ReportFileNotFoundException(
                sprintf(
                    'The report file "%s" cannot be found.',
                    $reportJRXMLFile
                )
            );
        }
        $this->reportFile = $reportJRXMLFile;
    }

    // public function setReportParams(ReportParams)

    /**
     * @return string current jrxml report file
     */
    public function getReportFile(): string
    {
        return $this->reportFile;
    }

    public function getStatus(): string
    {
        return ReportInterface::STATUS_FRESH;
    }
}
