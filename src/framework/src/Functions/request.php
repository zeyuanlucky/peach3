<?php
namespace Peach2\Framework\Functions;
// request
use http\Header;

function RequestVal($name=''){
    if(!$name){
        return $_REQUEST;
    }else{
        if(isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }else{
            return null;
        }
    }
}

// post
function Post($name=''){
    if(!$name){
        return $_POST;
    }else{
        if(isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return null;
        }
    }
}

// get
function Get($name=''){
    if(!$name){
        return $_GET;
    }else{
        if(isset($_GET[$name])){
            return $_GET[$name];
        }else{
            return null;
        }
    }
}

// put
function Put($name=''){
    // 获取请求的原始数据体
    $input = file_get_contents('php://input');
    // 解析原始数据体
    parse_str($input, $parsedData);
    if(!$name){
        return $parsedData;
    }else{
        if(isset($parsedData[$name])){
            return $parsedData[$name];
        }else{
            return null;
        }
    }
}

// delete
function Delete($name=''){
    // 获取请求的原始数据体
    $input = file_get_contents('php://input');
    // 解析原始数据体
    parse_str($input, $parsedData);
    if(!$name){
        return $parsedData;
    }else{
        if(isset($parsedData[$name])){
            return $parsedData[$name];
        }else{
            return null;
        }
    }
}

// options
function Options($name=''){
    // 获取请求的原始数据体
    $input = file_get_contents('php://input');
    // 解析原始数据体
    parse_str($input, $parsedData);
    if(!$name){
        return $parsedData;
    }else{
        if(isset($parsedData[$name])){
            return $parsedData[$name];
        }else{
            return null;
        }
    }
}

// head
function Head($name=''){
    // 获取请求的原始数据体
    $input = file_get_contents('php://input');
    // 解析原始数据体
    parse_str($input, $parsedData);
    if(!$name){
        return $parsedData;
    }else{
        if(isset($parsedData[$name])){
            return $parsedData[$name];
        }else{
            return null;
        }
    }
}

// 是否ajax请求
function IsAjax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == strtolower('XMLHttpRequest');
}

// 是否get请求
function IsGet(){
    return Work('GET');
}

// 是否post请求
function IsPost(){
    return Work('POST');
}

// 是否put请求
function IsPut(){
    return Work('PUT');
}

// 是否delete请求
function IsDelete(){
    return Work('DELETE');
}

// 是否head请求
function IsHead(){
    return Work('HEAD');
}

// 是否optins请求
function IsOptions(){
    return Work('OPTIONS');
}

function getHttpurl($noport = false) {
    if (IsHttps()) {
        $url = 'https://' . $_SERVER['HTTP_HOST'];
    } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'];
    }
    if ($noport) {
        $url = str_replace(':' . $_SERVER['SERVER_PORT'], '', $url);
    }
    return $url . $_SERVER["REQUEST_URI"];
}

function IsHttps() {
    if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
        return true;
    } elseif (isset($_SERVER['REQUEST_SCHEME']) && strtolower($_SERVER['REQUEST_SCHEME']) == 'https') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https') {
        return true;
    } else {
        return false;
    }
}

// 工作函数
function Work($method){
    if($_SERVER['REQUEST_METHOD']==$method){
        return true;
    }else{
        return false;
    }
}

// server work
function ServerWork($name){
    if ($name){
        if(isset($_SERVER[$name])){
            return $_SERVER[$name];
        }
    }else{
        return null;
    }
}

#当前正在执行脚本的文件名，与 document root相关。
function GetServerPhpSelf(){
    ServerWork('PHP_SELF');
}

#传递给该脚本的参数
function GetServerArgv(){
    ServerWork('argv');
}

#包含传递给程序的命令行参数的个数（如果运行在命令行模式）。
function GetServerArgc(){
    ServerWork('argc');
}

#服务器使用的 CGI 规范的版本。例如，“CGI/1.1”。
function GetServerGatewayInterface(){
    ServerWork('GATEWAY_INTERFACE');
}

#当前运行脚本所在服务器主机的名称。
function GetServerServerName(){
    ServerWork('SERVER_NAME');
}

#服务器标识的字串，在响应请求时的头部中给出。
function GetServerServerSoftware(){
    ServerWork('SERVER_SOFTWARE');
}

