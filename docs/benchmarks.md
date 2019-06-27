### Benchmarks

Early benchmarks for common operation (run on a laptop for now, will do soon on digitalocean). See `tests/bench/simple_benchmarks.php`.

#### Jasper compile time and filling (internal)

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_mini.jrxml (compile) | 43.03ms| 179.05ms| 347.55ms| 35.60ms| 18.97Kb|
| 00_report_mini.jrxml (fill) | 3.19ms| 9.15ms| 18.58ms| 1.93ms| 14.27Kb|
| 01_report_default.jrxml (compile) | 39.24ms| 192.41ms| 338.65ms| 35.64ms| 0.31Kb|
| 01_report_default.jrxml (fill) | 3.70ms| 11.22ms| 22.75ms| 2.35ms| 0.44Kb|


#### PDF exports

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_mini.jrxml (text-only) | 38.74ms| 3.76ms| 8.58ms| 3.19ms| 0.79Kb|
| 01_report_default.jrxml (text+png) | 318.68ms| 1,365.02ms| 2,709.56ms| 274.58ms| 0.75Kb|
| 06_report_barcodes.jrxml (barcodes) | 123.81ms| 323.71ms| 630.51ms| 67.38ms| 0.75Kb|

- Connection time: 3 ms
