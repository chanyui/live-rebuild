<?php
/**
 +----------------------------------------------------------
 *  cURL功能
 +----------------------------------------------------------
 *  @param  string $url 地址
 +----------------------------------------------------------
 *  @param  string $ref 包含一个”referer”头的字符串
 +----------------------------------------------------------
 *  @param  array $post 参数
 +----------------------------------------------------------
 */
function xcurl($url,$ref=null,$post=array(),$ua="Mozilla/5.0 (X11; Linux x86_64; rv:2.2a1pre) Gecko/20110324 Firefox/4.2a1pre",$print=false) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    if(!empty($ref)) {
        curl_setopt($ch, CURLOPT_REFERER, $ref);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($ua)) {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    }
    if(count($post) > 0){
        $o = "";
        foreach ($post as $k=>$v)
        {
            $o .= "$k=".urlencode($v)."&";
        }
        $post = substr($o,0,-1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);    
    }
    $output = curl_exec($ch);
    curl_close($ch);
    if($print) {
        print($output);
    } else {
        return $output;
    }
}

/**
 +----------------------------------------------------------
 *  cURL功能（get）
 +----------------------------------------------------------
 *  @param string $url 地址
 +----------------------------------------------------------
 *  @param string $header 包含一个”referer”头的字符串
 +----------------------------------------------------------
 *  @param array $get 参数
 +----------------------------------------------------------
 */
function gcurl($url,$header=array(),$get=array(),$ua="Mozilla/5.0 (X11; Linux x86_64; rv:2.2a1pre) Gecko/20110324 Firefox/4.2a1pre",$print=false) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    if(!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if(count($get) > 0){
        $o = "";
        foreach ($get as $k=>$v)
        {
            $o .= "$k=".urlencode($v)."&";
        }
        $get = substr($o,0,-1);
        $url = $url.'?'.$get;   
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($ua)) {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    }
    
    $output = curl_exec($ch);
    curl_close($ch);
    if($print) {
        print($output);
    } else {
        return $output;
    }
}

/**
 +----------------------------------------------------------
 * 加密解密（可逆）
 +----------------------------------------------------------
 * @param  string $string 加密的字符串
 +----------------------------------------------------------
 * @param  string $operation DECODE表示解密,其它表示加密
 +----------------------------------------------------------
 * @param  string $key 密钥
 +----------------------------------------------------------
 * @param  string $expiry 密文有效期 
 +----------------------------------------------------------
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) 
{
    $ckey_length = 4;
    $key = md5($key ? $key : "da7b4db15be94a4c597a34f9cf902b01");
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }

}

/**
 +----------------------------------------------------------
 * 获取随机码
 +----------------------------------------------------------
 * @param  int $length 随机码的长度
 +----------------------------------------------------------
 * @param  int $numeric 0是字母和数字混合码，不为0是数字码
 +----------------------------------------------------------
 */
