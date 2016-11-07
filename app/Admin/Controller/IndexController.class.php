<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    
    /**
    +----------------------------------------------------------
    * 首页
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-21
    +----------------------------------------------------------
    */
    public function index()
    {
        //$notice = D('Notice');
        $activity = A('Activity','Server');
        print_r($activity->get_tag());exit;
    }
}