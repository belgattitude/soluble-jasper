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

namespace Soluble\Jasper\Exporter;

use Soluble\Japha\Bridge\Exception\JavaException;

interface ExportManagerInterface
{
    /**
     * @throws JavaException i.e java.io.FileNotFoundException
     */
    public function savePdf(string $outputFile): void;
}
