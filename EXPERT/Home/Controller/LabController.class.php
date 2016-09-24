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

            $param["valid"]=true;

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


    public function delete()
    {
        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $condition['id'] = $id;
//            $condition['valid']=true;
            $lab = M('lab');
            $p = $lab->where($condition)->find();
            $p['valid']=false;
            $state = $lab->where($condition)->save($p);
            $name = $p["name"];
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '科研平台';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "删除平台: $id: $name ";


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
        }
    }


    /*
     * 导入导出
     */
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
                $file_name = "lab_info_".$str . "." . $file_type;

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
                        /*
                         *输入数据表的表结构：
                         *

                         */
                        $lab = M("lab");
                        $data = array();
                        $data["name"]= $v[0];//实验室名称
                        $data["location"]= $v[1];//地址
                        $manager_no =trim($v[2]);//负责人职工号
                        $data["formed_time"]=$v[3];//成立时间
                        $college_name = trim($v[4]);//所属部门或学院名
                        $data["member"]=$v[5];//成员（中文分号分隔）
                        $data["description"]=$v[6];//实验室介绍（500字内）


                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理负责人职工号
                            $manager_id = getPersonIdByEmployeeNo($manager_no);
                            if($manager_id>0){
                                $data["manager_id"] = $manager_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（负责人职工号不存在）";
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


                        $condition["manager_id"] =$data["manager_id"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $lab->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $lab->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;
                        }else{
                            //插入数据
                            $result = $lab->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }

                        if (empty($result))
                        {
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }
                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入实验室信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '实验平台';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的平台名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的平台id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的平台id： ";
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