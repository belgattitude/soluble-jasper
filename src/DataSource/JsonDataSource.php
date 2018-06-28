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

class JsonDataSource implements JRDataSourceFromReportParamsInterface, ReportParametrableInterface
{
    use JRDataSourceFromReportParamsTrait;

    public const PARAM_JSON_SOURCE         = 'net.sf.jasperreports.json.source';
    public const PARAM_JSON_DATE_PATTERN   = 'net.sf.jasperreports.json.date.pattern';
    public const PARAM_JSON_NUMBER_PATTERN = 'net.sf.jasperreports.json.number.pattern';
    public const PARAM_JSON_LOCALE_CODE    = 'net.sf.jasperreports.json.locale.code';
    public const PARAM_JSON_TIMEZONE_ID    = 'net.sf.jasperreports.json.timezone.id';

    public const DEFAULT_DATE_PATTERN   = 'yyyy-MM-dd';
    public const DEFAULT_NUMBER_PATTERN = '0.####';
    public const DEFAULT_LOCALE_CODE    = 'en_US';
    public const DEFAULT_TIMEZONE_ID    = 'UTC';

    /**
     * @var string
     */
    private $jsonSource;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $jsonSource  file or url for the json source
     * @param string $datePattern json date pattern in java style (i.e. 'yyyy-MM-dd')
     */
    public function __construct(
        string $jsonSource,
                                string $datePattern = self::DEFAULT_DATE_PATTERN,
                                string $numberPattern = self::DEFAULT_NUMBER_PATTERN,
                                string $timezoneId = self::DEFAULT_TIMEZONE_ID,
                                string $localeCode = self::DEFAULT_LOCALE_CODE
    ) {
        $this->jsonSource = $jsonSource;
        $this->setOptions([
                self::PARAM_JSON_SOURCE         => $jsonSource,
                self::PARAM_JSON_DATE_PATTERN   => $datePattern,
                self::PARAM_JSON_NUMBER_PATTERN => $numberPattern,
                self::PARAM_JSON_TIMEZONE_ID    => $timezoneId,
                self::PARAM_JSON_LOCALE_CODE    => $localeCode
        ]);
    }

    public function setOptions(array $options): void
    {
        $options[self::PARAM_JSON_SOURCE] = $this->jsonSource;
        $this->options                    = $options;
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
