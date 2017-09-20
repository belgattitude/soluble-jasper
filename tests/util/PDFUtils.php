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

    /**
     * @var string
     */
    protected $pdfFile;

    protected $pdf;

    public function __construct(string $pdfFile)
    {
        $this->parser  = new PDFParser();
        $this->pdfFile = $pdfFile;
        $this->pdf     = $this->parser->parseFile($this->pdfFile);
    }

    /**
     * Get all text content (all pages).
     */
    public function getTextContent(): string
    {
        $text  = '';
        $pages = $this->pdf->getPages();
        foreach ($pages as $page) {
            $text .= $page->getText();
        }

        return $text;
    }

    /**
     * @return string[]
     */
    public function getDetails(): array
    {
        return $this->pdf->getDetails();
    }
}
