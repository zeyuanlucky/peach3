<?php
namespace Peach2\Framework\Mvc\Session;

use function Peach2\Framework\Functions\report_message;

class Session
{
    public function __construct(){
        if(SESSSION_SWITCH==0){
            report_message([
                'message'=> 'env 文件session 未开启！',
                'code'=>'s-1',
            ]);
        }
    }

    public static function instance(){
        return new Session();
    }

    // 获取session
    public function get($name){
        if(isset($_SESSION[$name])){
            $session = $_SESSION[$name];
            if (isset($session['expire'])){
                if($session['expire']==true||$session['expire']>time()){
                    return $session['data'];
                }else{
                    return null;
                }
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    // 设置session
    public function set($name,$data,$expire=true){
        if (is_numeric($expire)){
            $expire = time()+$expire;
        }

        $_SESSION[$name] = [
            'name'=>$name,
            'data'=>$data,
            'expire'=>$expire
        ];
        return true;
    }

    // 移除某个session
    public function remove($name){
        $_SESSION[$name] = null;
    }

    // 销毁session
    public function destory(){
        session_destroy();
    }
}