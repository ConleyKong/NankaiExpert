<?php
namespace Home\Controller;
use Think\Controller;
class ProjectController extends Controller {

    public function index()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    public function ProjectList()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            unset($param['page']);
            unset($param['items']);

            $startTime = $param['start_time'];
            $endTime = $param['end_time'];
            $startPoint = array();
            if ($startTime){
                $startPoint = array('gt',$startTime);
                unset($param['start_time']);
            }
            if ($endTime){
                $startPoint = array('lt',$endTime);
                unset($param['end_time']);
            }
            if ($startPoint)
                $query['start_time'] = $startPoint;

            if($param['name']){
                $query['name'] = array('like','%'.$param['name'].'%');
                unset($param['name']);
            }
            if($param['person_name']){
                $query['person_name'] = array('like','%'.$param['person_name'].'%');
                unset($param['person_name']);
            }
            if($param['depth_flag']){
                $query['depth_flag'] = $param['depth_flag'];
                unset($param['depth_flag']);
            }

            $string='';
//            if ($param['projecttype_id']){
//                $string .= $string ? ' AND ('.$param['projecttype_id'].')' : '('.$param['projecttype_id'].')';
//                unset($param['projecttype_id']);
//            }
            if ($param['type_id']){
                $string .= $string ? ' AND ('.$param['type_id'].')' : '('.$param['type_id'].')';
                unset($param['type_id']);
            }
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if($param['keyword']){
                $keyword = $param['keyword'];
                $ts = " (project.name like '%$keyword%' OR person.name like '%$keyword%') ";
                $string .= $string?' AND '.$ts:$ts;
                unset($param['keyword']);
            }
            if ($string)
                $query['_string'] = $string;

            $query["valid"]=true;

            $Project = D('ProjectView');
            $result = $Project->where($query)->page($pageNum,$itemsNum)->order('project.id')->select();
            $prepData = $Project->where($query)->getField('fund',true);
            $Project->getLastSql();
            $result[0]['totalNum'] = sizeof($prepData);
            $result[0]['totalFund'] = number_format(array_sum($prepData),3);
//            $result[0]['totalFund'] = $prepData[1];
//            $totalNum = $Project->where($query)->count();
//            $result[0]['totalNum'] = $totalNum;
//            $result[0]['totalFund2']= $Project->where($query)->sum('fund');
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '项目列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '查询所有字段';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
        }
    }


    //获取项目类型
    public function getProjectTypeList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $projecttype = M('project_type');
            $result = $projecttype->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    }

    public function getTypeList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $projecttype = M('project_type');
            $result = $projecttype->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    }


    public function getProjectTypeCount()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
    		$project = D('ProjectView');
			$type = $project->field(array('count(project.type_id) typecount','type_name'))->group('type_id')->select();
			$support = $project->field(array('count(support_no) supportcount','support_no'))->group('support_no')->select();
            $result = array();
            $result['type'] = $type;
            $result['support'] = $support;
            $this->ajaxReturn($result, 'json');
		}
    }

    public function getProjectTrend(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $project = M('project');
            
        }
    }

    /*
     * 删除指定project
     * 将project的valid置为false
     */
    public function delete()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $condition['id'] = $id;
//            $condition['valid']=true;
            $project = M('project');
            $p = $project->where($condition)->find();
            $p['valid']=false;
            $state = $project->where($condition)->save($p);
            $name = $p["name"];
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '项目列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "删除项目: $id: $name ";


            if($state>0){
                $this->success('操作成功！');
                $audit['result'] = '成功';
                M('audit')->add($audit);
            }
            else{
                $audit['result'] = '失败';
                M('audit')->add($audit);
                $this->error('操作失败！');
            }
            ////////////////////////////////////////////////////////////////////////////////

