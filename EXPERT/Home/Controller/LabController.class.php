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

//            $manager_name = $param['manager_name'];
//            if($manager_name){
//                $query['manager_name']=array('like','%'.$manager_name.'%');
//                unset($param['manager_name']);
//            }
            $formed_start = $param['start_time'];
            $formed_end = $param['end_time'];
            $formed_date = array();
            if($formed_start){
                $formed_date = array('gt',$formed_start);
                unset($param['start_time']);
            }
            if($formed_end){
                $formed_date = array('lt',$formed_end);
                unset($param['end_time']);
            }
            if($formed_date)
                $query['formed_year']=$formed_date;

            $string = '';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if($param['keyword']){
                $keyword = $param['keyword'];
                $ts = " (lab.name like '%$keyword%' ) ";
                $string .= $string?' AND '.$ts:$ts;
                unset($param['keyword']);
            }
            if ($string)
                $query['_string'] = $string;

            $query["valid"]=true;

            $Lab = D('LabView');
            unset($param['page']);
            unset($param['items']);

            $result = $Lab->where($query)->page($pageNum,$itemsNum)->order('Lab.id')->select();
            $totalNum = $Lab->where($query)->count();

            $result[0]['totalNum'] = $totalNum;
            //操作记录日志
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
            //操作记录日志
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
            $field = I('get.field');

            $page = I('get.page');
            $items = I('get.items');

            $pageNum = $page ? $page : 1;  //当前页
            $itemsNum =  $items ? $items : 10; //每页个数

            $manager_name = I('get.manager_name');
            if($manager_name){
                $query['manager_name']=array('like','%'.$manager_name.'%');
            }

            $formed_start = I('get.start_time');
            $formed_end = I('get.end_time');
            $formed_date = array();
            if($formed_start){
                $formed_date = array('gt',$formed_start);
            }
            if($formed_end){
                $formed_date = array('lt',$formed_end);
            }
            if($formed_date)
                $query['formed_date']=$formed_date;

            $college_id = I('get.college_id');
            $string = '';
            if ($college_id){
                $string .= $string ? ' AND ('.$college_id.')' : '('.$college_id.')';
            }
            $keyword = I('get.keyword');
            if($keyword){
                $ts = " (lab.name like '%$keyword%' OR person.name like '%$keyword%' OR contact.name like '%$keyword%') ";
                $string .= $string?' AND '.$ts:$ts;
            }
            if ($string)
                $query['_string'] = $string;

            $query["valid"]=true;

            $Lab = D('LabView');

            if ($type == 'all'){
                $result = $Lab->field($field)->where($query)->order('Lab.id')->select();
            }
            else if ($type == 'current'){
                $result = $Lab->field($field)->where($query)->page($pageNum,$itemsNum)->order('Lab.id')->select();
            }
            else
                return '未知错误';

            $titleMap = array('name'=>'平台名称','college_name'=>'所属院系','lab_type'=>'实验室类型','description'=>'实验室简介','research_interests'=>'研究方向','research_results'=>'研究成果','formed_year'=>'成立年份','members'=>'成员');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '平台信息';
            exportExcel($filename,$field, $result, $excelTitle);


            //操作记录日志
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

                //默认的学院id为其他：23
                $default_college_id = 23;

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
                        $data["name"]= trim($v[0]);//实验室名称
                        $data["location"]= trim($v[1]);//地址
                        $data["formed_year"]=trim($v[2]);//成立时间
                        $college_name = trim($v[3]);//所属部门或学院名
                        $data["members"]=trim($v[4]);//成员（中文分号分隔）
                        $data["description"]=trim($v[5]);//实验室介绍（500字内）
                        $data['research_interests']=trim($v[6]);//研究方向
                        $data['research_results']=trim($v[7]);//研究方向
                        $data['lab_type']=trim($v[8]);//实验室类型
                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        //处理学院信息 college_id
                        $college_name = $college_name==''?'其他':$college_name;
                            $college_id = getForeignKeyFromDB($college_name,"college");
                            if($college_id>0){
                                $data["college_id"] = $college_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（学院 $college_name 不存在）";
//                                continue;
                            }

                        $condition["name"]=$data["name"];
                        $isduplicated = $lab->where($condition)->find();
                        $lab->startTrans();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $lab->where($condition)->save($data);
                            if($num){
                                $lab->commit();
                                $result = $isduplicated['id'];
                                //将成功更新的数据记录到日志中
                                $update_counter++;
                                $updated_id[]=$result;
                            }

                        }else{
                            //插入数据
                            $result = $lab->add($data);
                            if($result){
                                $lab->commit();
                                //成功插入数据记录到日志中
                                $insert_counter++;
                                $inserted_id[]=$result;
                            }

                        }

                        if (empty($result))
                        {
                            $lab->rollback();
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
                $descr = "\n 记录导入异常".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入异常的平台名： ";
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
                    $this->success("导入工作成功（详情见日志）",'/Audit/index',2);
                }else{
                    $this->error("存在导入异常的记录，请查看日志进行修正！",'/Audit/index',2);
                }

            }

            $this->error("文件选择失败！");

        }
    }


}