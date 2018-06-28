<?php

declare(strict_types=1);

namespace Soluble\Jasper\Io;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Bridge\Exception\JavaException;

class JvmFileUtils
{
    /**
     * @var BridgeAdapter
     */
    protected $ba;

    public function __construct(BridgeAdapter $ba)
    {
        $this->ba = $ba;
    }

    /**
     * @param string $directory
     *
     * @throws \Soluble\Japha\Bridge\Exception\ClassNotFoundException
     * @throws \Soluble\Japha\Bridge\Exception\JavaException
     */
    public function isDirectoryWritable(string $directory): bool
    {
        try {
            $jFile = $this->ba->java('java.io.File', $directory);
        } catch (JavaException $e) {
            throw $e;
        }

        if (!$jFile->exists()) {
            throw new Exception\InvalidDirectoryException(sprintf('Directory \'%s\' does not exists (jvm-side)', $directory));
        }

        if (!$jFile->isDirectory()) {
            throw new Exception\InvalidDirectoryException(sprintf('Path \'%s\' is not a directory (jvm-side)', $directory));
        }

        return $jFile->canWrite();
    }
}
