<?php
namespace Home\Controller;
use Think\Controller;
class EventController extends Controller {
    public function index()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    public function EventList()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            $Event = M('event');
            $result = $Event->page($pageNum,$itemsNum)->order('Event.time_start desc')->select();

            session('yan',M('event')->getLastSql());
            $totalNum = $Event->count();
            $result[0]['totalNum'] = $totalNum;

            $this->ajaxReturn($result,'json');
        }
    }

    public function show_picture(){


        $id = $_GET['id'];
        $condition['event_id'] = $id;
        $event_picture = M('picture');
        $result = $event_picture->where($condition)->select();
        $this->assign('result',$result);
//        dump($result);
        $this->display();

    }
}