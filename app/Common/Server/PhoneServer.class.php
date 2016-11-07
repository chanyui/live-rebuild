<?php
namespace Common\Server;

/*
+----------------------------------------------------------
* 手机类
+----------------------------------------------------------
*/
class PhoneServer
{
    
    /**
    +----------------------------------------------------------
    * 手机发送验证码
    +----------------------------------------------------------
    * @param  string $phone 手机号码
    +----------------------------------------------------------
    * @param  string $message 发送信息
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function send_by_phone($phone,$message)
    {
        if(!$phone || !$message){
            return false;
        }else{
            $post_data = array();
            $post_data['account'] = 'Hzwt888';
            $post_data['pswd'] = 'Hzwt888888';
            $post_data['mobile'] = $phone;
            $post_data['needstatus']="true";
            $post_data['msg']=mb_convert_encoding($message,'UTF-8', 'UTF-8');
            $url='http://222.73.117.158/msg/HttpBatchSendSM?'; 
            
            $output = xcurl($url,'',$post_data);
            $output = preg_replace('/\s/', ',', $output);
            $output = explode(',',$output);
            if($output[1] == 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
}