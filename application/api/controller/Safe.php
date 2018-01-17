<?php
Class Safe{
	function sec(&$array) {  
    //如果是数组，遍历数组，递归调用  
    if (is_array($array)) {  
        foreach ($array as $k => $v) {  
            $array[$k] = sec($v);  
        }  
    } else if (is_string($array)) {  
        //使用addslashes函数来处理  
        $array = addslashes($array);  
    } else if (is_numeric($array)) {  
        $array = intval($array);  
    }  
    return $array;  
	} 
/**
* 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
*  
* @param  string $value 需要过滤的值
* @return string
*/
function fliter_script($value) {
$value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$value);
$value = preg_replace("/(.*?)<\/script>/si","",$value);
$value = preg_replace("/(.*?)<\/iframe>/si","",$value);
$value = preg_replace ("//iesU", '', $value);
return $value;
}
 
/**
* 安全过滤类-过滤HTML标签
*  Controller中使用方法：$this->controller->fliter_html($value)
* @param  string $value 需要过滤的值
* @return string
*/
function fliter_html($value) {
if (function_exists('htmlspecialchars')) return htmlspecialchars($value);
return str_replace(array("&", '"', "'", "<", ">"), array("&", "\"", "'", "<", ">"), $value);
}
 
/**
* 安全过滤类-对进入的数据加下划线 防止SQL注入
*  Controller中使用方法：$this->controller->fliter_sql($value)
* @param  string $value 需要过滤的值
* @return string
*/
function fliter_sql($value) {
$sql = array("select", 'insert', "update", "delete", "\'", "\/\*",
     "\.\.\/", "\.\/", "union", "into", "load_file", "outfile");
$sql_re = array("","","","","","","","","","","","");
return str_replace($sql, $sql_re, $value);
}
 
/**
* 安全过滤类-通用数据过滤
*  Controller中使用方法：$this->controller->fliter_escape($value)
* @param string $value 需要过滤的变量
* @return string|array
*/
function fliter_escape($value) {
if (is_array($value)) {
  foreach ($value as $k => $v) {
   $value[$k] = self::fliter_str($v);
  }
} else {
  $value = self::fliter_str($value);
}
return $value;
}
 
/**
* 安全过滤类-字符串过滤 过滤特殊有危害字符
*  Controller中使用方法：$this->controller->fliter_str($value)
* @param  string $value 需要过滤的值
* @return string
*/
function fliter_str($value) {
$badstr = array("\0", "%00", "\r", '&', ' ', '"', "'", "<", ">", "   ", "%3C", "%3E");
$newstr = array('', '', '', '&', ' ', '"','"', ''',''', "<", ">", "   ", "<", ">");
$value  = str_replace($badstr, $newstr, $value);
$value  = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $value);
return $value;
}
 
/**
* 私有路劲安全转化
*  Controller中使用方法：$this->controller->filter_dir($fileName)
* @param string $fileName
* @return string
*/
function filter_dir($fileName) {
$tmpname = strtolower($fileName);
$temp = array(':/',"\0", "..");
if (str_replace($temp, '', $tmpname) !== $tmpname) {
  return false;
}
return $fileName;
}
 
/**
* 过滤目录
*  Controller中使用方法：$this->controller->filter_path($path)
* @param string $path
* @return array
*/
public function filter_path($path) {
$path = str_replace(array("'",'#','=','`','$','%','&',';'), '', $path);
return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $path), '/');
}
 
/**
* 过滤PHP标签
*  Controller中使用方法：$this->controller->filter_phptag($string)
* @param string $string
* @return string
*/
public function filter_phptag($string) {
return str_replace(array(''), array('<?', '?>'), $string);
}
 
/**
* 安全过滤类-返回函数
*  Controller中使用方法：$this->controller->str_out($value)
* @param  string $value 需要过滤的值
* @return string
*/
public function str_out($value) {
$badstr = array("<", ">", "%3C", "%3E");
$newstr = array("<", ">", "<", ">");
$value  = str_replace($newstr, $badstr, $value);
return stripslashes($value); //下划线
}
}