<?php
declare (strict_types = 1);

namespace app\demo\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return '您好！这是一个[demo]示例应用';
    }
}
