<?php
declare (strict_types = 1);

namespace app\message\controller;

use app\BaseController;
use think\facade\View;


class Index extends BaseController
{
    public function index(): string
    {
        return '您好！这是一个[message]示例应用';
    }

    public function servers()

    {
        $http = new Swoole\http\Server("localhost,80");
    }
    public function laoke()
    {
        return View::fetch('index');
    }
}
