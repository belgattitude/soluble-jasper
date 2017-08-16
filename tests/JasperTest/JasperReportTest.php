<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 16/08/17
 * Time: 17:36
 */

namespace JasperTest;

use Soluble\Jasper\JasperReport;
use PHPUnit\Framework\TestCase;

class JasperReportTest extends TestCase
{

    public function testConstruct() {

        $report = new JasperReport();
        $this->assertInstanceOf(JasperReport::class, $report);
    }

}