//            $this->display();
//                $this->redirect('Event/index');

        }
    }


    //导入导出
    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{

            $type = I('get.type');//$param['type'];
            $field = I('get.field');// $param['field'];

            $page = I('get.page');
            $items = I('get.items');
            $pageNum = $page? $page : 1;
            $itemsNum =  $items? $items : 10;
//            $academichonor_id = I('get.academichonor_id');
//            $college_id = I('get.college_id');
//            $gender = urldecode(I('get.gender'));
//            $postdoctor = I('get.postdoctor');

            $startTime = I('get.start_time');
            $endTime = I('get.end_time');
            $startPoint = array();
            if ($startTime)
                $startPoint = array('gt',$startTime);
            if ($endTime)
                $startPoint = array('lt',$endTime);
            if ($startPoint)
                $query['start_time'] = $startPoint;

            $depth_flag = I('get.depth_flag');
            if($depth_flag){
                $query['depth_flag']=$depth_flag;
            }

            $name = I('get.name');
            if ($name)
                $query['name'] = array('like','%'.$name.'%');
            $person_name = I('get.person_name');
            if ($person_name)
                $query['person_name']= array('like','%'.$person_name.'%');


            $string = '';
//            $projecttype_id = I('get.projecttype_id');
//            if ($projecttype_id)
//                $string .= $string ? ' AND ('.$projecttype_id.')' : '('.$projecttype_id.')';
            $type_id = I('get.type_id');
            if ($type_id)
                $string .= $string ? ' AND ('.$type_id.')' : '('.$type_id.')';

            $college_id = I('get.college_id');
            if ($college_id)
                $string .= $string ? ' AND ('.$college_id.')' : '('.$college_id.')';
            $keyword = I('get.keyword');
            if($keyword){
                $ts = " (project.name like '%$keyword%' OR person.name like '%$keyword%') ";
                $string .= $string?' AND '.$ts:$ts;
            }
            if ($string)
                $query['_string'] = $string;

            $query['valid']=true;

            $project = D('ProjectView');

            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $project->field($field)->where($query)->order('project.id')->select();
            }else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $result = $project->field($field)->where($query)->page($pageNum,$itemsNum)->order('project.id')->select();
            }else{
                return '未知错误';
            }

            $titleMap = array('person_name'=>'拥有者','name'=>'项目名称','type_name'=>'类型','depth_flag'=>'是否纵向','support_no'=>'支助编号','join_unit'=>'参与单位','source'=>'项目来源','source_department'=>'来源单位','subtype'=>'子类','start_time'=>'开始时间','end_time'=>'结束时间','fund'=>'经费','direct_fund'=>'直接经费','indirect_fund'=>'间接经费','financial_account'=>'金融账户','comment'=>'备注');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '项目信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '项目列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出项目列表';
            M('audit')->add($audit);

        }
    }

    public function import(){
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {
            //如果文件非空
            if (! empty ( $_FILES ['file_import'] ['name'] ))
            {
                //导入person表
                $tmp_file = $_FILES ['file_import'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['file_import'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                /*判别是不是.xls文件，判别是不是excel文件*/
                $excel_type = array("xls","xlsx");
                $extension = strtolower ( $file_type );
                if (!in_array($extension,$excel_type))
                {
                    $this->error ( '不是Excel文件，请重新选择文件！' );
                }
                /*设置上传路径*/
                $savePath = './Public/upfile/Excel/';
                /*以时间来命名上传的文件*/
                $str = date ( 'Ymdhis' );
                $file_name = "project_info_".$str . "." . $file_type;

                /*是否上传成功*/
                if (! copy ( $tmp_file, $savePath . $file_name ))
                {
                    $this->error ( "服务器错误，上传失败！（error in peoplecontroller） " );
                }
                /*
                   *对上传的Excel数据进行处理生成编程数据,这个函数会在下面第三步的ExcelToArray类中
                  注意：这里调用执行了第三步类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
                */
                $res = readExcel( $savePath . $file_name ,$extension);
                /*
                     重要代码 解决Thinkphp M、D方法不能调用的问题
                     如果在thinkphp中遇到M 、D方法失效时就加入下面一句代码
                 */
                //spl_autoload_register ( array ('Think', 'autoload' ) );

                /*存储临时数组，缓存少部分数据*/
                $type_buffer = array();
                $college_buffer = array();

                //统计导入的结果
                $insert_counter=0;//成功插入的新数据数
                $inserted_id = array();//成功插入的记录的id
                $update_counter=0;//更新的数据数
                $updated_id=array();//记录成功更新的数据的id
                $error_counter=0;//插入失败的数据数
                $errored_name=array();//记录插入失败的专家的姓名

                /*对生成的数组进行数据库的写入*/
                foreach ( $res as $k => $v )
                {
                    if ($k != 0)
                    {


                        $project = M("project");
                        $data = array();
                        $data["name"]=$v[0];//项目名
                        $data["project_no"]= $v[1];//项目编号
                        $manager_eno = $v[2];//项目负责人职工号//TODO
                        $college_name = trim($v[3]);//所属学院名//TODO
                        $data["source"]=$v[4];//项目来源
                        $data["start_time"]=$v[5];//起始时间
                        $data["end_time"]=$v[6];//截止时间
                        $data["direct_fund"]=$v[7];//直接经费
                        $data["indirect_fund"]=$v[8];//间接经费
                        $data["fund"]=$v[9];//经费

                        $data["financial_account"]=$v[10];//金融帐号
                        $type_name = trim($v[11]);//项目类型//TODO
                        $data["support_no"]=$v[12];//资助编号
                        $data["source_department"]=$v[13];//来源单位
                        $data["depth_flag"] = $v[14];//纵向标记，汉字


                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理项目负责人职工号$manager_eno
                            $manager_id = getPersonIdByEmployeeNo(trim($manager_eno));
                            if($manager_id>0){
                                $data["manager_id"] = $manager_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（项目负责人职工号不存在）";
                                continue;
                            }


                        //处理所属学院名
                            $college_id = getForeignKey($college_name,"college",$college_buffer);
                            if($college_id>0){
                                $data["college_id"] = $college_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（学院信息不存在）";
                                continue;
                            }

                        //处理项目类型
                            $type_id = getForeignKey($type_name,"project_type",$type_buffer);
                            if($type_id>0){
                                $data["type_id"] = $type_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（项目类型不存在）";
                                continue;
                            }

                        $condition["manager_id"] =$data["manager_id"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $project->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $project->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;
                        }else{
                            //插入数据
                            $result = $project->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }
                        if (empty($result))
                        {
                            $error_counter++;
                            $errored_name[]=$data["name"]."(插入失败)";
                        }

                    }
                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入项目信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '科研项目';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的项目名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的项目id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的项目id： ";
                    foreach ($updated_id as $id){
                        $descr .= $id.",";
                    }
                }
                $audit['descr'] .= $descr;
                M('audit')->add($audit);

                if($error_counter==0){
                    $this->success("导入工作成功（详情见日志）",'',3);
                }else{
                    $this->error("存在导入失败的记录，请查看日志进行修正！",'',30);
                }

            }

            $this->error("文件选择失败！");

        }
    }


}