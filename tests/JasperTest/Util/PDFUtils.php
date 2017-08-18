<?php

declare(strict_types=1);

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

        // Loop over each page to extract text.
        foreach ($pages as $page) {
            $text .= $page->getText();
        }

        return $text;
    }
}
