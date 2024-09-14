<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\BaseController;
use app\index\model\browse_info;
use app\index\model\website_parameters;
use JetBrains\PhpStorm\NoReturn;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\facade\Db;
use think\facade\Route;
use think\facade\View;


class Index extends BaseController {

    const DATE_FORMAT = "Y-m-d H:i:s";


    /**
     * 显示首页
     *
     * @return string 返回渲染后的视图字符串
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index(): string
    {
//        return View::fetch('index');
        // 渲染视图并返回渲染后的字符串
        try {
            // 获取备案号和浏览信息，考虑异常处理和效率优化
            $icp = $this->getIcpInfo();
            $browseInfo = $this->browseInfo();

            // 构建要传递给视图的数据
            $data = [
                'icp' => $icp['value'],
                'url' => Route::buildUrl(),
                'date' => date(self::DATE_FORMAT),
                'state' => $browseInfo
            ];

            // 渲染视图并返回渲染后的字符串
            return View::fetch('index', $data);
        } catch (\Exception $e) {
            // 在这里处理可能的异常，例如记录日志，并根据需要抛出异常或返回错误页面
            // 记录日志（这里使用伪代码）
            print_r("Error rendering index page: " . $e->getMessage());

            // 根据实际情况，您可能希望抛出异常或返回一个错误页面
            throw $e; // 或者返回一个错误页面，例如 return View::fetch('errorPage');
        }

    }
//return View::fetch('index', [
//'icp' => $this->websites()->findIcp()['value'],
//'url' => Route::buildUrl(),
//'date' => date("Y-m-d H:i:s"),
//'state' => $this->browseInfo()
//]);

    /**
     * 获取备案信息
     * @return array 返回备案号信息
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    #[NoReturn] private function getIcpInfo(): array
    {
        // 假设`findIcp`方法返回一个数组，如果该方法可能引发异常，我们应该在这里处理
        $icpInfo = json_decode($this->websites()->findIcp(),true);
        $sql = Db::getLastSql();
        // 检查是否返回了有效的数据
        if (empty($icpInfo)) {
            // 处理未找到备案信息的情况，可能需要抛出异常或返回默认值
            throw new DataNotFoundException("ICP information not found.");
        }
        return $icpInfo;
    }
    /**
     * 获取浏览器信息
     *
     * @return string 返回浏览器城市信息
     */
    public function browseInfo(): string
    {
        function isLocalIP($ip): bool
        {
            // 使用filter_var验证IP地址格式并检查是否为本地IP
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }
        function safeEncode($data): bool|string
        {
            // 对数据进行安全编码，防止注入
            return json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        }
        $Browse = new Browse(app());
        $browse_ip = $Browse->getIP();

        if (isLocalIP($browse_ip)) {
            return '127.0.0.1';
        }
        // 构造浏览器信息数组
        $data['browse_ip']           = $browse_ip;
        $data['browse_time']         = date("Y-m-d H:i:s");
        $data['browse_name']         = $Browse->getBrowseInfo();

        try {
            // 调用百度地图API获取城市信息
            $baidu = $Browse->baiDuMap();
            if (empty($baidu['content']['address'])) {
            throw new Exception("百度地图API返回数据异常");
        }

            $data['browse_city']         = $baidu['content']['address'];
            $data['browse_city_code']    = $baidu['content']['address_detail']['city_code'];
            $data['browse_city_address'] = $baidu['address'];
            $data['point_x']             = $baidu['content']['point']['x'];
            $data['point_y']             = $baidu['content']['point']['y'];
        } catch (Exception $e) {
            // 处理API请求异常
            // 此处可以记录日志或者返回一个默认值
            return "无法获取浏览器城市信息";
        }
        // 将服务器信息和百度地图API返回值转换为JSON字符串并存入数据库
        $data['service']             = safeEncode($_SERVER);
        $data['baidu_map_code']      = safeEncode($baidu);
        // 假设`insertDataBrowse`是已经做了数据注入防护的方法
        $this->browseInfoObj()->insertDataBrowse($data);
        // 返回浏览器城市信息
        return $data['browse_city'];
    }
    /**
     * 获取网站参数对象
     *
     * @return website_parameters 网站参数对象
     */
    public function websites(): website_parameters
    {
        // 创建并返回一个新的网站参数对象
        return new website_parameters();

    }


    /**
     * 获取浏览信息对象
     *
     * @return browse_info 浏览信息对象
     */
    public function browseInfoObj(): browse_info
    {
        // 创建并返回一个新的浏览信息对象
        return new browse_info();
    }

    /**
     * 百度翻译API接口调用函数
     * 本函数用于通过百度翻译API接口，将输入的字符串进行翻译后返回翻译结果。
     * @param string $query 待翻译的字符串
     * @return string 翻译结果
     */
    public function fanYi(string $query): string
    {
        $APP_ID = env('BAIDU_APP_ID'); // 从环境变量中获取百度APP ID
        $SEC_KEY = env('BAIDU_SEC_KEY'); // 从环境变量中获取百度SEC_KEY
        // 设置API地址和参数
        $URL = "https://api.fanyi.baidu.com/api/trans/vip/translate";
        $salt = rand(10000, 99999); // 随机生成盐值，用于签名
        // 生成签名
        $str = $APP_ID . $query . $salt . $SEC_KEY;
        $sign = md5($str);
        $url = $URL . "?" . "q=$query&from=en&to=zh&appid=$APP_ID&salt=$salt&sign=$sign";
        // 调用API获取翻译结果
        $data = json_decode(file_get_contents($url), true);
        // 检查API响应是否正常，是否有翻译结果
        if (!isset($data['trans_result']) || count($data['trans_result']) < 1) {
            print_r('翻译失败或无结果');
        }
        // 输出原始数据和翻译结果
        echo "原数据为：$query<br>翻译后数据为：" . $data['trans_result'][0]['dst'];
        return $data['trans_result'][0]['dst']; // 返回翻译结果
    }
    /**
     * 测试函数
     *
     * @return string 返回转换后的字符串
     */
    public function test(): string
    {
        // 定义一个字符串变量
        $str = "HelloWorld";
        // 调用 fanYi 方法并返回结果
        return $this->fanYi($str);
    }

    /**
     * 打印PHP信息
     */
    public function test2(): void
    {
        // 调用print_r函数打印PHP信息
        print_r(phpinfo());
    }


}