<?php

namespace Peach2\Framework\Mvc\Cache;

class PageCache
{
    protected static $path = null;
    protected static $obj = null;
    public static function instance($configs=[]){
        if (isset($configs['path'])){
            self::$path = $configs['path'];
        }else{
            self::$path = ROOT_PATH . 'variable' . DIRECTORY_SEPARATOR .'html';
        }
        self::$path = rtrim(self::$path,'/');
        self::$path = rtrim(self::$path,'\\');
        self::$path .= DIRECTORY_SEPARATOR;
        if (!is_dir(self::$path)){
            mkdir(self::$path,0777,true);
        }
        return self::$obj = new PageCache();
    }

    // 获取缓存
    public function get($name){
        $searchfile = $this->searchFiles(self::$path,$name);
        if (isset($searchfile[0])){
            $expires = explode('_',$searchfile['0']);
            if (isset($expires[count($expires)-1])){
                $expire = rtrim($expires[count($expires)-1],'.html');
                if($expire==1||$expire>time()){
                    return file_get_contents($searchfile[0]);
                }
            }
        }

        return null;
    }

    public function bindVar($html,$var){
        $htmls = $html;
        foreach ($var as $key=>$val){
            $htmls = str_replace($key,$val,$htmls);
        }
        return $htmls;
    }

    // 设置缓存
    public function set($name,$value,$expire=true){
        $searchfile = $this->searchFiles(self::$path,$name);
        foreach ($searchfile as $val){
            unlink($val);
        }
        $file = self::$path.$name.'_'.$expire.'.html';
        file_put_contents($file,$value);
    }

    function searchFiles($folder, $keyword) {
        $files = scandir($folder);
        $result = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $folder . '/' . $file;

                if (is_dir($path)) {
                    $result = array_merge($result, searchFiles($path, $keyword));
                } elseif (strpos($file, $keyword) !== false) {
                    $result[] = $path;
                }
            }
        }

        return $result;
    }
}