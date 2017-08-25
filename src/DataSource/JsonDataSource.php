<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource;

use Soluble\Jasper\DataSource\Contract\JRDataSourceInterface;

class JsonDataSource implements JRDataSourceInterface
{
    /*
     *             'net.sf.jasperreports.json.source'         => $ba->java('java.io.File', $jsonDataFile)->getAbsolutePath(),
                'net.sf.jasperreports.json.date.pattern'   => 'yyyy-MM-dd',
                'net.sf.jasperreports.json.number.pattern' => '#,##0.##',
                'net.sf.jasperreports.json.locale.code'    => 'en_GB',
                'net.sf.jasperreports.json.timezone.id'    => 'Europe/Brussels',
    
     */

    public const PARAM_JSON_SOURCE = 'net.sf.jasperreports.json.source';
    public const PARAM_JSON_DATE_PATTERN = 'net.sf.jasperreports.json.date.pattern';
    public const PARAM_JSON_NUMBER_PATTERN = 'net.sf.jasperreports.json.number.pattern';
    public const PARAM_JSON_LOCALE_CODE = 'net.sf.jasperreports.json.locale.code';
    public const PARAM_JSON_TIMEZONE_ID = 'net.sf.jasperreports.json.timezone.id';

    /*
    *             'net.sf.jasperreports.json.source'         => $ba->java('java.io.File', $jsonDataFile)->getAbsolutePath(),
            'net.sf.jasperreports.json.date.pattern'   => 'yyyy-MM-dd',
            'net.sf.jasperreports.json.number.pattern' => '#,##0.##',
            'net.sf.jasperreports.json.locale.code'    => 'en_GB',
            'net.sf.jasperreports.json.timezone.id'    => 'Europe/Brussels',
*/

    public const DEFAULT_DATE_PATTERN = 'yyyy-MM-dd';
    public const DEFAULT_NUMBER_PATTERN = '#,##0.##';
    public const DEFAULT_LOCALE_CODE = 'en_US';
    public const DEFAULT_TIMEZONE_ID = 'UTC';

    /**
     * @var string
     */
    private $jsonSource;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string     $jsonFile
     * @param array|null $options
     */
    public function __construct(
        string $jsonSource,
                                string $datePattern = self::DEFAULT_DATE_PATTERN,
                                string $numberPattern = self::DEFAULT_NUMBER_PATTERN,
                                string $timezoneId = self::PARAM_JSON_TIMEZONE_ID,
                                string $localeCode = self::PARAM_JSON_LOCALE_CODE
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

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
