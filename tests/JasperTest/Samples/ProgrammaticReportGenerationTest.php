<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use JasperTest\Util\PDFUtils;
use Soluble\Jasper\JRParameter;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportProperties;

class ProgrammaticReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    /**
     * An example of programmatic jasper generation.
     */
    public function testProgrammaticJsonReport()
    {
        $ba = $this->ba;

        // Variables
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_test_json_northwind.jrxml';
        $jsonDataFile = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';
        $outputFile = \JasperTestsFactories::getOutputDir() . '/programmatic.pdf';

        // Clean up
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        // --------------------------------------------------------------------------------------
        // STEP 1 - compile report
        // --------------------------------------------------------------------------------------
        $compileManager = $ba->javaClass('net.sf.jasperreports.engine.JasperCompileManager');
        $jasperPrint = $compileManager->compileReport($reportFile);

        // ---------------------------------------------------------------------------------------
        // STEP 2 - getting fileResolver and classLoader
        // ---------------------------------------------------------------------------------------

        $reportPath = $ba->java('java.io.File', dirname($reportFile));

        $fileResolver = $ba->java('net.sf.jasperreports.engine.util.SimpleFileResolver', [
            $reportPath
        ]);
        $fileResolver->setResolveAbsolutePath(true);
        $classLoader = $ba->java('java.net.URLClassLoader', [$reportPath->toUrl()]);

        // ---------------------------------------------------------------------------------------
        // STEP 3 - setting context props
        // ---------------------------------------------------------------------------------------

        $props = [
            //'net.sf.jasperreports.json.source'         => $jsonDataFile,
            'net.sf.jasperreports.json.source'         => $ba->java('java.io.File', $jsonDataFile)->getAbsolutePath(),
            'net.sf.jasperreports.json.date.pattern'   => 'yyyy-MM-dd',
            'net.sf.jasperreports.json.number.pattern' => '#,##0.##',
            'net.sf.jasperreports.json.locale.code'    => 'en_GB',
            'net.sf.jasperreports.json.timezone.id'    => 'Europe/Brussels',

            JRParameter::REPORT_FILE_RESOLVER                 => $fileResolver,
            JRParameter::REPORT_CLASS_LOADER                  => $classLoader
        ];

        // ------------------------------------------------------------------------------------
        // Step 4: filling report
        // ------------------------------------------------------------------------------------

        $fillManager = $ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');
        //$fillManager = $ba->javaClass('net.sf.jasperreports.engine.JasperFillManager')->getInstance($context);

        $params = array_merge($props, [
            'LOGO_FILE'    => \JasperTestsFactories::getReportBaseDir() . '/assets/wave.png',
            'REPORT_TITLE' => 'PHPUNIT'
        ]);
        $jasperPrint = $fillManager->fillReport(
            $jasperPrint,
            $ba->java('java.util.HashMap', $params)
        );

        // -----------------------------------------------------------------------------------
        // Step 5: Exporting report in pdf
        // -----------------------------------------------------------------------------------

        $exportManager = $this->ba->javaClass('net.sf.jasperreports.engine.JasperExportManager');

        $exportManager->exportReportToPdfFile($jasperPrint, $outputFile);

        // -----------------------------------------------------------------------------------
        // Step 6: Change permissions on outputfile
        // -----------------------------------------------------------------------------------

        $file = $ba->java('java.io.File', $outputFile);
        if ($file->exists()) {
            $posixPerms = $ba->javaClass('java.nio.file.attribute.PosixFilePermission');

            $perms = $ba->java('java.util.HashSet');
            $perms->add($posixPerms->OWNER_READ);
            $perms->add($posixPerms->OWNER_WRITE);
            $perms->add($posixPerms->GROUP_READ);
            $perms->add($posixPerms->GROUP_WRITE);
            $perms->add($posixPerms->OTHERS_READ);
            $perms->add($posixPerms->OTHERS_WRITE);

            $nioFiles = $ba->javaClass('java.nio.file.Files');
            $nioPaths = $ba->javaClass('java.nio.file.Paths');
            $paths = $nioPaths->get($file->toURI());

            $nioFiles->setPosixFilePermissions($paths, $perms);
        }

        // -----------------------------------------------------------------------------------
        // Step 7: Test output
        // -----------------------------------------------------------------------------------
        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($outputFile);

        $this->assertContains('Customer Order List', $text);
        $this->assertContains('Alfreds Futterkiste', $text);
        $this->assertContains('PHPUNIT', $text);

        /*
        $queryExecuterFactory = $ba->javaClass('net.sf.jasperreports.engine.query.JsonQueryExecuterFactory');
        $report->setReportProperties(new ReportProperties([
           // 'net.sf.jasperreports.data.adapter' => $jsonAdapter->getJavaProxiedObject(),
            'net.sf.jasperreports.data.adapter' => $jsonAdapter->getJavaProxiedObject(),
            'net.sf.jasperreports.json.source' => $jsonDataFile
            //(string) $queryExecuterFactory->JSON_DATE_PATTERN =>  'yyyy-MM-dd',
            //(string) $queryExecuterFactory->JSON_NUMBER_PATTERN => '#,##0.##',
            //(string) $queryExecuterFactory->JSON_LOCALE => $locale->ENGLISH
        ]));
*/
    }

    public function testProgrammaticCSVReport()
    {
        /*
         * JRCsvDataSource dataSource = new JRCsvDataSource(JRLoader.getLocationInputStream("data/CsvDataSource.txt"));
        dataSource.setRecordDelimiter("\r\n");
        //				dataSource.setUseFirstRowAsHeader(true);
        dataSource.setColumnNames(columnNames);
         */
        $this->assertTrue(true);
    }
}
