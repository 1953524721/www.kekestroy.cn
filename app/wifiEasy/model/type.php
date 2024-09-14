<?php

namespace app\hks\model;

use think\facade\Db;
use think\Model;


class type extends Model
{
    protected $connection = 'ikun';
    protected $table = 'y_type';
    public function selectALL()
    {
        $table = 'y_type';
        return Db::connect('ikun')->table($table)->select();
    }
}