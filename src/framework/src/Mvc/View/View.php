<?php
namespace Peach2\Framework\Mvc\View;

use function Peach2\Framework\Functions\report_message;

class View{

    protected $_viewArgs;

    public function fetch($path,$assign=[]){
        if (is_array($this->_viewArgs)){
            extract($this->_viewArgs);
        }
        extract($assign);

        if (is_file($path)){
            include $path;
        }else{
            if (is_file($appPath.$path)){
                include $appPath.$path;
            }else{
                report_message([
                    'message'=> '模板文件：'. $path .'不存在！',
                    'code'=>'v-1',
                ]);
            }
        }
    }

    public function assign($key,$val){
        $this->_viewArgs[$key] = $val;
    }
}