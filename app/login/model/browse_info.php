<?php

namespace app\index\model;

use think\db\exception\DbException;
use think\facade\Db;
use think\Model;
use think\Paginator;

class browse_info extends Model
{
    protected $name = 'browse_info';
    public mixed $browse_id;

    /**
     * @param $data
     * @return mixed
     */
    public function insertDataBrowse($data): mixed
    {
        $user = browse_info::create($data);
        return $user->getKey();
    }

    /**
     * @return Paginator
     * @throws DbException
     */
    public function selectAll(): Paginator
    {
        return Db::name('browse_info')->order('browse_id', 'desc')->paginate(20);
    }

    public function selectFind($id)
    {
        $data = browse_info::where('browse_id',$id)->find();
        return json_decode(json_encode($data),true);
    }
}