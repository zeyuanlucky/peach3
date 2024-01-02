<?php
namespace Peach2\Framework\Mvc\Cookie;

class Cookie
{

    public static function instance(){
        return new Cookie();
    }

    // 设置cookie
    public function set($name,$value,$expire,$path='',$domain=''){
        if (is_numeric($expire)){
            $expire = time()+$expire;
        }
        return setcookie($name, $value, $expire, $path, $domain);
    }

    // 获取cookie
    public function get($name){
        if (isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }else{
            return null;
        }
    }

    // 清除cookie
    public function remove($name){
        setcookie($name, null, time()-3600);
    }
}