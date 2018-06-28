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

namespace JasperTest\Functional\Recipes;

use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\Exception\JavaSaveProxiedException;
use Soluble\Jasper\Proxy\Engine\JasperCompileManager;

class JasperCompileManagerTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testCompileReportToFileShouldWorkIfPermsAreOk(): void
    {
        $reportFile     = \JasperTestsFactories::getReportBaseDir() . '/00_report_mini.jrxml';
        $outputFile     = \JasperTestsFactories::getOutputDir() . 'testcompilereportrofile.jasper';

        $compileManager = new JasperCompileManager($this->ba);

        // clean up if any
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        touch($outputFile);
        chmod($outputFile, 0666);

        $compileManager->compileReportToFile($reportFile, $outputFile);
        self::assertFileExists($outputFile);

        unlink($outputFile);
    }

    public function testCompileReportToFileShouldNotWorkIfPermsAreNotOk(): void
    {
        $reportFile     = \JasperTestsFactories::getReportBaseDir() . '/00_report_mini.jrxml';
        $outputFile     = \JasperTestsFactories::getOutputDir() . 'testcompilereportrofile.jasper';

        $compileManager = new JasperCompileManager($this->ba);

        // clean up if any
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        touch($outputFile);
        chmod($outputFile, 0400);

        $saved = false;
        try {
            $compileManager->compileReportToFile($reportFile, $outputFile);
            // Must throw exception, this code cannot be reached !!!
            $saved = true;
            unlink($outputFile);
        } catch (JavaSaveProxiedException $e) {
            chmod($outputFile, 0600);
            unlink($outputFile);
            $saved = false;
        } finally {
            self::assertFalse($saved, "Compiled file can't be saved as expected");
        }


    }
}
