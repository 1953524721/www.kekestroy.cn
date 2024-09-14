<?php

namespace app\index\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

class website_parameters extends Model
{
    protected $name = 'website_parameters';

    /**
     * 查找网站参数中的ICP号码
     *
     * @return mixed 返回ICP号码对应的值
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */

//    public function findIcp(): ?object
//    {
//        return (new website_parameters)->where('key', 'icp')->find();
//    }
    public function findIcp(): mixed
    {
        $websiteParameters = new website_parameters;
        $result = $websiteParameters->where('key', 'icp')->find();

        // 对可能的空值进行处理
        if ($result === null) {
            // 可以在这里记录日志或进行其他处理
            return null;
        }
        return json_encode($result);
    }

}