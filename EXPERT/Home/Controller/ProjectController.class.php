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
            $string='';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

            $Paper = D('ProjectView');
            unset($param['page']);
            unset($param['items']);
            $result = $Paper->where($param)->page($pageNum,$itemsNum)->order('project.id')->select();
            $totalNum = $Paper->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            //审计日志
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


    //导入导出
    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            //type:导出所有页或者导出当前页
            //query：查询条件
            //field:导出的字段
            $type = I('get.type');//$param['type'];
            $params = I('get.params');// $param['params'];
            $field = I('get.field');// $param['field'];
            deleteEmptyValue($params);
            $pageNum = $params['page'] ? $params['page'] : 1;
            $itemsNum =  $params['items'] ? $params['items'] : 10;

            if ($params['startTime'])
                $params['birthday'] = array('gt',$params['startTime']);
            $string = '';
            if ($params['academichonor_id']){
                $string .= $string ? ' AND ('.$params['academichonor_id'].')' : '('.$params['academichonor_id'].')';
//                unset($query['academichonor_id']);
            }
            if ($params['college_id']){
                $string .= $string ? ' AND ('.$params['college_id'].')' : '('.$params['college_id'].')';
//                unset($query['college_id']);
            }
            if ($string)
                $params['_string'] = $string;

            $person = D('ProjectView');
            $query = objectToArray($params);
            unset($query['page']);
            unset($query['items']);
            unset($query['startTime']);
            unset($query['endTime']);
            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $person->field($field)->where($query)->order('project.id')->select();
            }else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $result = $person->field($field)->where($query)->page($pageNum,$itemsNum)->order('project.id')->select();
            }else{
                return '未知错误';
            }

            $titleMap = array('person_name'=>'拥有者','name'=>'项目名称','type_name'=>'类型','support_no'=>'支助编号','join_unit'=>'参与单位','source'=>'项目来源','source_department'=>'来源单位','subtype'=>'子类','start_time'=>'开始时间','end_time'=>'结束时间','fund'=>'经费','direct_fund'=>'直接经费','indirect_fund'=>'间接经费','financial_account'=>'金融账户','comment'=>'备注');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '项目信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //审计日志
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
            if (! empty ( $_FILES ['file_people'] ['name'] ))
            {
                //导入person表
                $tmp_file = $_FILES ['file_people'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['file_people'] ['name'] );
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
                $file_name = "people_info_".$str . "." . $file_type;

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
                $degree_buffer = array();
                $mentor_buffer = array();
                $type_buffer = array();
                $title_buffer = array();
                $college_buffer = array();
                $honor_buffer = array();

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

                        /*
                         *输入数据表的表结构：
                         * 员工号(employee_no) 姓名(name) 性别(gender) 出生日期(birthday) 一级学科(first_class)	二级学科(second_class) 手机号(phone)
                         * 办公电话(office_phone) 邮箱地址(email)	学位(degree_id)	导师类型(mentor_type_id)	人员类型(type_id) 职称(title_id)
                         * 所在学院(college_id)	是否是博士后(postdoctor)
                         * 学术荣誉(person_honor表中有honor_id和person_id)
                         */
                        $people = M("person");
                        $data = array();
                        $data["employee_no"]= $v[0];
                        $data["name"]= $v[1];
                        $data["gender"]=$v[2];
                        $data["birthday"]=$v[3];
                        $data["first_class"]=$v[4];
                        $data["second_class"]=$v[5];
                        $data["phone"]=$v[6];
                        $data["office_phone"]=$v[7];
                        $data["email"]=$v[8];

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理学历信息 person_degree
                        $degree_id = getForeignKey($v[9],"person_degree",$degree_buffer);
                        if($degree_id>0){
                            $data["degree_id"] = $degree_id;
                        }//else:id信息获取失败

                        //处理导师类型信息 person_mentor_type
                        $mentor_type_id = getForeignKey($v[10],"person_mentor_type",$mentor_buffer);
                        if($mentor_type_id>0){
                            $data["person_type_id"] = $mentor_type_id;
                        }//else:id信息获取失败

                        //处理人员类型信息 person_type
                        $type_id = getForeignKey($v[11],"person_type",$type_buffer);
                        if($type_id>0){
                            $data["type_id"] = $type_id;
                        }//else:id信息获取失败

                        //处理人员职称信息 person_title
                        $title_id = getForeignKey($v[12],"person_title",$title_buffer);
                        if($type_id>0){
                            $data["title_id"] = $title_id;
                        }//else:id信息获取失败

                        //处理学院信息 college_id
                        $college_id = getForeignKey($v[13],"college",$college_buffer);
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }//else:id信息获取失败

                        /*
                         * 处理博士后信息
                         */
                        $t_pd = $v[14];
                        $postdoctor=false;
                        //转化输入的y/n,是/否为true和false
                        if($t_pd=='Y'||$t_pd=='y'||$t_pd=='是'){
                            $postdoctor=true;
                        }
                        $data["postdoctor"] = $postdoctor;

                        $condition = array("employee_no"=>$v[0],"name"=>$v[1]);
                        $isduplicated = $people->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $people->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;
                        }else{
                            //插入数据
                            $result = $people->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }
                        if (empty($result))
                        {
//                            $this->error ( '数据插入/更新失败！' );
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }else{
                            /* honor的插入需要特别注意，由于是一对多关系，因此会涉及三张表：
                             * 插入的主表为person_honor  涉及到 person中的id   和  academic_honor中的id
                             * $u_honor = $v[15];
                             */
//                            dump($data);//显示添加的数据
                            $person_id = (int)$result;
                            $honor_list = explode('，',(string)$v[15]);//此处分隔符需要选用全角逗号，因为Excel中输入的可能是用的中文输入法
                            foreach($honor_list as $honor_name){
//                                dump('honorName: '.$honor_name);
                                $honor_id = getForeignKey($honor_name,'academic_honor',$honor_buffer);
                                if($honor_id!=null){
                                    $token = array('person_id'=>$person_id,'honor_id'=>$honor_id);
                                    $existed = M('person_honor')->where($token)->find();
                                    if($existed==null){
                                        //插入honor数据
                                        $honor_result = M('person_honor')->add($token);
                                    }
                                }
                            }
                        }

                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入人员信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '人员信息';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的用户名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的用户id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的用户id： ";
                    foreach ($updated_id as $id){
                        $descr .= $id.",";
                    }
                }
                $audit['descr'] .= $descr;
                M('audit')->add($audit);

                if($error_counter==0){
//                    $this->success("恭喜您，成功导入或更新数据"+($insert_counter+$update_counter)+"条！（详情见日志）",'',5);
                    $this->success("恭喜".$data."（详情见日志）",'',100);
                }else{
                    $this->error("存在导入失败的记录，请查看日志进行修正！",'',8);
                }

            }

            $this->error("文件选择失败！");

        }
    }


}