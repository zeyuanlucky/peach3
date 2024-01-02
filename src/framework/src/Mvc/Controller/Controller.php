<?php
namespace Peach2\Framework\Mvc\Controller;

use Peach2\Framework\Mvc\View\View;

class Controller{

    protected $_configs;
    protected $_view;
    protected $_container;

    // 构造函数
    public function __construct($data,$container){
        $this->_configs = $data;
        $this->_container = $container;
        $this->_view = new View();
        $this->getAppPath();
    }

    // 注入数据
    public function assign($name,$data){
        $this->_view->assign($name,$data);
    }

    public function getThemeName(){
        if(isset($this->_configs['configs']['configs']['system']['theme'])){
            return $this->_configs['configs']['configs']['system']['theme'];
        }else{
            return '';
        }
    }

    public function getAppPath(){
        $theme = $this->getThemeName();
        $name = '';
        if($theme){
            $name = $theme . DIRECTORY_SEPARATOR;
        }

        $basePath = $this->_configs['appPath'] . 'View' . DIRECTORY_SEPARATOR;
        $this->assign('appPath',$basePath .= $name);
    }

    // 显示模板
    public function fetch($name,$data=[]){
        $name = str_replace('/',DIRECTORY_SEPARATOR,$name);
        $name = ltrim($name,DIRECTORY_SEPARATOR);

        $theme = $this->getThemeName();
        if($theme){
            $name = $theme . DIRECTORY_SEPARATOR . $name;
        }

        $basePath = $this->_configs['appPath'] . 'View' . DIRECTORY_SEPARATOR;
        $basePath .= $name;

        return $this->_view->fetch($basePath,$data);
    }
}