<?php

declare(strict_types=1);

namespace Soluble\Jasper\Context;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\Report;

class DefaultClassLoader
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    public function __construct(BridgeAdapter $ba)
    {
        $this->ba = $ba;
    }

    /**
     * @return JavaObject Java('java.lang.ClassLoader')
     */
    public function getReportClassLoader(Report $report): JavaObject
    {
        $reportPath = $report->getReportPath();

        return $this->getClassLoader([$reportPath]);
    }

    /**
     * @param array $paths           paths that will be added to the classLoader
     * @param bool  $checkPathExists default to true, will throw exception if path does not exists
     *
     * @return JavaObject Java('java.lang.ClassLoader')
     */
    public function getClassLoader(array $paths, bool $checkPathExists = true): JavaObject
    {
        $classLoaderPaths = [];
        foreach ($paths as $path) {
            if ($checkPathExists && !is_dir($path)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Cannot add path to classLoader, it does not exists: %s',
                        $path
                    )
                );
            }
            $classLoaderPaths[] = $this->ba->java('java.io.File', $path)->toUrl();
        }

        $classLoaderPaths = array_unique($classLoaderPaths);

        return $this->ba->java('java.net.URLClassLoader', $classLoaderPaths);
    }
}
