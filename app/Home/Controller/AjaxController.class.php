<?php
namespace Home\Controller;

use Think\Controller;

class AjaxController extends Controller
{
    
    /*
     *   0   => '成功',
     *   9   => '失败',
     *   300 => '请求方式错误',
     *   400 => '接口不存在',
     *   500 => '没有权限',
     *   700 => '参数错误',
    */
    
    public function _initialize()
    {  
        if(!method_exists($this,strtolower(ACTION_NAME))){
            $this->ajaxReturn(array('code'=>400));
            exit;
        }else{

        }
    }
    

    /**
    * 获取活动评论
    * @param int aid 必选参数 活动ID
    * @param int p 必选参数 页码
    */
    public function getCommentByActivityId()
    {

        $aid = I('aid');
        $page = I('p') ? I('p') : 1;
        $pagenum = I('pnum') ? I('pnum') : 5;

        $activityAct = A('Activity','Server');
        $commentArr =$activityAct->get_comment_by_id($aid,$page,$pagenum);

        $this->assign('list',$commentArr['list']);
        $this->assign('pagenum',$pagenum);
        $this->assign('page',$commentArr['page']);
        $this->assign('aid',$aid);

        $this->display();

    }


    /**
     * 获取报名人列表
     * @param int aid 必选参数 活动ID
     * @param int p 必选参数 页码
     */
    public function getActivityOrderByActivityId()
    {
        $aid = I('aid');
        $page = I('p') ? I('p') : 1;
        $pagenum = I('pnum') ? I('pnum') : 5;

        $activityAct = A('Activity','Server');

        $activityOrderArr = $activityAct->get_activity_orders_by_id($aid,$page,$pagenum);

        $this->assign('list',$activityOrderArr['list']);
        $this->assign('pagenum',$pagenum);
        $this->assign('page',$activityOrderArr['page']);
        $this->assign('aid',$aid);

        $this->display();

    }

    /**
     * 对活动进行关注(暂不用)
     * @param int aid 必选参数 活动ID
     * @param int p 必选参数 页码
     */
    function followActivity()
    {
        $aid = I('get.aid');
        $tel = I('get.tel');
        if(!$aid || !is_numeric($aid) || !$tel || !is_numeric($tel)){
            $return['code'] = 700;
            $this->ajaxReturn($return);
        }

        $where['aid'] = $aid;
        $where['phone'] = $tel;
        $lineMembersObj = D('LineMembers');
        if($lineMembersObj->where($where)->count() > 0){
            $return['code'] = 0;
            $return['data'] = '已关注过';
            $this->ajaxReturn($return);
        }
        $data['aid'] = $aid;
        $data['phone'] = $tel;
        $data['status'] = 0;
        $data['addtime'] = time();
        if($lineMembersObj->add($data)){
            $return['code'] = 0;
            $return['data'] = '关注成功';
        }else{
            $return['code'] = 9;
        }
        $this->ajaxReturn($return);

    }

    /**
     * 对活动进行评论
     * @param int aid 必选参数 活动ID
     * @param string content 必选参数 评论内容
     * @param int isanonymity 必选参数 是否匿名
     */
    function comment()
    {
        $aid = I('get.aid');
        $content = I('get.content');
        $isanonymity = I('get.isanonymity');
        if(!$aid || !is_numeric($aid)){
            $return['code'] = 700;
            $this->ajaxReturn($return);
        }

        $commentObj = D('ActivityComments');
        $memberAct = A('Member','Server');
        $uid = $memberAct->get_user_uid();
        if(!$uid){
            $return['code'] = 9;
            $return['data'] = '获取用户数据失败';
            $this->ajaxReturn($return);
        }
        $lastComment = $commentObj->field('aid,status',true)->where('uid='.$uid)->order('id desc')->find();
        if(time() - $lastComment['postdate'] < 10){
            $return['code'] = 9;
            $return['data'] = '评论太快了';
            $this->ajaxReturn($return);
        }

        $data['aid'] = $aid;
        $data['uid'] = $uid;
        $data['content'] = $content;
        $data['isanonymity'] = $isanonymity;
        $data['status'] = 1;
        $data['postdate'] = time();
        if($newId = $commentObj->add($data)){
            $newComment = $commentObj->field('aid,status',true)->where('id='.$newId)->find();
            $return['code'] = 0;
            $return['data'] = $newComment;
        }else{
            $return['code'] = 9;
            $return['data'] = '评论失败';
        }
        $this->ajaxReturn($return);

    }


    /**
     * 报名
     * @param int aid 必选参数 活动ID
     * @param int uid 必选参数 用户id
     */
    function addActivityOrders()
    {
        $aid = I('get.aid');

        $memberAct = A('Member','Server');
        $uid = $memberAct->get_user_uid();


        if(!$aid || !is_numeric($aid)){
            $return['code'] = 700;
            $this->ajaxReturn($return);
        }

        if(!$uid){
            $return['code'] = 9;
            $return['data'] = '获取用户数据失败';
            $this->ajaxReturn($return);
        }

        $activityOrdersObj = D('ActivityOrder');

        $data['uid'] = $uid;
        $data['aid'] = $aid;

        if($activityOrdersObj->where($data)->count() > 0){
            $return['code'] = 200;
            $return['data'] = '已报名';
            $this->ajaxReturn($return);
        }

        $data['postdate'] = time();
        if($activityOrdersObj->add($data)){
            $return['code'] = 0;
            $return['data'] = '报名成功';
        }else{
            $return['code'] = 9;
            $return['data'] = '报名失败';
        }
        $this->ajaxReturn($return);


    }


}
