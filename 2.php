<?php
header("Content-Type: text/html;charset=utf-8");
$str  = '{"address":"CN|\u6c5f\u82cf|None|None|CHINANET|0|0","content":{"address":"\u6c5f\u82cf\u7701","address_detail":{"adcode":"320000","city":"","city_code":18,"district":"","province":"\u6c5f\u82cf\u7701","street":"","street_number":""},"point":{"x":"119.36848894","y":"33.01379717"}},"status":0}';
$data = json_decode($str,TRUE);

//$data = (array)$str;
//var_dump($data);
print_r($data['content']['address']);


//var_dump($str);
//$arr = explode(", ", $str);
//$ip   = '117.61.176.200';
//$ak   = '7GQ2o7u5lwmiOYVhhRlvAQT4IYcG3qYQ';
//$url  =  "https://api.map.baidu.com/location/ip?ip=$ip&coor=bd09ll&ak=$ak";
//$data = file_get_contents($url);
//var_dump($data);