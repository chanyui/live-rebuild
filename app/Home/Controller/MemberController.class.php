<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends Controller {
    
    public function _initialize()
    {
        if(!method_exists($this,strtolower(ACTION_NAME)) || !method_exists($this,ACTION_NAME)){
            $this->redirect('index/index');
        }
    }
    
    /**
    +----------------------------------------------------------
    * 注册
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function register()
    {
        if(IS_POST){
            $regist_type = I('post.regist-type');
            $phone = I('post.phone');
            $phoneCode = I('post.phoneCode');
            $email = I('post.email');
            $password = I('post.password');
            $confirm_password = I('post.confirm_password');
            
            if(!in_array($regist_type,array(1,2))){
                $this->error('请选择正确的注册方式！');
                exit;
            }
            if(!check_string_length($password,6,16)){
                $this->error('请填写正确的密码！');
                exit;
            }
            if($password != $confirm_password){
                $this->error('两次密码输入不一致！');
                exit;
            }
            //手机注册
            if($regist_type == 1){
                if(!preg_match('/^[0-9]{11}$/',$phone)){
                    $this->error('请填写正确的手机！');
                    exit;
                }else{
                    $session = session('phoneCode');
                    $session = authcode($session,'DECODE',C('secret_key'));
                    list($old_phone,$old_code,$old_time) = explode('\t',$session);
                    if($old_phone != $phone || $old_code != $phoneCode || time()-$old_time > 600){
                        $this->error('验证码错误！');
                        exit;
                    }else{
                        session('phoneCode',null);
                        $ucenter_user = A('Member','Server')->add_ucenter_user($phone,$password);
                        if($ucenter_user['code'] != 200){
                            $this->error('注册失败！');
                            exit;
                        }else{
                            session('user',array('uid'=>$ucenter_user['data'],'nickname'=>'live'.$phone,'role'=>0));
                            $this->redirect('member/perfect');
                            exit;
                        }
                    }
                }
            }
            //邮箱注册
            if($regist_type == 2){
                if(!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',$email)){
                    $this->error('请填写正确的邮箱！');
                    exit;
                }else{
                    $memberActive = D('MemberActive');
                    $memberActive->email = $email;
                    $memberActive->password = authcode($password,'ENCODE',C('secret_key'));
                    $memberActive->postdate = time();
                    if($id = $memberActive->add()){
                        $token = $id.'\t'.time().'\t'.random(6);
                        $token = authcode($token,'ENCODE',C('secret_key'));
                        if(A('Email','Server')->active_by_email($email,$token)){
                            $this->redirect('member/result', array('email' => $email));
                        }else{
                            $this->error('注册失败！');
                            exit;
                        }
                    }else{
                        $this->error('注册失败！');
                        exit;
                    }
                }
            }
        }else{
            $user = A('Member','Server')->get_user();
            if($user){
                if($user['role'] == 1){
                    $this->redirect('index/index');
                    exit;
                }
                if($user['role'] == 2){
                    $this->redirect('admin/index');
                    exit;
                }
            }else{
                $this->display();
            }
        }
    }
    
    /**
    +----------------------------------------------------------
    * 手机号码验证
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function checkPhone()
    {
        $phone = I('post.phone');
        
        $member =A('Member','Server');
        $output = $member->check_ucenter_phone($phone);
        switch ($output['code']) {
            case 500:
                $return['code'] = 500;
                $return['msg'] = '请填写正确的手机！';
            break;
            case 200:
                $return['code'] = 200;
                $return['msg'] = '该手机已经被注册！';
            break;
            case 202:
                $return['code'] = 202;
                $return['msg'] = "该手机未注册！";
            break;
            default:
                $return['code'] = 9;
                $return['msg'] = '手机号码验证失败！';
        }
        $this->ajaxReturn($return);
        exit;
    }
    
    /**
    +----------------------------------------------------------
    *手机发送验证码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function sendCode()
    {
        $phone = I('post.phone');
        
        $return = '';
        if(!$phone){
            $return['code'] = 500;
            $return['msg'] = "请填写手机号码！";
        }else{
            $session = session('phoneCode');
            $session = authcode($session,'DECODE',C('secret_key'));
            list($old_phone,$old_code,$old_time) = explode('\t',$session);
            if($old_phone == $phone && time()-$old_time < 60){
                $return['code'] = 7;
                $return['msg'] = "请不要频繁的发送验证码，60秒后再试！";
            }else{
                $code = random(6,1);
                $message = '您的“来福Live Online”验证码：'.$code.'，10分钟内有效。';
                
                $send = D('Send');
                $data = array();
                $data['phone'] = $phone;
                $data['code'] = $code;
                $data['msg'] = $message;
                $data['ip'] = get_client_ip();
                $data['postdate'] = time();
                $data['istrue'] = 1;
                if($codeid = $send->add($data)){
                    $output = A('Phone','Server')->send_by_phone($phone,$message);
                    if($output == true){
                        $session = $phone.'\t'.$code.'\t'.time();
                        $session = authcode($session,'ENCODE',C('secret_key'));
                        session('phoneCode',$session);
                        $return['code'] = 0;
                        $return['msg'] = "发送成功！";
                    }else{
                        $send->where(array('id'=>$codeid))->save(array('istrue'=>0));
                        $return['code'] = 9;
                        $return['msg'] = "验证码发送失败！";
                    }
                }else{
                    $return['code'] = 9;
                    $return['msg'] = "验证码发送失败！";
                }
            }
        }
        
        $this->ajaxReturn($return);
        exit;
    }
    
    /**
    +----------------------------------------------------------
    * 邮箱验证
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function checkEmail()
    {
        $email = I('post.email');
        
        $member =A('Member','Server');
        $output = $member->check_ucenter_email($email);
        switch ($output['code']) {
            case 500:
                $return['code'] = 500;
                $return['msg'] = '请填写正确的邮箱！';
            break;
            case 200:
                $return['code'] = 200;
                $return['msg'] = '该邮箱已经被注册！';
            break;
            case 202:
                $return['code'] = 202;
                $return['msg'] = "该邮箱未注册！";
            break;
            default:
                $return['code'] = 9;
                $return['msg'] = '邮箱验证失败！';
        }
        $this->ajaxReturn($return);
        exit;
    }
    
    /**
    +----------------------------------------------------------
    * 完善信息
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function perfect()
    {
        $user = A('Member','Server')->get_user();
        if(empty($user)){
            $this->redirect('member/login');
            exit;
        }
        if($user['role'] != 0){
            $this->error('您已经完善信息！',U('index/index'));
            exit;
        }
        if(IS_POST){
            $role_type = I('post.role_type');
            $company = I('post.company');
            $job = I('post.job');
            
            if(!in_array($role_type,array(1,2))){
                $this->error('请选择角色！');
                exit;
            }
            
            $member = D('Member');
            //个人
            if($role_type == 1){
                if(!$company){
                    $this->error('请填写您所在公司名称！');
                    exit;
                }
                if(!$job){
                    $this->error('请填写您所处职务！');
                    exit;
                }
                $member->company = $company;
                $member->job = $job;
            }
            $uid = $user['uid'];
            $nickname = $user['nickname'];
            $member->role = $role_type;
            $member->uid = $uid;
            $member->nickname = $nickname;
            $member->regip = get_client_ip();
            $member->regdate = time();
            $member->lastip = get_client_ip();
            $member->lastdate = time();
            if($member->add()){
                session('user',null);
                session('user',array('uid'=>$uid,'nickname'=>$nickname,'role'=>$role_type));
                if($role_type == 2){
                    $this->redirect('admin/index');
                    exit;
                }else{
                    $this->redirect('index/index');
                    exit;
                }
            }else{
                $this->error('完善信息失败！');
                exit;
            }
        }else{
            $this->display();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 登录
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function login()
    {
        if(IS_POST){
            $username = I('post.username');
            $password = I('post.password');
            $rember = I('post.rember');
            if(!$username){
                $this->error('请输入用户名/邮箱/手机！');
                exit;
            }
            if(!check_string_length($password,6,16)){
                $this->error('请输入正确的密码！');
                exit;
            }
            $output = A('Member','Server')->ucenter_login($username,$password);
            if($output['code'] != 200){
                $this->error('用户名或者密码错误！');
                exit;
            }else{
                if($rember == 1){
                    $cookie = $username.'\t'.$password.'\t'.time();
                    $cookie = authcode($cookie,'ENCODE',C('secret_key'));
                    cookie('userLogin',$cookie,array('expire'=>5*24*3600));
                }
                $member = D('Member')->where(array('uid'=>$output['data']['uid']))->find();
                $role = $member ? $member['role'] : 0;
                session('user',array('uid'=>$output['data']['uid'],'nickname'=>$output['data']['nickname'],'role'=>$role));
                switch ($role){
                    case 0:
                        $this->redirect('member/perfect');
                    break;
                    case 1:
                        $this->redirect('index/index');
                    break;
                    case 2:
                        $this->redirect('admin/index');
                    break;
                    default:
                    break;
                }
            }
        }else{
            $user = A('Member','Server')->get_user();
            if(empty($user)){
                $cookie = cookie('userLogin');
                if($cookie){
                    $cookie = authcode($cookie,'DECODE',C('secret_key'));
                    list($username,$password,$lastdate) = explode('\t',$cookie);
                    $output = A('Member','Server')->ucenter_login($username,$password);
                    if($output['code'] == 200){
                        $member = D('Member')->where(array('uid'=>$output['data']['uid']))->find();
                        $role = $member ? $member['role'] : 0;
                        session('user',array('uid'=>$output['data']['uid'],'nickname'=>$output['data']['nickname'],'role'=>$role));
                        switch ($role){
                            case 0:
                                $this->redirect('member/perfect');
                            break;
                            case 1:
                                $this->redirect('index/index');
                            break;
                            case 2:
                                $this->redirect('admin/index');
                            break;
                            default:
                            break;
                        }
                    }
                }
            }else{
                switch ($user['role']){
                    case 0:
                        $this->redirect('member/perfect');
                    break;
                    case 1:
                        $this->redirect('index/index');
                    break;
                    case 2:
                        $this->redirect('admin/index');
                    break;
                    default:
                    break;
                }
            }
            $this->display();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 退出
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-31
    +----------------------------------------------------------
    */
    public function logout()
    {
        session(NULL);
        $this->redirect('index/index');
        exit;
    }
    
    /**
    +----------------------------------------------------------
    * 忘记密码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-02
    +----------------------------------------------------------
    */
    public function forgetpwd()
    {
        if(IS_POST){
            $regist_type = I('post.regist-type');
            $phone = I('post.phone');
            $phoneCode = I('post.phoneCode');
            $email = I('post.email');
            
            if(!in_array($regist_type,array(1,2))){
                $this->error('请选择正确的注册方式！');
                exit;
            }
            //手机认证
            if($regist_type == 1){
                if(!preg_match('/^[0-9]{11}$/',$phone)){
                    $this->error('请填写正确的手机！');
                    exit;
                }else{
                    $session = session('phoneCode');
                    $session = authcode($session,'DECODE',C('secret_key'));
                    list($old_phone,$old_code,$old_time) = explode('\t',$session);
                    if($old_phone != $phone || $old_code != $phoneCode || time()-$old_time > 600){
                        $this->error('验证码错误！');
                        exit;
                    }else{
                        session('phoneCode',null);
                        $ucenter_user = A('Member','Server')->check_ucenter_phone($phone);
                        if($ucenter_user['code'] != 200){
                            $this->error('认证失败！');
                            exit;
                        }else{
                            $token = $ucenter_user['data']['uid'].'\t'.time().'\t'.random(6);
                            $token = authcode($token,'ENCODE',C('secret_key'));
                            S($token,$ucenter_user['data'],600);
                            $this->redirect('member/resetpwd',array('token'=>urlencode($token)));
                            exit;
                        }
                    }
                }
            }
            //邮箱注册
            if($regist_type == 2){
                if(!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',$email)){
                    $this->error('请填写正确的邮箱！');
                    exit;
                }else{
                    $ucenter_user = A('Member','Server')->check_ucenter_email($email);
                    if($ucenter_user['code'] != 200){
                        $this->error('认证失败！');
                        exit;
                    }else{
                        $token = $ucenter_user['data']['uid'].'\t'.time().'\t'.random(6);
                        $token = authcode($token,'ENCODE',C('secret_key'));
                        if(A('Email','Server')->resetpwd_by_email($email,$token)){
                            S($token,$ucenter_user['data'],1800);
                            $this->redirect('member/result', array('email' => $email));
                        }else{
                            $this->error('认证失败！');
                            exit;
                        }
                    }
                }
            }
        }else{
            $this->display();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 重置密码
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-02
    +----------------------------------------------------------
    */
    public function resetpwd()
    {
        $token = urldecode(I('post.token')) ? urldecode(I('post.token')) : I('get.token');
        $user = S($token);
        if(empty($user)){
            $this->error('验证码过期！',U('member/forgetpwd'));
            exit;
        }
        if(IS_POST){
            $password = I('post.password');
            $confirm_password = I('post.confirm_password');
            
            if(!check_string_length($password,6,16)){
                $this->error('请填写正确的密码！');
                exit;
            }
            if($password != $confirm_password){
                $this->error('两次密码输入不一致！');
                exit;
            }
            $output = A('Member','Server')->reset_ucenter_pwd($user['name'],$password);
            if($output['code'] == 200){
                S($token,null);
                $this->redirect('member/revise');
                exit;
            }else{
                $this->error('重置失败！',U('member/resetpwd',array('token'=>urlencode($token))));
                exit;
            }
        }else{
            $this->assign('token',urlencode($token));
            $this->display();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 重置成功
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-02
    +----------------------------------------------------------
    */
    public function revise()
    {
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * 邮件激活
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-03
    +----------------------------------------------------------
    */
    public function active()
    {
        $token = I('get.token');
        $token = authcode($token,'DECODE',C('secret_key'));
        list($id,$old_time,$old_code) = explode('\t',$token);
        if(time() - $old_time > 24*3600){
            $this->error('已经过期！',U('index/index'));
            exit;
        }
        $memberAtive = D('MemberActive')->where(array('id'=>$id))->find();
        if(!$memberAtive){
            $this->redirect('index/index');
            exit;
        }
        if($memberAtive['isactive'] == 1){
            $this->error('已经激活！',U('index/index'));
            exit;
        }
        $ucenter_user = A('Member','Server')->add_ucenter_user($memberAtive['email'],authcode($memberAtive['password'],'DECODE',C('secret_key')),1);
        if($ucenter_user['code'] != 200){
            $this->error('激活失败！');
            exit;
        }else{
            $temp = explode('@', $memberAtive['email']);
            D('MemberActive')->where(array('id'=>$id))->save(array('isactive'=>1));
            session('user',array('uid'=>$ucenter_user['data'],'nickname'=>$temp[0],'role'=>0));
            unset($temp);
            $this->redirect('member/perfect');
            exit;
        }
    }
    
    /**
    +----------------------------------------------------------
    * 邮件发送成功
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-03
    +----------------------------------------------------------
    */
    public function result()
    {
        $email = I('get.email');
        if(!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',$email)){
            $this->redirect('index/index');
            exit;
        }
        $temp = explode('@', $email);
        $url = 'http://mail.'.$temp[1];
        unset($temp);
        
        $this->assign('email',$email);
        $this->assign('url',$url);
        $this->display();
    }
}

