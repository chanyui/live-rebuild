<?php
namespace Common\Model;
use Think\Model;
class ActivityModel extends Model {

    /*是否直播*/
    const  CAST_YES = 1;//是
    const  CAST_NO = 0;//否
    
    protected $tableName = 'activity'; 
    
}
