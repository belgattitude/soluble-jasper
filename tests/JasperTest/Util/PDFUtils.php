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

namespace JasperTest\Util;

use Smalot\PdfParser\Parser as PDFParser;

class PDFUtils
{
    /**
     * @var PDFParser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new PDFParser();
    }

    public function getPDFText(string $pdfFile): string
    {
        $text = '';
        $pdf = $this->parser->parseFile($pdfFile);

        $pages = $pdf->getPages();

        foreach ($pages as $page) {
            $text .= $page->getText();
        }

        return $text;
    }

    /**
     * @return string[]
     */
    public function getDetails(string $pdfFile): array
    {
        $pdf = $this->parser->parseFile($pdfFile);

        return $pdf->getDetails();
    }
}
