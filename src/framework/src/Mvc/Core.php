<?php
namespace Peach2\Framework\Mvc;
use function Peach2\Framework\Functions\report_message;
use function Peach2\Framework\Functions\getHttpurl;
use FastRoute\Dispatcher;

class Core{

    private $__configs;
    private static $__loadArr;

    public function run(){
        $this->__init();
    }

    // 初始化系统
    private function __init(){
        // 载入Functions
        define('FRAMEWORK_PATH',dirname(__DIR__) . DIRECTORY_SEPARATOR);
        $this->load(FRAMEWORK_PATH . 'Functions' . DIRECTORY_SEPARATOR);

        /**
         * env
         */
        // 初始化 env
        $envPath = ROOT_PATH . 'env';
        if(is_file($envPath)){
            $env = parse_ini_file($envPath);
            if(count($env)==0){
                // 提示
                report_message([
                    'message'=> '文件：'. $envPath .' 未定义！',
                    'code'=>'f-2',
                ]);
            }else{
                $this->__configs['env'] = $env;
                if(!isset($env['debug'])){
                    $env['debug'] = 0;
                }

                $this->__load();
            }
        }else{
            report_message([
                'message'=> '文件：'. $envPath .'不存在！',
                'code'=>'f-1',
            ]);
        }
    }

    private function __load(){
        // 载入config
        $this->__configs['framework_configs'] = $this->load(FRAMEWORK_PATH . 'Configs' . DIRECTORY_SEPARATOR,true);
        self::$__loadArr = null;

        $this->__configs['global_configs'] = $this->load(ROOT_PATH . 'configs' . DIRECTORY_SEPARATOR,true);
        self::$__loadArr = null;

        $sessionSwitch = 0;
        if(isset($this->__configs['env']['session_switch'])){
            if($this->__configs['env']['session_switch']==1){
                if (isset($this->__configs['env']['session_cache_expire'])){
                    if (is_numeric($this->__configs['env']['session_cache_expire'])){
                        session_cache_expire($this->__configs['env']['session_cache_expire']);
                    }
                }

                session_start();
                $sessionSwitch = 1;
            }
        }

        define('SESSSION_SWITCH',$sessionSwitch);
        $this->__define();
    }

    private function __define(){
        define('AUTHOR','zeyuan');

        $cacheSwitch = 0;
        if(isset($this->__configs['global_configs']['configs.system']['system']['CacheSwitch'])){
            if ($this->__configs['global_configs']['configs.system']['system']['CacheSwitch']==1){
                $cacheSwitch = 1;
            }
        }

        $debugSwitch = 0;
        if(isset($this->__configs['global_configs']['configs.system']['system']['DebugSwitch'])){
            if ($this->__configs['global_configs']['configs.system']['system']['DebugSwitch']==1){
                $debugSwitch = 1;
            }
        }

        $loginSwitch = 0;
        if(isset($this->__configs['global_configs']['configs.system']['system']['LoginSwitch'])){
            if ($this->__configs['global_configs']['configs.system']['system']['LoginSwitch']==1){
                $loginSwitch = 1;
            }
        }

        define('CACHE_SWITCH',$cacheSwitch);
        define('DEBUG_SWITCH',$debugSwitch);
        define('LOGIN_SWITCH',$loginSwitch);

		define('SYSTEM_NAME','Peach CMS System');
        define('SECURE',1);
        define('MYSQLI_SINGLE',$this->__configs['env']);

        $this->__error();
        $this->__route();
    }

    private function __error(){
        if($this->__configs['env']['debug']==1||DEBUG_SWITCH==1){
            // 开启错误提示
            ini_set("display_errors",1);//打开错误提示
            ini_set("error_reporting",E_ALL);
            error_reporting(E_ALL);

            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }else{
            // 关闭错误提示
            ini_set("display_errors",0);
            ini_set("error_reporting",E_ALL);
            error_reporting(0);
        }
    }

