客户端
----
client/目录，将config.js中的ip/port改成你服务器的配置。
将client目录放到apache/nginx中的可访问路径中，如http://localhost/webim/

服务器端
----
server.php是webim的核心逻辑文件
如要想要支持IE浏览器，需要开启flash_policy的支持
```shell
php server.php
php flash_policy.php
```

