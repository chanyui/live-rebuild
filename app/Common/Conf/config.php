<?php
return array(
    /*默认数据库配置*/
    'DB_TYPE'  => 'mysqli',// 数据库类型
    'DB_HOST'  => '121.40.125.156',
    'DB_NAME'  => 'live_busionline_com',// 数据库名称
    'DB_USER'  => 'root',// 数据库用户名
    'DB_PWD'  => 'sys123',// 数据库密码
    'DB_PREFIX'  => 'live_',// 数据表前缀
    'DB_CHARSET' => 'utf8',// 网站编码
    'DB_PORT'  => '3306',// 数据库端口
    
    //七牛存储配置文件
    'UPLOAD_SITEIMG_QINIU' => array (
        'maxSize' => 200 * 1024 * 1024,
        'rootPath' => './',
        'saveName' => array ('uniqid', ''),
        'driver' => 'Qiniu',
        'driverConfig' => array (
            'secrectKey' => '1sHgOVv7Cz1LH_hURWT0kCphXQ4eSzjGo2yXTIba',
            'accessKey' => 'u-XjYbEN6dPgDm1juSF-5EDSGSY6DxoPfJWa7StH',
            'domain' => 'source.ckcloud.busionline.com',
            'bucket' => 'ckcloud'
        ),
    ),
    
    'form_limits' => 20, //列表每一页数量
    //邮件发送配置
    'SENDMAIL' => array (
        'mail_type' => 'smtp',//smtp|mail|sendmail
        'mail_auth' => true,
        'mail_server' => 'smtp.hzwotu.com',
        'mail_port' => '25',
        'mail_user' => 'live@hzwotu.com',
        'mail_password' => 'live0909!@',
        'mail_from' => 'live@hzwotu.com',
        'site_name' => '来福直播',
    ),
    
    'secret_key'=>'bd4e76c7ee591c5b77659af00e589a0a',
    
    'play_stauts_arr' => array(1=>'预告',2=>'直播',3=>'回顾'),
    'weekend' =>array('日','一','二','三','四','五','六'),
    'sort_arr'=>array(1 =>'两创示范活动'),
    'cast_arr'=>array('n' =>'不直播','y'=>'直播'),
    'order_arr'=>array('time' =>'时间','hot'=>'热门'),
);