function random($length, $numeric = 0) {
    PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
    $seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 +----------------------------------------------------------
 * 获取数组中某个键名的所有的键值
 +----------------------------------------------------------
 * @param  array $array 数组
 +----------------------------------------------------------
 * @param  string $row 键名
 +----------------------------------------------------------
 */
function get_rows_by_array( $array , $row) 
{
    $result = array();
    foreach ( $array as $k => $value) {
        $result[] = $value[$row];
    }
    return $result;
}

/**
 +----------------------------------------------------------
 * 以数组中某个键名作为数组的键名得到新的数组
 +----------------------------------------------------------
 * @param  array $array 数组
 +----------------------------------------------------------
 * @param  string $row 键名
 +----------------------------------------------------------
 */
function get_array_by_rows( $array , $row)
{
    $result = array();
    foreach ( $array as $k => $value) {
        $result[$value[$row]] = $value;
    }
    return $result;
}

/**
 +----------------------------------------------------------
 * 以数组中的两个键值分别作为数组的键名，键值得到新的数组
 +----------------------------------------------------------
 * @param  array $array 数组
 +----------------------------------------------------------
 * @param  string $key1 键名
 +----------------------------------------------------------
 * @param  string $key2 键名
 +----------------------------------------------------------
 */
function get_array_by_key($array , $key1 ,$key2)
{
    $result = array();
    foreach ( $array as $k => $value) {
        $result[$value[$key1]] = $value[$key2];
    }
    return $result;
}

/**
+----------------------------------------------------------
 * 拼接select方法数组中的字段值
+----------------------------------------------------------
 * @param  array $array 数组
+----------------------------------------------------------
 * @param  string $field 键名
+----------------------------------------------------------
 * @param  string $way 拼接符号
+----------------------------------------------------------
 */
function get_field_string($array, $field = 'id', $way = ',')
{
    foreach ($array as $k => $v) {
        $return[] = $v[$field];
    }
    $fieldStr = implode($way, $return);
    return $fieldStr;
}

/**
 +----------------------------------------------------------
 * 判断字符串的字符个数
 +----------------------------------------------------------
 * @param  string $string 字符串
 +----------------------------------------------------------
 * @param  int $min 最小长度
 +----------------------------------------------------------
 * @param  int $max 最大长度
 +----------------------------------------------------------
 */
function check_string_length($string , $min , $max)
{
    $len = strlen($string);
    preg_match_all("/./us", $string, $match);
    $len = count($match[0]);
    if($len < $min || $len > $max){
        return 0;
    }else{
        return $len;
    }
}

/**
+----------------------------------------------------------
* 根据时间戳获取中文周几
+----------------------------------------------------------
* @ param int $time
+----------------------------------------------------------
 * @ author yucheng
+----------------------------------------------------------
* @ return string
+----------------------------------------------------------
*/
function get_weekend($time)
{
    $weekend_cn = array('日', '一', '二', '三', '四', '五', '六');
    return '周' . $weekend_cn[date('w', $time)];
}

/**
+----------------------------------------------------------
 * 发送邮件
+----------------------------------------------------------
 * @param  string $tomail 接收者邮箱
+----------------------------------------------------------
 * @param  string $subject 邮件标题
+----------------------------------------------------------
 * @param  int string $body 邮件内容
+----------------------------------------------------------
 * @param  array array $config 邮件发送方的配置
+----------------------------------------------------------
 */
function sendmail($tomail, $subject, $body, $config = '')
{
    $config = C('SENDMAIL');


    $mail = new \Org\SendMail\PHPMailer();

    if ($config['mail_type'] == 'smtp') {
        $mail->IsSMTP();
    } elseif ($config['mail_type'] == 'mail') {
        $mail->IsMail();
    } else {
        if ($config['sendmailpath']) {
            $mail->Sendmail = $config['mail_sendmail'];
        } else {
            $mail->Sendmail = ini_get('sendmail_path');
        }
        $mail->IsSendmail();
    }
    if ($config['mail_auth']) {
        $mail->SMTPAuth = true; // 开启SMTP认证
    } else {
        $mail->SMTPAuth = false; // 开启SMTP认证
    }

    $mail->PluginDir = LIB_PATH . "Org/SendMail/";
    $mail->CharSet = 'utf-8';
    $mail->SMTPDebug = false;        // 改为2可以开启调试
    $mail->Host = $config['mail_server'];      // GMAIL的SMTP
    //$mail->SMTPSecure = "ssl"; // 设置连接服务器前缀
    //$mail->Encoding = "base64";
    $mail->Port = $config['mail_port'];    // GMAIL的SMTP端口号
    $mail->Username = $config['mail_user']; // GMAIL用户名,必须以@gmail结尾
    $mail->Password = $config['mail_password']; // GMAIL密码
    //$mail->From ="742857043@qq.com";
    //$mail->FromName = "杭州沃土教育股份有限公司";
    $mail->SetFrom($config['mail_from'], $config['site_name']);     //发送者邮箱
    $mail->AddAddress($tomail); //可同时发多个
    //$mail->AddReplyTo('742857043@qq.com', 'wotu'); //回复到这个邮箱
    //$mail->WordWrap = 50; // 设定 word wrap
    //$mail->AddAttachment("/var/tmp/file.tar.gz"); // 附件1
    //$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // 附件2
    $mail->IsHTML(true); // 以HTML发送
    $mail->Subject = $subject;
    $mail->Body = $body;

    //$mail->AltBody = "This is the body when user views in plain text format";     //纯文字时的Body
    if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}



