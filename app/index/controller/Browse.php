<?php
declare (strict_types = 1);
namespace app\index\controller;

use app\BaseController;
use JetBrains\PhpStorm\NoReturn;
use think\response\Json;


class Browse extends BaseController
{

    /**
     * 获取百度地图API的IP定位信息
     * @return mixed 返回JSON格式的IP定位信息
     */
    public function baiDuMap(): mixed
    {
        // 获取客户端IP地址
        $ip = $this->getIP();
        // 如果IP地址为本机地址，则返回false
        if ($ip === '127.0.0.1') {
            return false;
        }
//        $ip = '117.61.185.57';
        // 百度地图API的AK
        $ak = '7GQ2o7u5lwmiOYVhhRlvAQT4IYcG3qYQ';
        /**
         * 发送GET请求
         * @param string $url 请求的URL地址
         * @param array $param 请求的参数
         * @return bool|string 返回请求结果或false
         */
        function request_get(string $url = '', array $param = array()): bool|string
        {
            // 判断请求的URL和参数是否为空
            if (empty($url) || empty($param)) {
                return false;
            }
            // 构造请求的URL
            $getUrl = $url . "?" . http_build_query($param);
            $curl = curl_init(); // 初始化curl
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_URL, $getUrl); // 抓取指定网页
            curl_setopt($curl, CURLOPT_TIMEOUT, 1000); // 设置超时时间1秒
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // curl不直接输出到屏幕
            curl_setopt($curl, CURLOPT_HEADER, 0); // 设置header
            $data = curl_exec($curl); // 运行curl
            // 如果请求失败，则输出错误信息
            if (!$data) {
                print(sprintf("an error " . "occured in function request_get(): %s\n", curl_error($curl)));
            }
            // 关闭curl
            curl_close($curl);
            // 返回请求结果
            return $data;
        }
        // 请求地址
        $url = 'https://api.map.baidu.com/location/ip';
        // 构造请求参数
        $param['ip'] = $ip;
        $param['coor'] = 'gcj02';
        $param['ak'] = $ak;
        // 发送GET请求并获取返回结果
        $res = request_get($url, $param);
        // 将原始返回的结果打印出来
//        echo "请求的原始返回结果为:\n"."<br>";
        return json_decode($res, TRUE);
    }

    /**
     * 获取客户端IP地址
     *
     * @return mixed 返回客户端IP地址
     */
    public function getIP(): mixed
    {
        // 从$_SERVER数组中获取客户端IP地址
        return $_SERVER['REMOTE_ADDR'];
    }


    /**
     * 获取浏览器信息
     *
     * @return string 浏览器类型和版本号，可能的值为：ie、firefox、chrome、opera、safari、unknown
     */
    public function getBrowseInfo(): string
    {

        /**
         * 获取浏览器类型
         *
         * @return string 浏览器类型，可能的值为：ie、firefox、chrome、opera、safari、unknown
         */
        function getBrowser(): string
        {
            // 避免重复调用，存储结果到变量中
            $isApi = isApiRequest();
            // 检查是否是API请求
            if ($isApi === '0') {
                return "API";
            } elseif ($isApi === '1' && isset($_SERVER['HTTP_USER_AGENT'])) {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                // 使用更简洁的条件判断和映射表来识别浏览器
                $browserMap = [
                    'MSIE' => "ie",
                    'rv:11.0' => "ie", // 特别处理IE11
                    'Firefox' => "firefox",
                    'Chrome' => "chrome",
                    'Opera' => "opera",
                    'Safari' => "safari",
                ];

                foreach ($browserMap as $signature => $browser) {
                    if (str_contains($agent, $signature)) {
                        return $browser;
                    }
                }

                return "unknown"; // 默认返回值，如果无法识别
            }

            return "unknown"; // 默认返回值，覆盖冗余情况
        }


        /**
         * 判断是否为API请求。
         * @return string|Json|array 根据请求头返回不同的值。
         */
        function isApiRequest(): Json|string|array
        {
            try {
                // 获取请求头
                $headers = request()->header();
                if (!is_array($headers) || !isset($headers['content-type'])) {
                    // 如果请求头不合法或者缺少'content-type'，抛出异常
                    throw new Exception("Invalid or missing request headers.");
                }
                // 判断请求头的内容类型
                if ($headers['content-type'] === 'application/x-www-form-urlencoded') {
                    return "0";
                } else {
                    return "1";
                }
            } catch (Exception $e) {
                // 异常处理：可以记录日志、返回错误信息等
                // 此处简单返回一个错误信息，实际项目中可能需要更复杂的错误处理逻辑
                error_log($e->getMessage());
                return "Error: " . $e->getMessage();
            }
        }

        /**
         * 获取浏览器版本号
         *
         * @return string 浏览器版本号或unknown
         */
        function getBrowserVer(): string
        {
            // 避免重复调用，存储结果到变量中
            $isApi = isApiRequest();
            // 检查是否是API请求
            if ($isApi === '0') {
                return "--API--";
            }else
            {
                // 判断HTTP_USER_AGENT是否存在
                if (!empty($_SERVER['HTTP_USER_AGENT']))
                {
                    $agent = $_SERVER['HTTP_USER_AGENT'];
                    // 判断是否为IE浏览器
                    if (!preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)) {
                        // 判断是否为Firefox浏览器
                        if (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs)) return $regs[1];
                        // 判断是否为Opera浏览器
                        elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs)) return $regs[1];
                        // 判断是否为Chrome浏览器
                        elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs)) return $regs[1];
                        // 判断是否为其他浏览器
                        elseif (strpos($agent, "Chrome") || !preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs)) return "unknown";
                        else return $regs[1];
                    } else return $regs[1];
                }
                return 'unknown';
            }
        }
        return getBrowser() === 'unknown' && getBrowserVer() === 'unknown' ? 'unknown' : getBrowser() . getBrowserVer();
    }

}