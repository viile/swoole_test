WebIM部署方法
=====
客户端：webim/ ，将此目录加入nginx的静态文件请求中

服务器端：
```shell
php webim_server.php
php webim/flash_policy.php #这里是flash-websocket的xml-socket授权
```

swoole websocket是支持IE浏览器的，在不支持HTML5标准的浏览器上，如IE6/7/8/8，swoole框架会自动启用flash-websocket。

