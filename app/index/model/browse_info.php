<?php

namespace app\index\model;

use Exception;
use InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Model;
use think\Paginator;

class browse_info extends Model
{
    protected $name = 'browse_info';
    public mixed $browse_id;


    /**
     * 插入数据并返回主键ID
     *
     * 本函数用于将给定的数据数组插入到browse_info表中，并返回新插入数据的主键ID。
     * 如果插入成功，将返回主键ID；如果插入失败，将返回null。
     *
     * @param array $data 要插入的数据，应为包含表字段及其对应值的关联数组。
     * @return mixed 返回插入数据的主键ID，若插入失败则返回null。
     */
    public function insertDataBrowse(array $data): mixed
    {
        try {
            // 尝试创建一个新的browse_info实例并插入数据
            try {
                // 假设$data已经经过外部或内部适当的验证和过滤
                // 为简化示例，这里不展示具体的验证逻辑

                // 创建一个新的browse_info实例并传入$data参数
                $user = browse_info::create($data);

                // 检查$user是否有效，以防创建失败但未抛出异常的情况
                if ($user instanceof browse_info) {
                    // 返回插入数据的主键ID
                    return $user->getKey();
                } else {
                    // 如果$user不是预期的实例，记录错误或处理异常
                    // 此处根据实际情况记录日志，或抛出自定义异常
                    return null;
                }
            } catch (\Exception $e) {
                // 捕获并处理可能的异常，例如数据库操作失败
                // 根据实际情况记录日志或处理异常
                return null;
            }
        } catch (\Exception $e) {
            // 捕获并处理可能的其他异常
            // 根据实际情况记录日志或处理异常
            return null;
        }
    }


    /**
     * 选择所有浏览记录并分页显示
     *
     * @return Paginator
     * @throws DbException
     */
    public function selectAll(): Paginator
    {
        try {
            // 使用配置项或常量来避免硬编码，提高代码的可维护性
            return Db::name("browse_info")->order('browse_id', 'desc')->paginate(20);
        } catch (\Exception $e) {
            // 处理可能的数据库异常，比如连接失败、查询错误等
            // 根据项目的具体要求，可以记录日志、抛出自定义异常或返回错误信息等
            throw new DbException("数据库操作失败: " . $e->getMessage());
        }
    }

    /**
     * 根据ID查询浏览记录信息
     *
     * @param int $id 浏览记录ID
     * @return array 浏览记录信息数组
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function selectFind(int $id): array
    {
        // 输入验证
        if ($id < 0) {
            throw new InvalidArgumentException("Invalid browse ID provided.");
        }

        try {
            // 通过浏览记录ID查询浏览记录信息
            $data = browse_info::where('browse_id', $id)->find();
            // 检查数据是否存在
            if (empty($data)) {
                // 可根据实际情况选择抛出异常或返回空数组等
                throw new DataNotFoundException("Browse info not found for ID: " . $id);
            }
            // 将查询结果转换为数组并返回
            return $data->toArray();
        } catch (Exception $e) {
            // 处理可能的数据库异常
            // 可记录日志，并根据业务需求选择抛出异常或返回错误信息等
            throw new DbException("Error occurred while fetching browse info: " . $e->getMessage());
        }
    }

}