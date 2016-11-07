<?php
namespace Home\Controller;
use Think\Controller;
class ActivityController extends Controller {
    
    public function _initialize()
    {
        if(!method_exists($this,strtolower(ACTION_NAME)) || !method_exists($this,ACTION_NAME)){
            $this->redirect('index/index');
        }
    }
    
    /**
    +----------------------------------------------------------
    * 列表
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function index()
    {
        $cast = I('get.cast') ? I('get.cast') : 0;
        $sort = I('get.sort') ? I('get.sort') : 0;
        $slive = I('get.slive') ? intval(I('get.slive')) : 0;
        $tag = I('get.tag') ? intval(I('get.tag')) : 0;
        $order = I('get.order');
        
        $cast_arr = C('cast_arr');
        $sort_arr = C('sort_arr');
        $slive_arr = C('play_stauts_arr');
        $activity = A('Activity','Server');
        $tag_arr = $activity->get_tag();
        $order_arr = C('order_arr');
        
        $db = D('Activity');
        $where = array();
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['ischeck'] = 1;
        if(array_key_exists($cast,$cast_arr)){
            $cast == 'n' && $where['iscast'] = 0;
            $cast == 'y' && $where['iscast'] = 1;
        }
        if(array_key_exists($sort,$sort_arr)){
            $sort == 1 && $where['isexample'] = 1;
        }
        if(array_key_exists($slive,$slive_arr)){
            $nowtime = time();
            if($slive == 1){
                $where['begindate'] = array('gt',$nowtime);
            }
            if($slive == 2){
                $where['begindate'] = array('lt',$nowtime);
                $where['enddate'] = array('gt',$nowtime);
            }
            if($slive == 3){
                $where['enddate'] = array('lt',$nowtime);
            }
        }
        if(array_key_exists($tag,$tag_arr)){
            $aid_arr = $activity->get_activity_by_tag($tag);
            $where['id'] = array('in',$aid_arr);
        }
        
        $order = array_key_exists($order,$order_arr) ? $order : 'time';
        if($order == 'time'){
            $_order = 'begindate DESC';
        }else{
            $_order = 'praises DESC';
        }
        
        $count = $db->where($where)->count();
        $page  = new \Think\Page1($count,12);// 实例化分页类 传入总记录数
        $show = $page->show();
        $list = $db->where($where)->order($_order)->limit($page->firstRow.','.$page->listRows)->select();
        foreach($list as $key => $value){
            $list[$key] = $this->_extendInfoInActivity($value);

        }
        
        $this->assign('cast',$cast);
        $this->assign('cast_arr',$cast_arr);
        $this->assign('sort',$sort);
        $this->assign('sort_arr',$sort_arr);
        $this->assign('slive',$slive);
        $this->assign('slive_arr',$slive_arr);
        $this->assign('tag',$tag);
        $this->assign('tag_arr',$tag_arr);
        $this->assign('order',$order);
        $this->assign('order_arr',$order_arr);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * 活动直播
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function cast()
    {
        $sort = I('post.sort') ? I('post.sort') : 0;
        
        $nowtime = time();
        $db = D('Activity');
        $where = array();
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['ischeck'] = 1;
        $where['begindate'] = array('lt',$nowtime);
        $where['enddate'] = array('gt',$nowtime);
        switch ($sort) {
            case 1:
                $where['iscast'] = 1;
                $where['isexample'] = 1;
            break;
            case 2:
                $where['iscast'] = 0;
                $where['isexample'] = 1;
            break;
            default:
            break;
        }
        $castlist = $db->where($where)->order('isexample ASC,begindate ASC')->limit(6)->select();
        
        $this->assign('cast',$cast);
        $this->assign('sort',$sort);
        $this->assign('castlist',$castlist);
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * 活动预告
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function foreshow()
    {
        $sort = I('post.sort') ? I('post.sort') : 0;
        
        $db = D('Activity');
        $where = array();
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['ischeck'] = 1;
        $where['begindate'] = array('gt',time());
        switch ($sort) {
            case 1:
                $where['iscast'] = 0;
                $where['isexample'] = array('neq',1);
            break;
            case 2:
                $where['iscast'] = 1;
                $where['isexample'] = array('neq',1);
            break;
            case 3:
                $where['iscast'] = 1;
                $where['isexample'] = 1;
            break;
            case 4:
                $where['iscast'] = 0;
                $where['isexample'] = 1;
            break;
            default:
            break;
        }
        $foreshow = $db->where($where)->order('isexample ASC,begindate ASC')->limit(6)->select();

        foreach ($foreshow as $k => $v) {
            $foreshow[$k] = $this->_extendInfoInActivity($v);
        }

        $this->assign('sort',$sort);
        $this->assign('foreshow',$foreshow);
        $this->display();
    }
    
    /**
    +----------------------------------------------------------
    * 精彩回顾
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function review()
    {
        $sort = I('post.sort') ? I('post.sort') : 0;
        
        $db = D('Activity');
        $where = array();
        $where['status'] = 0;
        $where['isshow'] = 1;
        $where['enddate'] = array('lt',time());
        switch ($sort) {
            case 1:
                $where['iscast'] = 1;
                $where['isexample'] = 1;
            break;
            case 2:
                $where['iscast'] = 0;
                $where['isexample'] = 1;
            break;
            default:
            break;
        }
        $review = $db->where($where)->order('begindate DESC')->limit(3)->select();
        
        $this->assign('sort',$sort);
        $this->assign('review',$review);
        $this->display();
    }

    /**
     * 搜索结果
     * @ function_name search
     * @ author yucheng
     */
    public function search()
    {
        $keyword = I('get.keyword');
        if (!$keyword || $keyword == '') {
            $this->error('请输入关键词');
            exit;
        }
        $activity = A('Activity', 'Server');
        $where = array();
        $where['subject'] = array('like', '%' . $keyword . '%');
        $where['isshow'] = 1;
        $where['status'] = 0;
        $count = D('Activity')->where($where)->count();
        $limit = 9;
        $page = new \Think\Page1($count, $limit);
        $show = $page->show();
        $result = D('Activity')->where($where)->order('postdate desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($result as $k => $v) {
            $result[$k] = $this->_extendInfoInActivity($v);
        }
        $this->assign('result', $result);
        $this->assign('page', $show);
        $this->assign('keyword', $keyword);
        $this->assign('count', $count);
        $this->display();
    }

