<?php
namespace Peach2\Framework\Functions;

use DI\Definition\ArrayDefinition;

function report_message($data, $isDie=true){
    if(IsAjax()||IsPost()){
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($data));
    }else{
        if(defined('APP_PATH')){
            $appPath = APP_PATH . 'View' . DIRECTORY_SEPARATOR . 'Prompt.phtml';
        }else{
            $appPath =  ROOT_PATH . 'app'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Home'.DIRECTORY_SEPARATOR.'View' . DIRECTORY_SEPARATOR . 'Prompt.phtml';
        }

        extract($data);
        if(is_file($appPath)){
            include ($appPath);
        }
    }
    if ($isDie){
        die();
    }
}

function is_session_started() {
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

function load($fileName){
    if(is_file($fileName)){
        $configs = include $fileName;
        if($configs['expire']==true||$configs['expire']>=time()){
            return $configs['content'];
        }
    }

    return null;
}

function saveConfig($fileName,$configs,$expire=true){
    $data['content'] = $configs;
    $data['expire'] = $expire;
    file_put_contents($fileName,'<?php return $configs='.var_export($data,true).';');
}

function formatBytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) {
        $size /= 1024;
    }
    return round($size, 2).$units[$i];
}

function createGuid($namespace = '') {
    static $guid = '';

    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['PHP_SELF'];
    $data .= $_SERVER['REMOTE_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $data .= generateGuid($namespace);
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = '{' .
        substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12) .
        '}';
    return $guid;
}

/**
 * @param $prefix
 * @return string
 * 生成唯一id
 */
function generateGuid($prefix=''){
    //假设一个机器id
    $machineId = mt_rand(100000,999999);

    //41bit timestamp(毫秒)
    $time = floor(microtime(true) * 1000);

    //0bit 未使用
    $suffix = 0;

    //datacenterId  添加数据的时间
    $base = decbin(pow(2,40) - 1 + $time);

    //workerId  机器ID
    $machineid = decbin(pow(2,9) - 1 + $machineId);

    //毫秒类的计数
    $random = mt_rand(1, pow(2,11)-1);

    $random = decbin(pow(2,11)-1 + $random);
    //拼装所有数据
    $base64 = $suffix.$base.$machineid.$random;
    //将二进制转换int
    $base64 = bindec($base64);

    $id = sprintf('%.0f', $base64);

    return $prefix.$id;
}

function loadFiles($dirPath,$return=false){
    global $__loadArr;
    if(!is_dir($dirPath)){
        return;
    }
    $dirHandle = opendir($dirPath);
    while(false !== ($file = readdir($dirHandle))){
        if($file != '.' && $file != '..'){
            $filePath = $dirPath.'/'.$file;
            if(is_dir($filePath)){
                loadFiles($filePath,$return);
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

                        if($file=='auth.php'){
                            $fileName = 'auth';
                        }else{
                            $fileName = rtrim($file,'.php');
                        }

                        $dirNameNew[] = $fileName;

                        $dirNameNew = array_filter($dirNameNew);
                        $dirNameNew = array_keys(array_flip($dirNameNew));

                        $__loadArr[implode('.',$dirNameNew)] = $data['content'];
                    }
                }
            }
        }
    }
    closedir($dirHandle);
    return $__loadArr;
}

function maktimes($time){
    $t=time()-$time;
    $f=array(
        '31536000'=> '年',
        '2592000' => '个月',
        '604800'  => '周',
        '86400'   => '天',
        '3600'    => '小时',
        '60'      => '分钟',
        '1'       => '秒'
    );
    foreach ($f as $k=>$v){
        if (0 !=$c=floor($t/(int)$k)){
            return $c.$v.'前';
        }
    }
}

function vtime($time){
    $output = '';
    foreach (array(86400 => '天', 3600 => '小时', 60 => '分', 1 => '秒') as $key => $value) {
        if ($time >= $key) $output .= floor($time/$key) . $value;
        $time %= $key;
    }
    return $output;
}

function Sec2Time($time){
    if(is_numeric($time)){
        $value = array(
            "years" => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if($time >= 31556926){
            $value["years"] = floor($time/31556926);
            $time = ($time%31556926);
        }
        if($time >= 86400){
            $value["days"] = floor($time/86400);
            $time = ($time%86400);
        }
        if($time >= 3600){
            $value["hours"] = floor($time/3600);
            $time = ($time%3600);
        }
        if($time >= 60){
            $value["minutes"] = floor($time/60);
            $time = ($time%60);
        }
        $value["seconds"] = floor($time);
        //return (array) $value;
        $t=$value["years"] ."年". $value["days"] ."天"." ". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
        Return $t;

    }else{
        return (bool) FALSE;
    }
}