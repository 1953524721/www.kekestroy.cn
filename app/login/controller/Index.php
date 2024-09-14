<?php
declare (strict_types = 1);

namespace app\login\controller;

use app\BaseController;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Route;
use think\facade\View;
use app\index\controller\Index as web;

class Index extends BaseController {
    public function loginQQ(): string{
        return View::fetch('loginQQ');
    }

    /**
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login(): string
    {
        $public = __PUBLIC__;
        $web = new web(app());
        return View::fetch('login', [
            'icp'    => $web->websites()->findIcp()['value'],
            'url'    => Route::buildUrl(),
            'public' => $public,
            'date'   => date("Y-m-d H:i:s"),
        ]);
    }
}