<?php

declare(strict_types=1);

namespace Soluble\Jasper\Proxy;

use Soluble\Japha\Interfaces\JavaObject;

interface RemoteJavaObjectProxyInterface
{
    public function getJavaProxiedObject(): JavaObject;
}
