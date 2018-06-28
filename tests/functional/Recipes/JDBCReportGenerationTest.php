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

use JasperTest\Util\PDFUtils;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

class JDBCReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp(): void
    {
        if (!\JasperTestsFactories::isJdbcTestsEnabled()) {
            self::markTestSkipped(
                'Skipping JDBCReportGeneration tests, enable option in phpunit.xml '
            );
        }
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testJDBCReport(): void
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/08_report_jdbc.jrxml';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $report = new Report(
                $reportFile,
                new ReportParams(),
                new JavaSqlConnection(
                        \JasperTestsFactories::getJdbcDsn(),
                    'com.mysql.jdbc.Driver'
                )
            );

        $exportManager = $reportRunner->getExportManager($report);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_jdbc.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        $exportManager->savePdf($output_pdf);

        // open the pdf and check for text

        $pdfUtils = new PDFUtils($output_pdf);
        $text     = $pdfUtils->getTextContent();

        self::assertContains('JDBC mysql report test', $text);
        self::assertContains('Congas', $text);
    }
}
