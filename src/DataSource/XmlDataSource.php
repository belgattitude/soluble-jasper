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

namespace Soluble\Jasper\DataSource;

use Soluble\Jasper\DataSource\Contract\JRDataSourceFromReportParamsInterface;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromReportParamsTrait;
use Soluble\Jasper\DataSource\Contract\ReportParametrableInterface;
use Soluble\Jasper\ReportParams;

class XmlDataSource implements JRDataSourceFromReportParamsInterface, ReportParametrableInterface
{
    use JRDataSourceFromReportParamsTrait;

    public const PARAM_XML_SOURCE         = 'net.sf.jasperreports.xml.source';
    public const PARAM_XML_DATE_PATTERN   = 'net.sf.jasperreports.xml.date.pattern';
    public const PARAM_XML_NUMBER_PATTERN = 'net.sf.jasperreports.xml.number.pattern';
    public const PARAM_XML_LOCALE_CODE    = 'net.sf.jasperreports.xml.locale.code';
    public const PARAM_XML_TIMEZONE_ID    = 'net.sf.jasperreports.xml.timezone.id';

    public const DEFAULT_DATE_PATTERN   = 'yyyy-MM-dd';
    public const DEFAULT_NUMBER_PATTERN = '0.####';
    public const DEFAULT_LOCALE_CODE    = 'en_US';
    public const DEFAULT_TIMEZONE_ID    = 'UTC';

    /**
     * @var string
     */
    private $xmlSource;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $xmlSource   file or url for the json source
     * @param string $datePattern json date pattern in java style (i.e. 'yyyy-MM-dd')
     */
    public function __construct(
        string $xmlSource,
        string $datePattern = self::DEFAULT_DATE_PATTERN,
        string $numberPattern = self::DEFAULT_NUMBER_PATTERN,
        string $timezoneId = self::DEFAULT_TIMEZONE_ID,
        string $localeCode = self::DEFAULT_LOCALE_CODE
    ) {
        $this->xmlSource = $xmlSource;
        $this->setOptions([
                self::PARAM_XML_SOURCE         => $xmlSource,
                self::PARAM_XML_DATE_PATTERN   => $datePattern,
                self::PARAM_XML_NUMBER_PATTERN => $numberPattern,
                self::PARAM_XML_TIMEZONE_ID    => $timezoneId,
                self::PARAM_XML_LOCALE_CODE    => $localeCode
        ]);
    }

    public function setOptions(array $options)
    {
        $options[self::PARAM_XML_SOURCE] = $this->xmlSource;
        $this->options                   = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getDataSourceReportParams(): ReportParams
    {
        return new ReportParams($this->options);
    }
}
