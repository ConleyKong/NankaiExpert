<?php
namespace Home\Controller;
use Think\Controller;
class AwardController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function awardList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
            $param = I('post.');
            deleteEmptyValue($param);
	    	$pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            if ($param['startTime'])
                $param['time'] = array(array('gt',$param['startTime']),array('lt',$param['endTime']));
            if ($param['person_startTime'])
                $param['birthday'] = array(array('gt',$param['person_startTime']),array('lt',$param['person_endTime']));
            $string = '';
            if ($param['level']){
                $string .= $string ? ' AND ('.$param['level'].')' : '('.$param['level'].')';
                unset($param['level']);
            }
            if ($param['academichonor_id']){
                $string .= $string ? ' AND ('.$param['academichonor_id'].')' : '('.$param['academichonor_id'].')';
                unset($param['academichonor_id']);
            }
            if ($param['grade_id']){
                $string .= $string ? ' AND ('.$param['grade_id'].')' : '('.$param['grade_id'].')';
                unset($param['grade_id']);
            }
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

	        $award = D('AwardView');
            unset($param['page']);
            unset($param['items']);
            unset($param['startTime']);
            unset($param['endTime']);
            unset($param['person_startTime']);
            unset($param['person_endTime']);
	        $result = $award->field('id,name,level,time,comment,birthday,person_name,grade_name,academichonor_name,col_name')->where($param)->page($pageNum,$itemsNum)->order('award.id')->select();
            $totalNum = $award->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '获奖列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '查询所有字段';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
	    }
    }




    //获取人员职称
    public function getGradeList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $grade = M('person_title');
            $result = $grade->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    } 

    //获取荣誉称号
    public function getAcademichonorList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $academichonor = M('academic_honor');
            $result = $academichonor->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    }


    /*
     * 文件的导入导出
     */

    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $type = I('get.type');//$param['type'];
            $query = I('get.query');// $param['query'];
            $field = I('get.field');// $param['field'];
            deleteEmptyValue($query);
            $pageNum = $query['page'] ? $query['page'] : 1;
            $itemsNum =  $query['items'] ? $query['items'] : 10;
            if ($query['startTime'] && $query['endTime'])
                $query['time'] = array(array('gt',$query['startTime']),array('lt',$query['endTime']));
            if ($query['person_startTime'] && $query['person_endTime'])
                $query['birthday'] = array(array('gt',$query['person_startTime']),array('lt',$query['person_endTime']));
            $string = '';
            if ($query['level']){
                $string .= $string ? ' AND ('.$query['level'].')' : '('.$query['level'].')';
                //unset($query['level']);
            }
            if ($query['academichonor_id']){
                $string .= $string ? ' AND ('.$query['academichonor_id'].')' : '('.$query['academichonor_id'].')';
                //unset($query['academichonor_id']);
            }
            if ($query['grade_id']){
                $string .= $string ? ' AND ('.$query['grade_id'].')' : '('.$query['grade_id'].')';
                //unset($query['grade_id']);
            }
            if ($query['college_id']){
                $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
//                unset($query['college_id']);
            }
            if ($string)
                $query['_string'] = $string;

            $award = D('AwardView');
            $query = objectToArray($query);
            unset($query['page']);
            unset($query['items']);
            unset($query['startTime']);
            unset($query['endTime']);
            unset($query['person_startTime']);
            unset($query['person_endTime']);
            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $award->field($field)->where($query)->order('award.id')->select();
            }
            else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $result = $award->field($field)->where($query)->page($pageNum,$itemsNum)->order('award.id')->select();
            }
            else
                return '未知错误';
            $titleMap = array('person_name'=>'姓名','birthday'=>'出生日期','col_name'=>'学院','grade_name'=>'职称','academichonor_name'=>'学术荣誉','name'=>'奖励名称','level'=>'奖励级别','time'=>'获奖时间','comment'=>'备注');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '奖励信息';

            exportExcel($filename, $field, $result, $excelTitle);
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '奖励列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '导出奖励列表信息';
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
                $file_name = "award_info_".$str . "." . $file_type;

                /*是否上传成功*/
                if (! copy ( $tmp_file, $savePath . $file_name ))
                {
                    $this->error ( "服务器错误，上传失败！（error in award controller） " );
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
                        $award = M("award");
                        $data = array();
                        $data["name"]= $v[0];//奖励名称
                        $data["achievement"]= $v[1];//成就（获奖具体）
                        $data["date"]=$v[2];//获奖日期
                        $first_no =trim($v[3]);//第一获奖人职工号
                        $others_nos =trim($v[4]);//其他获奖人职工号(中文分号分隔)
                        $data["level"]=$v[5];//等级（省部级、国家级等）
                        $college_name=$v[6];//奖励所属学院（全称）

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理第一获奖人职工号
                            $first_id = getPersonIdByEmployeeNo($first_no);
                            if($first_id>0){
                                $data["first_id"] = $first_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（第一获奖人职工号不存在）";
                                continue;
                            }

                        //处理学院信息 college_id
                            $college_id = getForeignKey($college_name,"college",$college_buffer);
                            if($college_id>0){
                                $data["college_id"] = $college_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（学院信息不存在）";
                                continue;
                            }

                        $condition["achievement"] =$data["achievement"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $award->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $award->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;

                            //删除所有award_other_person相关award职工号，稍后重新插入（以后来数据为准）
                            $flag["award_id"]=(int)$result;
                            M('award_other_person')->where($flag)->delete();

                        }else{
                            //插入数据
                            $result = $award->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }


                        if (empty($result))
                        {
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }else if($others_nos!=''){
                            // 插入其他奖励人职工号
                            $award_id = (int)$result;
                            $others_no_list = explode('；',$others_nos);//此处分隔符需要选用全角分号，因为Excel中输入的可能是用的中文输入法
                            foreach($others_no_list as $person_no){
                                $person_id = getPersonIdByEmployeeNo($person_no);

                                if($person_id>0){
                                    $token["award_id"] = $award_id ;
                                    $token["person_id"]  = $person_id  ;
                                    $existed = M('award_other_person')->where($token)->find();
                                    if($existed==null){
                                        //插入patent_other_inventor数据
                                        $award_other_person_result = M('award_other_person')->add($token);
                                    }//若更新的数据少于上次的数据如何处理？
                                }else{//可能获取不到这个外键的id
                                    $error_counter++;
                                    $errored_name[]=$data["name"]."（其他获奖人职工号 $person_no 不存在）";
                                    continue;
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
                    $this->success("导入工作成功（详情见日志）",'',3);
                }else{
                    $this->error("存在导入失败的记录，请查看日志进行修正！",'',30);
                }

            }

            $this->error("文件选择失败！");

        }
    }


}