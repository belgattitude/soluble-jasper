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

namespace Soluble\Jasper\Proxy\Data;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception;
use Soluble\Jasper\Proxy\RemoteJavaObjectProxyInterface;

class JsonDataAdapterImpl implements RemoteJavaObjectProxyInterface
{
    public const LANGUAGE_JSON   = 'JSON';
    public const LANGUAGE_JSONQL = 'JSONQL';

    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var JavaObject Java('net.sf.jasperreports.data.json.JsonDataAdapterImpl')
     */
    private $jsonAdapter;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba          = $bridgeAdapter;
        $this->jsonAdapter = $this->ba->java('net.sf.jasperreports.data.json.JsonDataAdapterImpl');
    }

    public function setLocale(string $locale): void
    {
        $javaLocale = $this->ba->java('java.util.Locale', $locale);
        $this->jsonAdapter->setLocale($javaLocale);
    }

    /**
     * @param string $datePattern json date pattern in java style (i.e. 'yyyy-MM-dd')
     */
    public function setDatePattern(string $datePattern): void
    {
        $this->jsonAdapter->setDatePattern($datePattern);
    }

    /**
     * @param string $numberPattern json number pattern in java style (i.e. '#,##0.##')
     */
    public function setNumberPattern(string $numberPattern): void
    {
        $this->jsonAdapter->setNumberPattern($numberPattern);
    }

    public function setUseConnection(bool $useConnection): void
    {
        $this->jsonAdapter->setUseConnection($useConnection);
    }

    /**
     * Set local json filename.
     *
     * @see self::setDataFile()
     *
     * @throws Exception\FileNotFoundException
     */
    public function setFileName(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new Exception\FileNotFoundException(sprintf(
                'JSON file not found: %s',
                $filename
            ));
        }
        $this->jsonAdapter->setFileName($filename);
    }

    public function setLanguage(string $language): void
    {
        $jsonExpressionLanguageEnum = $this->ba->javaClass('net.sf.jasperreports.data.json.JsonExpressionLanguageEnum');
        switch (mb_strtolower($language)) {
            case 'json':
                $lang = $jsonExpressionLanguageEnum->JSON;

                break;

            case 'jsonql':
                $lang = $jsonExpressionLanguageEnum->JSONQL;

                break;
            default:
                throw new Exception\InvalidArgumentException(sprintf(
                    'Language mode not supported: %s',
                    $language
                ));
        }
        $this->jsonAdapter->setLanguage($lang);
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.data.json.JsonDataAdapterImpl')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->jsonAdapter;
    }
}
