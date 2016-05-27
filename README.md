# Websocket Console Client

Websocket Console Client是用来和[Websocket Console Server](https://github.com/joy2fun/websocket-console-server)
通信的PHP客户端。

## 使用Composer安装

```sh
composer require joy2fun/websocket-console-client
```

## 基本用法

请先安装[Websocket Console Server](https://github.com/joy2fun/websocket-console-server)并启动服务。
下面假设你已经启动了WebSocket服务器，地址为：`ws://192.168.1.100:9028` 。

首先，打开[网页客户端](http://php.html.js.cn/console/) ，输入你的服务器地址并点击连接按钮，连接成功后就可以接收消息了；

然后，创建一个PHP文件 `test.php`，内容如下：

```php
<?php
require 'vendor/autoload.php';

// 连接服务器
try {
    $uri = 'ws://192.168.1.100:9028';
    $client = (new WsConsoleClient\Client($uri))->getInstance();
    $client->connect();
} catch (\Exception $e) {
    echo "连接异常";die(1);
}

// 推送消息到服务器
$client->send("Hello, Web Client!");

```

运行 `test.php` 后，你的网页客户端就会收到消息。

## 命令行工具

PHP客户端附带一个命令行工具，默认安装路径为项目根目录下的 `vendor/bin/websocket-console-client`。
可以通过运行一下命令查看它的用法：

```sh
./vendor/bin/websocket-console-client --help
```

### 推送简单消息

例如以上的例子，可以通过一行命令完成：

```sh
./vendor/bin/websocket-console-client -h 192.168.1.100 -m "Hello, Web Client!"
```

### 监控日志文件

命令行的 `-f` 选项可以用来监控日志文件，类似命令行 `tail -f` 的效果，示例如下

```sh
./vendor/bin/websocket-console-client -h 192.168.1.100 -f /wwwlog/access.log
```

如果你的PHP的 `error_log` 配置为一个文件，可以使用 `-e` 参数监控PHP日志

```sh
./vendor/bin/websocket-console-client -h 192.168.1.100 -e
```

## 演示

![http://php.html.js.cn/assets/images/websocket-console-demo.gif](http://php.html.js.cn/assets/images/websocket-console-demo.gif)