    /**
     * 详情
     * @ function_name activityDetail
     * @ author yucheng
     * @ return bool
     */
    public function activityDetail()
    {
        $id = I('get.id');
        if (!$id || !is_numeric($id)) {
            return false;
            exit;
        } else {
            $activity = A('Activity','Server');
            //获取活动详情
            $info = $activity->get_info_by_id($id);
            if ($info) {
                $info = $this->get_status($info);
            }
            $info['clicknums']=$activity->changeDataInField($id,'clicknums');
            $info_category = get_array_by_rows($activity->get_category(),'id');
            $info['category'] = $info_category[$info['cateId']];
            $info['order_count'] = count($activity->get_order_by_id($id));
            //活动介绍
            $info['content'] = htmlspecialchars_decode($info['content']);
            //var_dump($info);

            //报名人列表



            //评论






            //推荐活动
            $recommend = D('Activity');
            $where = array();
            $where = array('isrecomment' => 1, 'status' => 0, 'isshow' => 1);
            $where['id'] = array('not in', $id);
            $return = $recommend->where($where)->order('id desc')->limit(3)->select();
            if ($return) {
                foreach ($return as $k => $v) {
                    $return[$k] = $this->get_status($v);
                }
            }
            $this->assign('info',$info);
            $this->assign('recommend', $return);
            $this->display();
        }
    }

    /**
     * 获取活动状态
     * @ param $info
     * @ function_name get_status
     * @ author yucheng
     * @ return mixed
     */
    public function get_status($info)
    {
        $activity = A('Activity', 'Server');
        $activity_statusArr = C('play_stauts_arr');
        $info['play_status'] = $activity->get_play_status($info['begindate'], $info['enddate']);
        $info['playstatus'] = $activity_statusArr[$info['play_status']];
        return $info;
    }


    /**
     * 编辑活动字段
     * @author vaeling
     * @date 2016-10-31
     */
    private function _extendInfoInActivity($info)
    {
        $activityAct = A('Activity','Server');
        //获取状态
        $playStautsArr = C('play_stauts_arr');
        $info['play_status']  = $playStatus =  $activityAct->get_play_status($info['begindate'],$info['enddate']);
            $info['playStatus'] =array('key'=>$playStatus,'value'=>$playStautsArr[$playStatus]);

        //时间
        $info['begindateMsec'] = $info['begindate'] * 1000;

        //用户登录信息
        $user = A('Member','Server')->get_user();
        $user = !empty($user) ? $user:  array();
        $this->assign('user',$user);
        $this->assign('islogin',!empty($user));

        //报名信息
        $enrollCount = 0;
        if($user){
            $whereEnroll['uid'] = $user['uid'];
            $whereEnroll['aid'] = $info['id'];
            $enrollCount =D('ActivityOrder')->where($whereEnroll)->count();
        }
        $info['enroll'] = $enrollCount ? '已报名' : '立即报名';

        return $info;

    }


}

