<?php
namespace Common\Server;

/*
+----------------------------------------------------------
* 用户类
+----------------------------------------------------------
*/
class MemberServer
{
    
    /**
    +----------------------------------------------------------
    * 生成密码
    +----------------------------------------------------------
    * @param  string $password 原始密码
    +----------------------------------------------------------
    * @param  string $salt 密钥
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function get_password($password,$salt)
    {
        return md5(md5($password).$salt);
    }
    
    /**
    +----------------------------------------------------------
    * 验证密码
    +----------------------------------------------------------
    * @param  string $password 原始密码
    +----------------------------------------------------------
    * @param  string $salt 密钥
    +----------------------------------------------------------
    * @param  string $pwd 加密后密码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function check_password($password,$salt,$pwd)
    {
        if(get_password($password,$salt) == $pwd){
            return true;
        }else{
            return false;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  获取用户信息
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function get_user()
    {
        $user = session('user');
        if($user){
            return $user;
        }else{
            return 0;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  获取用户uid
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function get_user_uid()
    {
        $user = session('user');
        if($user){
            return $user['uid'];
        }else{
            return 0;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  获取用户角色
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function get_user_role()
    {
        $user = session('user');
        if($user){
            return $user['role'];
        }else{
            return 0;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  用户中心邮箱验证
    +----------------------------------------------------------
     * @param  string $email 邮箱
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function check_ucenter_email($email)
    {
        if(!$email){
            return false;
        }else{
            $header = array('Auth-Info: '.base64_encode('live:wt123465'));
            $url = 'http://sso.busionline.com/isemailreg';
            $output = json_decode(gcurl($url,$header,array('email'=>$email)),true);
            return $output;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  用户中心手机验证
    +----------------------------------------------------------
     * @param  string $phone 手机号码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function check_ucenter_phone($phone)
    {
        if(!$phone){
            return false;
        }else{
            $header = array('Auth-Info: '.base64_encode('live:wt123465'));
            $url = 'http://sso.busionline.com/isphonereg';
            $output = json_decode(gcurl($url,$header,array('phone'=>$phone)),true);
            return $output;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  用户中心用户注册
    +----------------------------------------------------------
    * @param  string $username 手机或者邮箱
    +----------------------------------------------------------
    * @param  string $password 密码
    +----------------------------------------------------------
    * @param  int $point 默认0手机注册，1邮箱注册
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function add_ucenter_user($username,$password,$point=0)
    {
        if(!$username || !$password){
            return false;
        }else{
            $get = array();
            $point == 1 && $temp = explode('@', $username);
            $get['nickname'] = $point == 1 ? $temp[0] : 'live'.$username;
            $get['email'] = $point == 1 ? $username : $username.'@busionline.com';
            $get['password'] = $password;
            $point == 0 && $get['mobile'] = $username;
            $header = array('Auth-Info: '.base64_encode('live:wt123465'));
            $url = 'http://sso.busionline.com/registerApi';
            $output = json_decode(gcurl($url,$header,$get),true);
            return $output;
        }
    }
    
    /**
    +----------------------------------------------------------
    * 用户中心用户密码修改
    +----------------------------------------------------------
    * @param  string $nickname 用户名
    +----------------------------------------------------------
    * @param  string $newpwd 新密码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-26
    +----------------------------------------------------------
    */
    function reset_ucenter_pwd($nickname,$newpwd)
    {
        if(!$nickname || !$newpwd){
            return false;
        }else{
            $header = array('Auth-Info: '.base64_encode('live:wt123465'));
            $url = 'http://sso.busionline.com/changePwd';
            $output = json_decode(gcurl($url,$header,array('nickname'=>$nickname,'newpwd'=>$newpwd)),true);
            return $output;
        }
    }
    
    /**
    +----------------------------------------------------------
    *  用户中心用户登陆
    +----------------------------------------------------------
    * @param  array $get array('username','password')
    +----------------------------------------------------------
    */
    function ucenter_login($username,$password)
    {
        if(!$username || !$password){
            return false;
        }else{
            $header = array('Auth-Info: '.base64_encode('train:wt123465'));
            $url = 'http://sso.busionline.com/loginApi';
            $output = json_decode(gcurl($url,$header,array('username'=>$username,'password'=>$password)),true);
            return $output;
        }
    }
    
}