<?php


namespace Peach2\Framework\Mvc\Model;


use Peach2\Framework\Mvc\Session\Session;
use function Peach2\Framework\Functions\report_message;

class LoginModel extends Model
{
    public function login($input,$lang){
        $rs = $this->_instance->where('Username',$input['username'])->getOne('system_user');
        if (isset($rs['ID'])){
            $lock = Session::instance()->get('LOCK');
            if($lock>5){
                Session::instance()->set('LOCK',$lock,3600*12);
                report_message([
                    'message'=> $lang['gzhybsd'].'！',
                    'code'=>'l-m-0',
                ]);
            }

            if($rs['Status']==-1){
                report_message([
                    'message'=> $lang['gzhjzdl'].'！',
                    'code'=>'l-m-1',
                ]);
            }

            if($rs['Status']==0){
                report_message([
                    'message'=> $lang['gzhyjy'].'！',
                    'code'=>'l-m-2',
                ]);
            }

            if($rs['Status']==2){
                report_message([
                    'message'=> $lang['gzhwtgglysh'].'！',
                    'code'=>'l-m-3',
                ]);
            }

            if(!password_verify($input['password'],$rs['Password'])){
                $lock = Session::instance()->get('LOCK');
                if(!$lock){
                    $lock = 0;
                }

                $lock = $lock+1;
                Session::instance()->set('LOCK',$lock,3600*12);

                report_message([
                    'message'=> $lang['yhmhmmcw'].'！',
                    'code'=>'l-m-4',
                ]);
            }

            Session::instance()->set('LOCK',0,0);

            return $rs;
        }else{
            report_message([
                'message'=> $lang['yhmhmmcw'].'！',
                'code'=>'l-m-4',
            ]);
        }
    }
}