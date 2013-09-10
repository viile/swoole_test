Swoole应用服务器
====================
框架
-----
PHP高级Web开发框架，内置应用服务器。提供统一注册树，数据库操作，模板，Cache，日志，队列，上传管理，用户管理等丰富的功能特性。
使用内置应用服务器，可节省每次请求代码来的额外消耗。连接池技术可以很好的帮助存储系统节省连接资源。

Swoole_Framework支持的特性

* 热部署，代码更新后即刻生效。依赖runkit扩展（https://github.com/zenovich/runkit）
* MaxRequest进程回收机制，防止内存泄露
* 支持使用Windows作为开发环境
* http KeepAlive，可节省tcp connect带来的开销
* 静态文件缓存，节省流量
* 支持Gzip压缩，节省流量

赞助Swoole开源项目
-----
捐赠地址：http://me.alipay.com/swoole

Composer
-----
```js
{
    "require": {
        "matyhtf/swoole_framework": "dev-master",
    }
}
```

创建swoole.phar包
-----
```
php ./libs/code/phar.php
```

应用服务器
-----
需要安装swoole扩展。
```
git clone https://github.com/matyhtf/swoole.git
cd swoole
phpize
./configure
make
sudo make install
```
然后修改php.ini加入extension=swoole.so
```php
<?php
require __DIR__.'/libs/lib_config.php';

$AppSvr = new Swoole\Network\Protocol\AppServer();
$AppSvr->loadSetting(__DIR__."/swoole.ini"); //加载配置文件
$AppSvr->setAppPath(__DIR__.'/apps/'); //设置应用所在的目录
$AppSvr->setLogger(new Swoole\Log\EchoLog(false)); //Logger

/**
 *如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台，需要将worker_num设为1
 * SelectTCP 使用select做事件循环，支持windows平台，需要将worker_num设为1
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$server = new \Swoole\Network\Server('0.0.0.0', 8888);
$server->setProtocol($AppSvr);
$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 5000));

```

```shell
php server.php
[2013-07-09 12:17:05]  Swoole. running. on 0.0.0.0:8888
```

在浏览器中打开 http://127.0.0.1:8888/

压测数据
-----
本测试是使用swoole扩展作为底层Server框架的,其他驱动暂未测试.
建议使用swoole扩展，性能最佳。
```shell
ab -c 100 -n 100000 http://127.0.0.1:8888/hello/index/
This is ApacheBench, Version 2.3 <$Revision: 655654 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 10000 requests
Completed 20000 requests
Completed 30000 requests
Completed 40000 requests
Completed 50000 requests
Completed 60000 requests
Completed 70000 requests
Completed 80000 requests
Completed 90000 requests
Completed 100000 requests
Finished 100000 requests


Server Software:        Swoole
Server Hostname:        127.0.0.1
Server Port:            8888

Document Path:          /hello/index/
Document Length:        11 bytes

Concurrency Level:      100
Time taken for tests:   10.717 seconds
Complete requests:      100000
Failed requests:        0
Write errors:           0
Total transferred:      27500000 bytes
HTML transferred:       1100000 bytes
Requests per second:    9330.83 [#/sec] (mean)
Time per request:       10.717 [ms] (mean)
Time per request:       0.107 [ms] (mean, across all concurrent requests)
Transfer rate:          2505.84 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   1.0      1       9
Processing:     1   10   5.6      8      63
Waiting:        0    7   5.4      6      62
Total:          1   11   5.5      9      63

Percentage of the requests served within a certain time (ms)
  50%      9
  66%     11
  75%     12
  80%     13
  90%     17
  95%     22
  98%     28
  99%     32
 100%     63 (longest request)
```
