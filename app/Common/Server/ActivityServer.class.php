<?php
namespace Common\Server;

/*
+----------------------------------------------------------
* 活动类
+----------------------------------------------------------
*/
class ActivityServer
{
    /**
    +----------------------------------------------------------
    * 获取活动分类
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-21
    +----------------------------------------------------------
    */
    public function get_category()
    {
        $category = D('Category');
        $return = $category->where(array('status'=>0))->field('id,name,icon')->order('sort ASC')->select();
        return $return ? $return : array();
    }

    /**
    +----------------------------------------------------------
    * 获取活动标签
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-21
    +----------------------------------------------------------
    */
    public function get_tag()
    {
        $tag = D('Tag');
        $return = $tag->where(array('status'=>0))->field('id,name')->order('sort ASC')->select();
        return $return ? get_array_by_key($return,'id','name') : array();
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动详情
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_info_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('Activity')->where(array('id'=>$id))->find();
            return $return ? $return : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动的状态
    +----------------------------------------------------------
    * @param  int $begindate 活动开始时间
    +----------------------------------------------------------
    * @param  int $enddate 活动结束时间
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_play_status($begindate,$enddate)
    {
        $nowtime = time();
        //预告
        if($nowtime < $begindate){
            return 1;
        }
        //直播
        if($nowtime >= $begindate && $nowtime <= $enddate){
            return 2;
        }
        //回顾
        if($nowtime > $enddate){
            return 3;
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动的标签
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_tag_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $tid_arr = D('ActivityTag')->where(array('aid'=>$id))->field('tid')->select();
            if($tid_arr){
                $tidIds = implode(',',get_rows_by_array($tid_arr,'tid'));
                $where = array();
                $where['status'] = 0;
                $where['id'] = array('in',$tidIds);
                $return = D('Tag')->where($where)->field('id,name')->select();
            }
            return $return ? get_array_by_key($return,'id','name') : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 通过标签获取活动
    +----------------------------------------------------------
    * @param  int $tid 活动标签ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function get_activity_by_tag($tid)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('ActivityTag')->where(array('tid'=>$tid))->field('aid')->select();
            return $return ? get_rows_by_array($return,'aid') : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动直播
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_cast_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('ActivityCast')->where(array('activityId'=>$id))->find();
            return $return ? $return : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动图片
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_image_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('ActivityImage')->where(array('status'=>0,'activityId'=>$id))->order('postdate DESC')->select();
            return $return ? $return : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动广告
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-25
    +----------------------------------------------------------
    */
    public function get_advert_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('ActivityAdvert')->where(array('status'=>0,'activityId'=>$id))->order('rank ASC')->select();
            return $return ? $return : array();
        }
    }
    
    /**
    +----------------------------------------------------------
    * 获取活动报名
    +----------------------------------------------------------
    * @param  int $id 活动ID
    +----------------------------------------------------------
    * @ author chenfeng
    +----------------------------------------------------------
    * @ date 2016-10-28
    +----------------------------------------------------------
    */
    public function get_order_by_id($id)
    {
        if(!$id || !is_numeric($id)){
            return false;
        }else{
            $return = D('ActivityOrder')->where(array('aid'=>$id))->select();
            return $return ? get_rows_by_array($return,'uid') : array();
        }
    }


    /**
     * 获取推荐的活动
     * @param  int $p 页码
     * @param  string $exceptIds 活动ID
     * @return array  $array  推荐的活动
     * @author vaeling
     * @date 2016-10-31
     */
    public function get_recomment($exceptIds=0,$p='1')
    {
        $activityObj = D('Activity');
        $where=  array('status'=>0,'isrecomment'=>1);
        if(!empty($exceptIds))
            $where['id'] =  array('not in',$exceptIds);
        $return = $activityObj->field('content',true)->where($where)->page($p.','.C('form_limits'))->order('id desc')->limit(20)->select();
        return $return ? $return : array();
    }

    /**
     * 获取分页的推荐的活动
     * @param  int $id 活动id
     * @param  int $page 页码
     * @param  int $pagenum 一页显示数
     * @return array  $array  当前活动的评论
     * @author vaeling
     * @date 2016-10-31
     */
    public function get_comment_by_id($activityId=0,$page,$pagenum)
    {
        $page = !empty($page) && is_numeric($page) ? $page: 1;
        $pagenum = (!empty($pagenum) && is_numeric($pagenum)) ? $pagenum: C('form_limits');

        $commentObj = D('activityComments');
        $where=  array('status'=>1);
        if(!empty($activityId) && is_numeric($activityId))
            $where['aid'] =  $activityId;

        $count =$commentObj->where($where)->count();
        $page = new \Think\Page1($count,$pagenum);
        $commentArr = $commentObj->field('aid,status',true)->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();

        //获取用户数据
        $userIds = get_field_string(get_array_by_rows($commentArr,'uid'),'uid');
        $whereuser = array('uid'=>array('in',$userIds));
        $userArr = get_array_by_rows(D('Member')->field('uid,nickname')->where($whereuser)->select(),'uid');

        foreach($commentArr as $key => $value){

            $user = $userArr[$value['uid']];
            $user['avatar'] = 'http://git.oschina.net/uploads/33/277833_xudao.png?1463561675';
            $commentArr[$key]['user'] = $user;
            unset($commentArr[$key]['uid']);
        }
        $return  = array('list'=>$commentArr,'page'=>$page->show());
        return $return ? $return : array();
    }

    /**
     * 获取分页的推荐的活动
     * @param  int $id 活动id
     * @param  int $page 页码
     * @param  int $pagenum 一页显示数
     * @return array  $array  当前活动的评论
     * @author vaeling
     * @date 2016-10-31
     */
    public function get_activity_orders_by_id($orderId=0,$page,$pagenum)
    {
        $page = !empty($page) && is_numeric($page) ? $page: 1;
        $pagenum = (!empty($pagenum) && is_numeric($pagenum)) ? $pagenum: C('form_limits');

        $orderObj = D('ActivityOrder');
        $where=  array();
        if(!empty($orderId) && is_numeric($orderId))
            $where['aid'] =  $orderId;

        $count =$orderObj->where($where)->count();
        $page = new \Think\Page1($count,$pagenum);
        $orderArr = $orderObj->where($where)->limit($page->firstRow.','.$page->listRows)->select();

        //获取用户数据
        $userIds = get_field_string(get_array_by_rows($orderArr,'uid'),'uid');

        $whereuser = array('uid'=>array('in',$userIds));
        $userArr = get_array_by_rows(D('Member')->field('uid,nickname,job,company')->where($whereuser)->select(),'uid');

        foreach($orderArr as $key => $value){

            $user = $userArr[$value['uid']];
            $user['avatar'] = 'http://git.oschina.net/uploads/33/277833_xudao.png?1463561675';
            $orderArr[$key]['user'] = $user;
            unset($orderArr[$key]['uid']);
        }
        $return  = array('list'=>$orderArr,'page'=>$page->show());
        return $return ? $return : array();
    }

    /**
     *
     * @param  int $id 活动id
     * @param  string $field 更改的字段
     * @param  string $opt 行为
     * @date 2016-10-31
     */
    public function changeDataInField($id,$field,$opt='inc'){

        if(!empty($id) && is_numeric($id)){
            $where['id'] = $id;
            if($opt =='inc'){
                D('Activity')->where($where)->setInc($field);
            }elseif($opt =='dec'){
                D('Activity')->where($where)->setDec($field);
            }
        }
    }
}