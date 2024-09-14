<?php
use Swoole\WebSocket\Server;
use think\facade\Db;

// 创建WebSocket服务器对象，监听0.0.0.0:9502端口  
$ws = new Server("0.0.0.0", 9502);

// 监听WebSocket连接建立事件  
$ws->on('open', function ($ws, $request) {
    echo "connection open: {$request->fd}\n";
});

// 监听WebSocket消息事件  
$ws->on('message', function ($ws, $frame) {
    echo "received message: {$frame->data}\n";
    // 在这里可以进行数据库操作，或者使用ThinkPHP的其他功能  
    $ws->push($frame->fd, json_encode(["hello", "world"])); // 向客户端发送消息  
});

// 监听WebSocket连接关闭事件  
$ws->on('close', function ($ws, $fd) {
    echo "connection close: {$fd}\n";
});

// 启动Swoole WebSocket服务器  
$ws->start();