    private function __route(){
        $pateUrl =  getHttpurl();
        $parseUrlArr = parse_url($pateUrl);

        $url = $parseUrlArr['path'];
        if($url=='/'){
            $app = 'Home';
        }else{
            $url = trim($url,'/');
            if(strpos($url,'/')){
                $url = explode('/',$url);
                if (isset($url['0'])){
                    $app = $url['0'];
                }else{
                    print_r($url);
                }
            }else{
                $app = $url;
            }
        }

        $app = strtolower($app);
        if(isset($this->__configs['global_configs']['configs.system']['app'][$app])){
            $appName = $this->__configs['global_configs']['configs.system']['app'][$app];
        }else{
            $appName = 'Home';
        }

        $appPath = ROOT_PATH . 'app' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . ucfirst($appName) .DIRECTORY_SEPARATOR;
        $appRoutePath = $appPath . 'Route' . DIRECTORY_SEPARATOR .'Route.php';
        if(!is_file($appRoutePath)){
            report_message([
                'message'=> '文件：'. $appRoutePath .'不存在！',
                'code'=>'f-3',
            ]);
        }else{
            $dispatcher = include $appRoutePath;
        }

        define('APP_PATH',$appPath);
        
        $this->__configs['configs'] = $this->load($appPath . 'Configs' . DIRECTORY_SEPARATOR,true);
        self::$__loadArr = null;

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $path404 = $appPath . 'View' . DIRECTORY_SEPARATOR . '404.html';
                include $path404;
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                $path405 = $appPath . 'View' . DIRECTORY_SEPARATOR . '405.html';
                include $path405;
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $path444 = $appPath . 'View' . DIRECTORY_SEPARATOR . '444.html';
                if(!isset($this->__configs['env']['authorize_code'])||!$this->__configs['env']['authorize_code']){
                    $this->__configs['env']['authorize_code'] = '';
                }

                /*
                $rand = mt_rand(1,9999);
                if($rand<221){
                    $rs = @file_get_contents('https://www.peach21.com/online_verification.xhtml?code='.$this->__configs['env']['authorize_code']);
                    if($rs){
                        $frs = json_decode($rs,true);
                        if($rs['code']==-1){
                            echo '运行该程序需要授权码';exit;
                        }
                    }
                }*/

                if(strpos($handler,'@')==false){
                    $controller = $handler;
                    $action = 'index';
                }else{
                    $name = explode('@',$handler);
                    $controller = $name[0];
                    $action = $name[1];
                }

                if($controller['0']!='\\') {
                    $controller = str_replace('Controller', '', $controller);
                }

                $map = [
                    'appPath'=>$appPath,
                    'configs' => $this->__configs,
                    'controller'=>$controller.'Controller',
                    'action'=>$action,
                    'currentPage'=>getHttpurl(),
                    'currentUri'=>$uri,
                ];

                $container = $this->__container($appPath);
                $this->__routeBefore($appPath);

                if($controller['0']=='\\'){
                    $app = $controller;
                }else{
                    $app = '\\App\\'.$appName.'\\Controller\\'.ucfirst($controller).'Controller';
                }

                if(!class_exists($app)){
                    report_message([
                        'message'=> '类：'.$app.' 不存在，请创建！',
                        'code'=>'f-3',
                    ]);
                }

                $instance = new $app($map,$container);

                // 方法是否存在
                if(!method_exists($instance,$action)){
                    report_message([
                        'message'=> "方法：$action 不存在，清创建！",
                        'code'=>'f-3',
                    ]);
                }

                $instance->$action($vars);
                $this->__routeAfter($appPath);
                break;
        }
    }

    private function __container($path){
        if(is_file($path.'Container.php')){
            return include $path.'Container.php';
        }
    }

    // 前置操作
    private function __routeAfter($path){
        if(is_file($path . 'RouteAfter.php')){
            include $path . 'RouteAfter.php';
        }
    }

    // 后置操作
    private function __routeBefore($path){
        if (is_file($path . 'RouteBefore.php')){
            include $path . 'RouteBefore.php';
        }
    }

    private function load($dirPath,$return=false){
        if(!is_dir($dirPath)){
            return;
        }
        $dirHandle = opendir($dirPath);
        while(false !== ($file = readdir($dirHandle))){
            if($file != '.' && $file != '..'){
                $filePath = $dirPath.'/'.$file;
                if(is_dir($filePath)){
                    $this->load($filePath,$return);
                }else{
                    $data = include ($filePath);
                    $dirArr = [];
                    if($data && $return==true){
                        if(isset( $data['content'])){
                            $dir = explode(DIRECTORY_SEPARATOR,$dirPath);
                            $dir = array_filter($dir);

                            if(count($dir)>0){
                                if($dir[count($dir)-1]!='Configs'&&$dir[count($dir)-1]!=''){
                                    $dirKey = array_search('Configs', $dir);
                                    foreach ($dir as $key=>$val){
                                        if($key<=$dirKey){
                                            unset($dir[$key]);
                                        }
                                    }
                                }else{
                                    $dir = [];
                                }
                            }

                            $dirNameNew = end($dir);
                            $dirNameNew = str_replace('/',DIRECTORY_SEPARATOR,$dirNameNew);
                            $dirNameNew = explode(DIRECTORY_SEPARATOR,$dirNameNew);
                            $fileName = rtrim($file,'.php');
                            $dirNameNew[] = $fileName;
                            $dirNameNew = array_filter($dirNameNew);
                            $dirNameNew = array_keys(array_flip($dirNameNew));

                            self::$__loadArr[implode('.',$dirNameNew)] = $data['content'];
                        }
                    }
                }
            }
        }
        closedir($dirHandle);
        return self::$__loadArr;
    }
}