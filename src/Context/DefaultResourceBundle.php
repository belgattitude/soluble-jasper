<?php

declare(strict_types=1);

namespace Soluble\Jasper\Context;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Proxy\Engine\JasperReport;

class DefaultResourceBundle
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    public function __construct(BridgeAdapter $ba)
    {
        $this->ba = $ba;
    }

    public function getResourceBundle(JasperReport $jasperReport, JavaObject $locale, JavaObject $classLoader): ?JavaObject
    {
        $reportBundle = $jasperReport->getResourceBundle();
        if ($reportBundle != '') {
            $resourceBundle = $this->ba->javaClass('java.util.ResourceBundle');
            $resourceBundle->getBundle($reportBundle, $locale, $classLoader);

            return $resourceBundle;
        }
    }
}
