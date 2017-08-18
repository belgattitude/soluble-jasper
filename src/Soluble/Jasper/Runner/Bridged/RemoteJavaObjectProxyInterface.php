<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged;

use Soluble\Japha\Interfaces\JavaObject;

interface RemoteJavaObjectProxyInterface
{
    public function getJavaProxiedObject(): JavaObject;
}
