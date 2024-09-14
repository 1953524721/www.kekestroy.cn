<?php
declare (strict_types = 1);

namespace app\wifiEasy\controller;

use app\BaseController;
use app\index\controller\Index as indexController;
use think\response\Json;

class Index extends BaseController
{
    /**
     * 处理索引页面请求的方法
     * 主要处理POST和GET请求，并返回相应的JSON响应
     *
     * @return Json 总是返回一个JSON响应对象
     */
    public function index(): Json
    {
        // 定义状态码和对应的消息，用于后续的错误处理和提示
        define('STATUS_SUCCESS', '100000');
        define('STATUS_ENCRYPT_ERROR', '100001');
        define('STATUS_METHOD_ERROR', '100002');

        // 判断请求的方法，本方法只处理POST请求
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            try {
                // 实例化控制器对象，用于调用browseInfo方法
                $result = new IndexController(app());
                // 调用browseInfo方法可能会抛出异常，用try...catch捕获
                $result = $result->browseInfo();
                // 如果结果为false，表示操作成功但无数据返回
                if(!$result)
                {
                    // 返回成功状态的JSON响应
                    return $this->createJsonResponse(STATUS_SUCCESS, 'SUCCESS');
                }else{
                    // 对输入的数据进行验证和清理，防止XSS攻击
                    $name        = request()->param('name');
                    $text        = request()->param('text');
                    $encode      = request()->param('encode');
                    // 使用更安全的哈希算法SHA-256对数据进行加密
                    $newData     = hash('sha256', $name . $text);
                    // 检查客户端提供的加密数据是否与服务器端计算的一致
                    if ($newData === $encode)
                    {
                        // 数据匹配，返回成功状态的JSON响应
                        return $this->createJsonResponse(STATUS_SUCCESS, 'SUCCESS');
                    } else {
                        // 数据不匹配，返回加密错误状态的JSON响应
//                        return json($newData);
                        return $this->createJsonResponse(STATUS_ENCRYPT_ERROR, '加密错误');

                    }
                }
            } catch (\Exception $e) {
                // 捕获到异常，记录日志并返回内部错误状态的JSON响应
                error_log($e->getMessage());
                return $this->createJsonResponse(STATUS_METHOD_ERROR, '内部错误');
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // 如果是GET请求，直接返回请求方式错误状态的JSON响应
            return $this->createJsonResponse(STATUS_METHOD_ERROR, '请求方式错误');
        }
    }

    /**
     * 创建 JSON 响应的辅助函数
     * @param string $status 状态码
     * @param string $message 消息
     * @return Json
     */
    function createJsonResponse(string $status, string $message): Json
    {
        $array = [
            'status' => $status,
            'data' => $message
        ];
        return json($array);
    }

    public function test(): string
    {
        $name        = request()->param('name');
        $text        = request()->param('text');
        $encode      = request()->param('encode');
        $newData     = hash('sha256', $name . $text);
        if ($newData === $encode)
        {
            return "数据匹配".$name.$text.$encode;
        } else {
            return $name.$text.$encode."数据不匹配".$newData;
        }
    }

}
