#!/bin/sh
# src 需要监控的地址
src=/data/php/swoole_framework/framework/apps/
/usr/bin/inotifywait -dmrq --timefmt '%d/%m/%y/%H:%M' --format '%T%w%f' -o /home/server/Project/test/bin/reload.log -e create $src | while read file
  do
        php /data/php/swoole_framework/framework/examples/app_server.php reload
  done
