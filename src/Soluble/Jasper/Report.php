<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use Soluble\Jasper\Exception\ReportFileNotFoundException;

class Report
{
    /**
     * @var string
     */
    protected $reportFile;

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

    /**
     * @return string current jrxml report file
     */
    public function getReportFile(): string
    {
        return $this->reportFile;
    }
}
