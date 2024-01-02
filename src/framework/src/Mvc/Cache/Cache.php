<?php

namespace Peach2\Framework\Mvc\Cache;

class Cache
{
    protected static $path = null;
    protected static $obj = null;
    public static function instance($configs=[]){
        if (isset($configs['path'])){
            self::$path = $configs['path'];
        }else{
            self::$path = ROOT_PATH . 'variable' . DIRECTORY_SEPARATOR .'cache';
        }
        self::$path = rtrim(self::$path,'/');
        self::$path = rtrim(self::$path,'\\');
        self::$path .= DIRECTORY_SEPARATOR;
        if (!is_dir(self::$path)){
            mkdir(self::$path,0777,true);
        }
        return self::$obj = new Cache();
    }

    // 获取缓存
    public function get($name){
        $file = self::$path.$name.'.php';
        if(is_file($file)){
            $cache = include ($file);
            if(isset($cache['content'])){
                if(isset($cache['expire'])){
                    if ($cache['expire']==true||$cache['expire']>time()){
                        return $cache['content'];
                    }
                }
            }
            return null;
        }else{
            return null;
        }
    }

    // 设置缓存
    public function set($name,$value,$expire=true){
        $file = self::$path.$name.'.php';
        $data = [
            'content'=>$value,
            'expire'=>time()+$expire,
        ];

        if ($expire==true){
            $data['expire'] = $expire;
        }

        file_put_contents($file,'<?php return '.var_export($data,true).';');
    }
}