#请求页面时通信协议的名称和版本。例如，“HTTP/1.0”。
function GetServerServerProtocol(){
    ServerWork('SERVER_PROTOCOL');
}

#访问页面时的请求方法。例如：“GET”、“HEAD”，“POST”，“PUT”
function GetServerRequestMethod(){
    ServerWork('REQUEST_METHOD');
}

#查询(query)的字符串。
function GetServerQueryString(){
    ServerWork('QUERY_STRING');
}

#当前运行脚本所在的文档根目录。在服务器配置文件中定义。
function GetServerDocumentRoot(){
    ServerWork('DOCUMENT_ROOT');
}

#当前请求的 Accept: 头部的内容
function GetServerHttpAccept(){
    ServerWork('HTTP_ACCEPT');
}

#当前请求的 Accept-Charset: 头部的内容。例如：“iso-8859-1,*,utf-8”。
function GetServerHttpAcceptCharset(){
    ServerWork('HTTP_ACCEPT_CHARSET');
}

#当前请求的 Accept-Encoding: 头部的内容。例如：“gzip”。
function GetServerHttpAcceptEncoding(){
    ServerWork('HTTP_ACCEPT_ENCODING');
}

#当前请求的 Accept-Language: 头部的内容。例如：“en”。
function GetServerHttpAcceptLanguage(){
    ServerWork('HTTP_ACCEPT_LANGUAGE');
}

#当前请求的 Connection: 头部的内容。例如：“Keep-Alive”。
function GetServerHttpConnection(){
    ServerWork('HTTP_CONNECTION');
}

#当前请求的 Host: 头部的内容。
function GetServerHttpHost(){
    ServerWork('HTTP_HOST');
}

#链接到当前页面的前一页面的 URL 地址。
function GetServerHttpReferer(){
    ServerWork('HTTP_REFERER');
}

#当前请求的 User_Agent: 头部的内容。
function GetServerHttpUserAgent(){
    ServerWork('HTTP_USER_AGENT');
}

#如果通过https访问,则被设为一个非空的值(on)，否则返回off
function GetServerHttps(){
    ServerWork('HTTPS');
}

#正在浏览当前页面用户的 IP 地址。
function GetServerRemoteAddr(){
    ServerWork('REMOTE_ADDR');
}

#正在浏览当前页面用户的主机名。
function GetServerRemoteHost(){
    ServerWork('REMOTE_HOST');
}

#用户连接到服务器时所使用的端口。
function GetServerRemotePort(){
    ServerWork('REMOTE_PORT');
}

#当前执行脚本的绝对路径名。
function GetServerScriptFilename(){
    ServerWork('SCRIPT_FILENAME');
}

#管理员信息
function GetServerServerAdmin(){
    ServerWork('SERVER_ADMIN');
}

#服务器所使用的端口
function GetServerServerPort(){
    ServerWork('SERVER_PORT');
}

#包含服务器版本和虚拟主机名的字符串。
function GetServerServerSignature(){
    ServerWork('SERVER_SIGNATURE');
}

#当前脚本所在文件系统（不是文档根目录）的基本路径。
function GetServerPathTranslated(){
    ServerWork('PATH_TRANSLATED');
}

#包含当前脚本的路径。这在页面需要指向自己时非常有用。
function GetServerScriptName(){
    ServerWork('SCRIPT_NAME');
}

#访问此页面所需的 URI。例如，“/index.html”。
function GetServerRequestUri(){
    ServerWork('REQUEST_URI');
}

#当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的用户名。
function GetServerPhpAuthUser(){
    ServerWork('PHP_AUTH_USER');
}

#当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的密码。
function GetServerPhpAuthPw(){
    ServerWork('PHP_AUTH_PW');
}

#当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是认证的类型。
function GetServerAuthType(){
    ServerWork('AUTH_TYPE');
}

#透过代理服务器取得客户端的真实 IP 地址
function GetServerHttpXFowardedFor(){
    ServerWork('HTTP_X_FORWARDED_FOR');
}

#代理服务器IP
function GetServerHttpVia(){
    ServerWork('HTTP_VIA');
}

#客户端IP
function GetServerHttpClientIp(){
    ServerWork('HTTP_CLIENT_IP');
}

function redirect($url){
    \header('Location:'.$url);
}