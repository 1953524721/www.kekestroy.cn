<?php
declare (strict_types = 1);

namespace app\login\controller;

use app\BaseController;
use Exception;
use think\facade\Route;
use think\facade\View;
use app\index\controller\Index as web;

class Index extends BaseController
{
    public function loginQQ(): string
    {
        return View::fetch('loginQQ');
    }

    public function login(): string
    {
        $public = __PUBLIC__;
        $web = new web(app());
//        print_r($web);die();
        return View::fetch('login', [
            'icp' => json_decode($web->websites()->findIcp(),true)['value'],
            'url' => Route::buildUrl(),
            'public' => $public,
            'date' => date("Y-m-d H:i:s"),
        ]);
    }


}