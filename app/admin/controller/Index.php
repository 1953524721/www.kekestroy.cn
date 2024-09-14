<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\BaseController;
use app\Index\controller\Index AS selectBrowse;
use think\db\exception\DbException;
use think\facade\View;

class Index extends BaseController
{
    public function index(): string
    {
        return '您好！这是一个[admin]示例应用';
    }

    /**
     * @return string
     * @throws DbException
     */
    public function selectBrowseALL(): string
    {
        $list  = $this->returnBrowseObj()->browseInfoObj()->selectAll();
        if (!$list){
            $count = $list->render();
            return View::fetch('browse', [
                'list' => $list,
                'count' => $count
            ]);
        }

    }

    /**
     * @return void
     */
    public function selectBrowseFind(): void
    {
        $id = '6063';
        $list  = $this->returnBrowseObj()->browseInfoObj()->selectFind($id);
//        print_r($list);
        var_dump($list['baidu_map_code']);
    }

    /**
     * @return selectBrowse
     */
    public function returnBrowseObj(): selectBrowse
    {
        return new selectBrowse(app());
    }
}
