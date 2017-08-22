<?php

declare(strict_types=1);

namespace JasperTest\Samples;

use JasperTest\Util\PDFUtils;
use Soluble\Jasper\DataSource\EmptyDataSource;
use Soluble\Jasper\Exception\JavaProxiedException;
use Soluble\Jasper\JRParameter;
use Soluble\Jasper\Proxy\Data\JsonDataAdapterImpl;
use Soluble\Jasper\Report;
use PHPUnit\Framework\TestCase;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportProperties;
use Soluble\Jasper\ReportRunnerFactory;

class JsonReportGenerationTest extends TestCase
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function setUp()
    {
        $this->ba = \JasperTestsFactories::getJavaBridgeAdapter();
    }

    public function testCSV()
    {
        /*
         * JRCsvDataSource dataSource = new JRCsvDataSource(JRLoader.getLocationInputStream("data/CsvDataSource.txt"));
        dataSource.setRecordDelimiter("\r\n");
        //				dataSource.setUseFirstRowAsHeader(true);
        dataSource.setColumnNames(columnNames);
         */
        $this->assertTrue(true);
    }

    public function testDefaultReport()
    {
        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/10_report_test_json_northwind.jrxml';
        //        $reportFile = \JasperTestsFactories::getReportBaseDir() . '/MyReports/10_report_test_json_northwind.jrxml';
        $jsonFile = \JasperTestsFactories::getDataBaseDir() . '/northwind.json';

        $reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);

        $queryExecuterFactory = $this->ba->javaClass('net.sf.jasperreports.engine.query.JsonQueryExecuterFactory');
        $locale = $this->ba->javaClass('java.util.Locale');

        $dataSource = new EmptyDataSource();

        $reportParams = new ReportParams();

        $dataFile = $this->ba->java('net.sf.jasperreports.data.StandardRepositoryDataLocation');
        $jsonFile = '/web/www/soluble-jasper/tests/data/northwind.json';
        $dataFile->setLocation($jsonFile);
        echo (string) $dataFile->getLocation();
        //die();

        $jsonAdapter = new JsonDataAdapterImpl($this->ba);
        $jsonAdapter->setLocale('en_GB');
        //$jsonAdapter->setFileName($jsonFile);
        $jsonAdapter->setNumberPattern('#,##0.##');
        $jsonAdapter->setLanguage(JsonDataAdapterImpl::LANGUAGE_JSONQL);
        $jsonAdapter->setDatePattern('yyyy-MM-dd');
        $jsonAdapter->setUseConnection(false);
        //$jsonAdapter->setDataFile($dataFile);

        $report = new Report($reportFile, $reportParams, $dataSource);

        /*
        $report->setReportProperties(new ReportProperties([
           // 'net.sf.jasperreports.data.adapter' => $jsonAdapter->getJavaProxiedObject(),
            'net.sf.jasperreports.data.adapter' => $jsonAdapter->getJavaProxiedObject(),
            'net.sf.jasperreports.json.source' => $jsonFile
            //(string) $queryExecuterFactory->JSON_DATE_PATTERN =>  'yyyy-MM-dd',
            //(string) $queryExecuterFactory->JSON_NUMBER_PATTERN => '#,##0.##',
            //(string) $queryExecuterFactory->JSON_LOCALE => $locale->ENGLISH
        ]));
*/
        $jasperReport = $reportRunner->compileReport($report);

        // SETTING FILE RESOLVER

        $reportPath = $this->ba->java('java.io.File', dirname($report->getReportFile()));
        $fileResolver = $this->ba->java('net.sf.jasperreports.engine.util.SimpleFileResolver', [
            $reportPath
        ]);
        $fileResolver->setResolveAbsolutePath(true);

        // SETTING CLASSLOADER
        $classLoader = $this->ba->java('java.net.URLClassLoader', [$reportPath->toUrl()]);

        // SETTING CONTEXT
        $context = $this->ba->java(
            'net.sf.jasperreports.engine.util.LocalJasperReportsContext',

                            $this->ba->javaClass('net.sf.jasperreports.engine.DefaultJasperReportsContext')->getInstance()
                        );

        $context->setFileResolver($fileResolver);
        $context->setClassLoader($classLoader);

        $context->setPropertiesMap([
            //'net.sf.jasperreports.data.adapter' => $jsonAdapter->getJavaProxiedObject(),
            'net.sf.jasperreports.json.source'         => $jsonFile,
            'net.sf.jasperreports.json.date.pattern'   => 'yyyy-MM-dd',
            'net.sf.jasperreports.json.number.pattern' => '#,##0.##',
            'net.sf.jasperreports.json.locale.code'    => 'en_GB',
            'net.sf.jasperreports.json.timezone.id'    => 'Europe/Brussels',
        ]);

        $context->removeProperty('net.sf.jasperreports.data.adapter');
        //$context->setProperty('net.sf.jasperreports.data.adapter', $jsonAdapter->getJavaProxiedObject());

        // Getting FillManager
        $cls = $this->ba->javaClass('net.sf.jasperreports.engine.JasperFillManager');
        $fillManager = $cls->getInstance($context);
        //var_dump($this->ba->values($context->getProperties()));

        //$jasperReport->setProperty('net.sf.jasperreports.data.adapter', $jsonAdapter->getJavaProxiedObject());
        // This to work the old way
        $props = [
            'net.sf.jasperreports.json.source'         => $jsonFile,
            'net.sf.jasperreports.json.date.pattern'   => 'yyyy-MM-dd',
            'net.sf.jasperreports.json.number.pattern' => '#,##0.##',
            'net.sf.jasperreports.json.locale.code'    => 'en_GB',
            'net.sf.jasperreports.json.timezone.id'    => 'Europe/Brussels',
        ];

        foreach ($props as $key => $value) {
            $jasperReport->setProperty($key, $value);
        }

        $jasperReport->removeProperty('net.sf.jasperreports.data.adapter');
        $jasperReport->setProperty('net.sf.jasperreports.json.source', $jsonFile);

        $jasperReport->setProperty(JRParameter::REPORT_FILE_RESOLVER, $fileResolver);
        $jasperReport->setProperty(JRParameter::REPORT_CLASS_LOADER, $classLoader);

        $fillManager->fillReport(
            $jasperReport->getJavaProxiedObject(),
            []
        );

        $exportManager = $reportRunner->getExportManager($report);

        $output_pdf = \JasperTestsFactories::getOutputDir() . '/test_json.pdf';
        if (file_exists($output_pdf)) {
            unlink($output_pdf);
        }

        try {
            $exportManager->savePdf($output_pdf);
        } catch (JavaProxiedException $e) {
            //var_dump($e->getJvmStackTrace());
            throw $e;
        }

        $pdfUtils = new PDFUtils();
        $text = $pdfUtils->getPDFText($output_pdf);

        $this->assertContains('Customer Order List', $text);
    }
}
