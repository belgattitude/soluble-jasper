<?php

declare(strict_types=1);

namespace JasperTest\Util;

class MarkdownBenchmark
{
    /**
     * @var bool
     */
    public $tableHeaderPrinted = false;

    /**
     * @var int[]
     */
    public $iterations = [1, 5, 10];

    public function printTableHeader(): void
    {
        echo '| Benchmark name | ' . implode('|', array_map(function ($iter) {
            return " x$iter ";
        }, $this->iterations)) . '| Average | Memory |' . PHP_EOL;
        echo '|----| ' . implode('|', array_map(function ($iter) {
            return '----:';
        }, $this->iterations)) . '|-------:|----:| ' . PHP_EOL;
    }

    public function time(string $name, callable $fn): void
    {
        $times = [];

        $start_memory = memory_get_usage(false);

        foreach ($this->iterations as $iteration) {
            $start_time = microtime(true);
            $fn($iteration);
            $total_time        = microtime(true) - $start_time;
            $times[$iteration] = $total_time;
        }

        $memory = memory_get_usage(false) - $start_memory;

        $avg = array_sum($times) / array_sum(array_keys($times));

        /*
        $ttime = array_sum($times);
        echo number_format($ttime * 1000, 2);
        */
        echo  "| $name | " . implode('| ', array_map(function (float $time) {
            return number_format($time * 1000, 2) . 'ms';
        }, $times)) . '| ' .
            number_format($avg * 1000, 2) . 'ms| ' .
            round($memory / 1024, 2) . 'Kb' . '|' . PHP_EOL;
    }

    public function getFormattedTimeMs(int $start_time, int $end_time): string
    {
        $time = $end_time - $start_time;

        return number_format($time, 0, '.', '') . ' ms';
    }

    /**
     * Get ms time (only 64bits platform).
     */
    public function getTimeMs(): int
    {
        $mt = explode(' ', microtime());

        return ((int) $mt[1]) * 1000 + ((int) round(((int) $mt[0]) * 1000));
    }
}
