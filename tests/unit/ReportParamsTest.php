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

namespace JasperTest;

use PHPUnit\Framework\TestCase;
use Soluble\Jasper\Exception\InvalidArgumentException;
use Soluble\Jasper\ReportParams;

class ReportParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testContructorThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReportParams(['cool', 'hello', 'test' => 'help']);
    }

    public function testPut(): void
    {
        $p = new ReportParams();
        $p->put('REPORT_DATE', 'cool');
        self::assertEquals('cool', $p['REPORT_DATE']);
    }

    public function testGetArray(): void
    {
        $params = [
            'Title'    => 'cool',
            'SubTitle' => 'test'
        ];

        $reportParams = new ReportParams($params);
        $array        = $reportParams->toArray();

        self::assertEquals($params, $array);
        self::assertEquals('cool', $array['Title']);
    }

    public function testWithParams(): void
    {
        $p1 = new ReportParams([
            'Title'    => 'cool',
            'SubTitle' => 'test'
        ]);

        $p2 = $p1->withMergedParams(new ReportParams([
            'Title' => 'success',
            'Test'  => 'hello'
        ]));

        self::assertEquals('success', $p2['Title']);
        self::assertEquals('hello', $p2['Test']);
        self::assertEquals('test', $p2['SubTitle']);
    }

    public function testIterable(): void
    {
        $data = [
            'param1' => 0,
            'param2' => 'cool',
            'param3' => date('Y-m-d'),
            'param4' => 'hello',
            'param5' => PHP_INT_MAX
        ];
        $p       = new ReportParams($data);
        $newData = [];
        foreach ($p as $key => $value) {
            $newData[$key] = $value;
        }
        self::assertEquals($data, $newData);
    }

    public function testConstructorIterable(): void
    {
        $p  = new ReportParams(['test' => 'cool']);
        $p2 = new ReportParams($p);

        self::assertEquals('cool', $p2->offsetGet('test'));
    }

    public function testArrayAccess(): void
    {
        $p         = new ReportParams();
        $p['cool'] = 'test';
        self::assertEquals('test', $p['cool']);
        unset($p['cool']);
        self::assertFalse(isset($p['cool']));

        try {
            $p->offsetSet(1, 'cool');
            self::fail('Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            self::assertTrue(true);
        }

        try {
            $p->offsetSet('   ', 'cool');
            self::fail('Should reject blank string offsets');
        } catch (InvalidArgumentException $e) {
            self::assertTrue(true);
        }

        try {
            $p->offsetUnset(1);
            self::fail('Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            self::assertTrue(true);
        }

        try {
            $p->offsetGet(1);
            self::fail('Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            self::assertTrue(true);
        }

        try {
            $p->offsetExists(1);
            self::fail('Should reject non-string offsets');
        } catch (InvalidArgumentException $e) {
            self::assertTrue(true);
        }
    }
}
