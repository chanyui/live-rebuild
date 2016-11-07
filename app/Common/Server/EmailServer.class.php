<?php
namespace Common\Server;

/*
+----------------------------------------------------------
* 邮箱类
+----------------------------------------------------------
*/
class EmailServer
{
    
    /**
    +----------------------------------------------------------
    * 邮箱激活
    +----------------------------------------------------------
    * @param  string $email 邮箱
    +----------------------------------------------------------
    * @param  string $token 发送信息token
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-01
    +----------------------------------------------------------
    */
    function active_by_email($email,$token)
    {
        if(!$email || !$token){
            return false;
        }else{
            $temp = explode('@',$email);
            $url = 'http://live2.busionline.com/member/active?token='.urlencode($token);
            
            $subject = '来福直播激活邮箱帐号';
            
            $body = '<p>
                        <strong><span style="font-size:19px;font-family:&#39;微软雅黑&#39;,&#39;sans-serif&#39;">【'.$temp[0].'】您好</span></strong>
                     </p>
                     <p>感谢您申请来福直播账号，点击链接即可激活您的账号，</p>
                     <br>
                     <p><a href="'.$url.'">'.$url.'</a></p>
                     <br>
                     <p>为保障您的帐号安全，请在24小时内点击该链接，您也可以将链接复制到浏览器地址栏访问。</p>
                     <p>如果您并未尝试激活邮箱，请忽略本邮件，由此给您带来的不便请谅解。</p>
                     <p>本邮件由系统自动发出，请勿直接回复！</p>
                     ';
            
            if(sendmail($email, $subject, $body)){
                return true;
            }else{
                return false;
            }
        }
    }
    
    /**
    +----------------------------------------------------------
    * 找回密码
    +----------------------------------------------------------
    * @param  string $email 邮箱
    +----------------------------------------------------------
    * @param  string $token 发送信息token
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-11-01
    +----------------------------------------------------------
    */
    function resetpwd_by_email($email,$token)
    {
        if(!$email || !$token){
            return false;
        }else{
            $temp = explode('@',$email);
            $url = 'http://live2.busionline.com/member/resetpwd?token='.urlencode($token);
            
            $subject = '来福直播找回邮箱密码';
            
            $body = '<p>
                        <strong><span style="font-size:19px;font-family:&#39;微软雅黑&#39;,&#39;sans-serif&#39;">亲爱的来福直播用户：【'.$temp[0].'】您好</span></strong>
                     </p>
                     <p>您在访问来福直播时点击了“忘记密码”链接，这是一封密码重置确认邮件。</p>
                     <p>您可以通过点击以下链接重置帐户密码：</p>
                     <br>
                     <p><a href="'.$url.'">'.$url.'</a></p>
                     <br>
                     <p>为保障您的帐号安全，请在24小时内点击该链接，您也可以将链接复制到浏览器地址栏访问。</p>
                     <p>若如果您并未尝试修改密码，请忽略本邮件，由此给您带来的不便请谅解。</p>
                     <br>
                     <p>本邮件由系统自动发出，请勿直接回复！</p>
                     ';
            
            if(sendmail($email, $subject, $body)){
                return true;
            }else{
                return false;
            }
        }
    }
}