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
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function findIcp(): mixed
    {
        return website_parameters::where('key','icp')->find();
    }
}