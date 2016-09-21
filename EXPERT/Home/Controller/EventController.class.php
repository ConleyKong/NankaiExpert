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
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = ' 科技事件列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '查询所有字段';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
        }
    }

    public function show_picture(){


        $id = $_GET['id'];
        $condition['id'] = $id;
        $event_picture = M('picture');
        $result = $event_picture->where($condition)->select();
        $this->assign('result',$result);
//        dump($result);
        $this->display();

    }

    public function delete()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $condition['id'] = $id;
            $event = M('event');
            $state = $event->where($condition)->delete();
            session('yan',M('event')->getLastSql());
            if($state){$this->success('操作成功！');}
            else{$this->error('操作失败！');}
            ////////////////////////////////////////////////////////////////////////////////

//            $this->display();
//                $this->redirect('Event/index');

        }
    }

    public function select()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            session('time_start',null);
            session('time_end',null);
            session('name',null);
            session('abstract',null);
            session('relevant_person',null);
            session('location',null);
            session('remark',null);
            $id = $_GET['id'];
//            $this->success($id);
            $condition['id'] = $id;
            $event = M('event');
            $result = $event->where($condition)->find();

            $time_start = $result['time_start'];
            $time_end = $result['time_end'];
            $name=$result['name'];
            $abstract = $result['abstract'];
            $relevant_person = $result['relevant_person'];
            $location = $result['location'];
            $remark = $result['remark'];
            session('time_start',$time_start);
            session('time_end',$time_end);
            session('name',$name);
            session('abstract',$abstract);
            session('relevant_person',$relevant_person);
            session('location',$location);
            session('remark',$remark);
            ////////////////////////////////////////////////////////////////////////////////

            $this->display();
//                $this->redirect('Event/index');

        }
    }
    public function add()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $data['time_start'] = I('post.time_start','暂无','string');
            $data['time_end'] = I('post.time_end','暂无','string');
            $data['name'] = I('post.name','暂无','string');
            $data['abstract'] = I('post.abstract',0,'int');
            $data['relevant_person'] = I('post.relevant_person','暂无','string');
            $data['location'] = I('post.location','暂无','email');
            $data['remark'] = I('post.remark','暂无','string');

            $event = M('event');
            $result  = $event->add($data);
            if ($result)
            {
                $this->success('新增成功');
            }
            else
            {
                $this->error('数据插入失败');
            }
            ////////////////////////////////////////////////////////////////////////////////

//            $this->display();
//                $this->redirect('Event/index');

        }
    }
}