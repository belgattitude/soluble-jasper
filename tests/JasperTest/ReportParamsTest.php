<?php

declare(strict_types=1);

namespace JasperTest;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\ReportParams;

class ReportParamsTest extends TestCase
{
    public function setUp()
    {
    }

    public function testArrayAccess()
    {
        $p = new ReportParams();
        $p['cool'] = 'test';
        $this->assertEquals('test', $p['cool']);
        unset($p['cool']);
        $this->assertFalse(isset($p['cool']));

        try {
            $p->offsetSet(1, 'cool');
            $this->assertTrue(false, 'Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $p->offsetUnset(1);
            $this->assertTrue(false, 'Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $p->offsetGet(1);
            $this->assertTrue(false, 'Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $p->offsetExists(1);
            $this->assertTrue(false, 'Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
}
