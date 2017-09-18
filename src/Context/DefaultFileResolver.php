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

namespace Soluble\Jasper\Context;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\Report;

class DefaultFileResolver
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
     * @return JavaObject Java('net.sf.jasperreports.engine.util.FileResolver')
     */
    public function getReportFileResolver(Report $report, bool $resolveAbsolutePath = true): JavaObject
    {
        $reportPath = $report->getReportPath();

        return $this->getFileResolver([$reportPath], $resolveAbsolutePath);
    }

    /**
     * @param string[] $paths               paths that will be added to the FileResolver
     * @param bool     $resolveAbsolutePath Resolve absolute paths
     * @param bool     $checkPathExists     default to true, will throw exception if path does not exists
     *
     * @return JavaObject Java('net.sf.jasperreports.engine.util.FileResolver')
     */
    public function getFileResolver(array $paths, bool $resolveAbsolutePath = true, bool $checkPathExists = true): JavaObject
    {
        $resolverPaths = [];
        foreach ($paths as $path) {
            if ($checkPathExists && !is_dir($path)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Cannot add path to FileResolver, it does not exists: %s',
                        $path
                    )
                );
            }
            $resolverPaths[] = $this->ba->java('java.io.File', $path);
        }

        $fileResolver = $this->ba->java(
            'net.sf.jasperreports.engine.util.SimpleFileResolver',
            $resolverPaths
        );

        $fileResolver->setResolveAbsolutePath($resolveAbsolutePath);

        return $fileResolver;
    }
}
