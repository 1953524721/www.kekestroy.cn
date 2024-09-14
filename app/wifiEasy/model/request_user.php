<?php

namespace app\hks\model;

use think\Model;

class request_user extends Model
{
    public function insertRequestUser($data): mixed
    {
        $user = request_user::create($data);
        return $user->getKey();
    }
}