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

    public function testContructorThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReportParams(['cool', 'hello', 'test' => 'help']);
    }

    public function testPut()
    {
        $p = new ReportParams();
        $p->put('REPORT_DATE', 'cool');
        $this->assertEquals('cool', $p['REPORT_DATE']);
    }

    public function testIterable()
    {
        $data = [
            'param1' => 0,
            'param2' => 'cool',
            'param3' => date('Y-m-d'),
            'param4' => 'hello',
            'param5' => PHP_INT_MAX
        ];
        $p = new ReportParams($data);
        $newData = [];
        foreach ($p as $key => $value) {
            $newData[$key] = $value;
        }
        $this->assertEquals($data, $newData);
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
            $p->offsetSet('   ', 'cool');
            $this->assertTrue(false, 'Should reject blank string offsets');
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
