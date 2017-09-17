<?php

declare(strict_types=1);

namespace Soluble\Jasper\Exporter;

interface ExportManagerInterface
{
    public function savePdf(string $outputFile): void;
}
