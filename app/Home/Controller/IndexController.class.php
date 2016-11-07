<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    
    public function _initialize()
    {
        if(!method_exists($this,strtolower(ACTION_NAME)) || !method_exists($this,ACTION_NAME)){
            $this->redirect('index/index');
        }
    }
    
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
        $advert_arr = D('Advert')->where(array('status'=>0))->field('subject,url,icon')->order('rank ASC')->limit(4)->select();
        
        $activity = A('Activity','Server');
        //最近直播
        $db = D('Activity');
        $where = array();
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['begindate'] = array('gt',time());
        $newly = $db->where($where)->field('id,subject,imgpath,begindate,address')->order('begindate ASC')->find();
        if($newly){
            $newly['date_time'] = $newly['begindate'] * 1000;
            $newly['order_count'] = count($activity->get_order_by_id($newly['id']));
        }
        
        $this->assign('advert_arr', $advert_arr);
        $this->assign('newly', $newly);
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * 播放器
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function player()
    {
        $vid = I('get.vid');
        $activity = A('Activity','Server');
        $info = $activity->get_info_by_id($vid);
        if($info['status'] == 9){
            exit('该直播已经关闭！');
        }
        
        $cast = $activity->get_cast_by_id($info['id']);
        if($cast['state'] != 1){
            exit('该直播已经关闭！');
        }
        
        $data = array();
        $data['title'] = $info['subject'];
        $data['poster'] = $info['imgpath'];
        
        $play_status = $activity->get_play_status($info['begindate'],$info['enddate']);
        $data['status'] = 0;
        if($play_status == 1){
            $data['enddate'] = $info['begindate']*1000;
            $data['nowdate'] = time() * 1000;
            $data['stream'] = $cast['hlsurl'];
        }
        if($play_status == 2){
            $data['status'] = 1;
            $data['stream'] = $cast['hlsurl'];
        }
        if($play_status == 3){
            $data['status'] = 1;
            $data['stream'] = $cast['recordurl'];
        }
        
        $this->assign('data', $data);
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * IPEI播放器
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function ipei()
    {
        $activity = D('Activity');
        $today = getdate();
        $begintime = mktime(0,0,0,$today['mon'],$today['mday'],$today['year']);
        $endtime = $begintime + 24*3600;
        //预告
        $where = array();
        $where['iscast'] = 1;
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['begindate'] = array(array('gt',$begintime),array('lt',$endtime));
        $list = $activity->where($where)->field('id,subject,begindate,enddate')->order('begindate ASC')->limit(8)->select();
        $nowtime = time();
        if($list){
            foreach($list as $key => $value){
                $list[$key]['timespan'] = $nowtime < $value['enddate'] ? $value['enddate'] - $nowtime : $value['enddate'] - $value['begindate'];
            }
        }
        $this->assign('list', $list);
        $this->display();
    }
}
