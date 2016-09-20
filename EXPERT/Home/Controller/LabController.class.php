<?php
namespace Home\Controller;
use Think\Controller;
class LabController extends Controller {
    public function index()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    public function labList()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数

            $string = '';
            if ($param['manager_id']){
                $string .= $string ? ' AND ('.$param['manager_id'].')' : '('.$param['manager_id'].')';
                unset($param['manager_id']);
            }
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }

            if ($string)
                $param['_string'] = $string;

            $Lab = D('LabView');
            unset($param['page']);
            unset($param['items']);
            $result = $Lab->where($param)->page($pageNum,$itemsNum)->order('Lab.id')->select();
            $totalNum = $Lab->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '科研平台列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
        }
    }


    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $type = I('get.type');
            $query = I('get.query');
            $field = I('get.field');
            deleteEmptyValue($query);
            $pageNum = $query['page'] ? $query['page'] : 1;
            $itemsNum =  $query['items'] ? $query['items'] : 10;
            $string = '';
            if ($query['formed_start'] && $query['formed_end'])
                $query['formed_time'] = array(array('gt',$query['formed_start']),array('lt',$query['formed_end']));
//
            if ($query['college_id']){
                $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
                //unset($query['college_id']);
            }
            if ($string){
                $query['_string'] = $string;
            }

            $Lab = D('LabView');
            $query = objectToArray($query);
            unset($query['formed_start']);
            unset($query['formed_end']);
            unset($query['page']);
            unset($query['items']);
            if ($type == 'all'){
                $result = $Lab->field($field)->where($query)->order('Lab.id')->select();
            }
            else if ($type == 'current'){
                $result = $Lab->field($field)->where($query)->page($pageNum,$itemsNum)->order('Lab.id')->select();
            }
            else
                return '未知错误';
            $titleMap = array('name'=>'平台名称','manager_name'=>'负责人姓名','location'=>'地址','formed_time'=>'成立时间','college_name'=>'所属院系','member'=>'成员','description'=>'描述');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '平台信息';
            exportExcel($filename,$field, $result, $excelTitle);
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '平台信息';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出平台信息';
            M('audit')->add($audit);

        }
    }

    /**
     *
     * 导出Excel
     */
    function expUser()
    {//导出Excel
        $xlsName = "User";
        $xlsCell = array(
            array('id', '账号序列'),
            array('truename', '名字'),
            array('sex', '性别'),
            array('res_id', '院系'),
            array('sp_id', '专业'),
            array('class', '班级'),
            array('year', '毕业时间'),
            array('city', '所在地'),
            array('company', '单位'),
            array('zhicheng', '职称'),
            array('zhiwu', '职务'),
            array('jibie', '级别'),
            array('tel', '电话'),
            array('qq', 'qq'),
            array('email', '邮箱'),
            array('honor', '荣誉'),
            array('remark', '备注')
        );
        $xlsModel = M('Member');

        $xlsData = $xlsModel->Field('id,truename,sex,res_id,sp_id,class,year,city,company,zhicheng,zhiwu,jibie,tel,qq,email,honor,remark')->select();
        foreach ($xlsData as $k => $v) {
            $xlsData[$k]['sex'] = $v['sex'] == 1 ? '男' : '女';
        }
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }


    }