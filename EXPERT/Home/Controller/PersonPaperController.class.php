<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Conley.K
 * Date: 2017/2/17 0017
 * Time: 16:29
 */
class PersonPaperController extends Controller {
    

    public function index(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    public function paperList(){
        /*
         * 本方法用于列出每个人的论文发布统计信息
         */
        if (! session('logged')){
            $this->redirect('Index/index');
        }else{
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            $offset = ($pageNum-1)*$itemsNum;
            unset($param['page']);
            unset($param['items']);

            $paper = D();

            $create_person_paper_table_field_sql="create temporary table if not exists pp engine=memory (
                select 
                person.id as person_id, 
                paper.paper_type as paper_type,
                paper.publish_year as stat_year,
                count(paper.id) as paper_num
                from 
                person left join paper_authors on person.id=paper_authors.person_id
                left join paper on paper_authors.paper_id=paper.id ";
            $create_person_paper_table_where_sql=" where person.valid=1 and paper.valid=1 ";

            $publish_date = "";
            if($param['start_time']){
                $pub_start = $param['start_time'];
                $publish_date.=" and (paper.publish_year>=".$pub_start.") ";
                unset($param['startTime']);
            }
            if($param['end_time']){
                $pub_end = $param['end_time'];
                $publish_date.= " and (paper.publish_year<=".$pub_end.") ";
                unset($param['endTime']);
            }
            if($publish_date){
                $create_person_paper_table_where_sql.=$publish_date;
            }

            $create_person_paper_table_group_sql=" group by person.id,paper.paper_type)";
            $create_person_paper_table_sql =$create_person_paper_table_field_sql.$create_person_paper_table_where_sql.$create_person_paper_table_group_sql;


            $create_ei_table_sql = "create temporary table if not exists ei_table engine=memory (select person_id,paper_num ei_sum from pp where pp.paper_type='ei')";
            $create_sci_table_sql = "create temporary table if not exists sci_table engine=memory (select person_id,paper_num sci_sum from pp where pp.paper_type='sci')";
            $create_cpci_table_sql = "create temporary table if not exists cpci_table engine=memory (select person_id,paper_num cpci_sum from pp where pp.paper_type='cpci-s')";
            $create_total_table_sql = "create temporary table if not exists total_table engine=memory (select person_id,sum(paper_num) total_sum from pp group by person_id)";

            $result_fields_sql = "create temporary table if not exists result_table engine=memory (select
                                    p.id person_id,p.employee_no,p.name person_name,p.gender,p.college_names,title.id title_id,title.name title_name,ei_sum,sci_sum,cpci_sum,total_sum
                                    from person p
                                    left join person_title title on p.title_id = title.id
                                    left join ei_table on p.id = ei_table.person_id
                                    left join sci_table on p.id = sci_table.person_id
                                    left join cpci_table on p.id=cpci_table.person_id
                                    left join total_table on p.id=total_table.person_id";
            $result_where_sql = " where total_sum>0 ";

            if ($param['college_id']){
                $result_where_sql.='AND ( '.$param['college_id'].')';
                unset($param['college_id']);
            }
            if($param['title_type']){
                //实际是title_id
                $ts = htmlspecialchars_decode($param['title_type']);
                $result_where_sql.=' AND ('.$ts.')';
                unset($param['title_type']);
            }
            if($param['keyword']){
                $keyword = $param['keyword'];
                $result_where_sql.=" and ( p.name like '%$keyword%') ";
                unset($param['keyword']);
            }

            $create_result_talbe_sql = $result_fields_sql.$result_where_sql.")";

            $paper->execute($create_person_paper_table_sql);
            $paper->execute($create_ei_table_sql);
            $paper->execute($create_sci_table_sql);
            $paper->execute($create_cpci_table_sql);
            $paper->execute($create_total_table_sql);
            $paper->execute($create_result_talbe_sql);

            $query_result_sql = "select * from result_table order by person_id limit $offset,$itemsNum";
            $query_totalNum_sql="select count(person_id) total_num from result_table;";

            $result = $paper->query($query_result_sql);
            $totalNum = $paper->query($query_totalNum_sql);
            $result[0]['totalNum'] = $totalNum[0]['total_num'];
            $pub_start= empty($pub_start)?"先前":$pub_start;
            $pub_end= empty($pub_end)?"至今":$pub_end;

            $result[0]["stat_year"] = $pub_start."-".$pub_end;
//            $itemCollegeCount = $paper->field('col_name,count(*) enum')->where($query)->group('col_name')->select();
//            $result[0]['itemCollegeCount']=$itemCollegeCount;
//            $itemTypeCount = $paper->field('paper_type,count(*) enum')->where($query)->group('paper_type')->select();
//            $result[0]['itemTypeCount']=$itemTypeCount;
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文统计列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr']="获取论文统计列表成功";
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
        }
    }

    /*
     * 导出功能
     */

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
            $offset = ($pageNum-1)*$itemsNum;


            $pub_start = I('get.start_time');
            $pub_end = I('get.end_time');
            $college_id = I('get.college_id');
            $title_type = I('get.title_type');
            $keyword = I('get.keyword');
            $paper_type = I('get.paper_type');
            $paper = D();

            $create_person_paper_table_field_sql="create temporary table if not exists pp engine=memory (
                select 
                person.id as person_id, 
                paper.paper_type as paper_type,
                paper.publish_year as stat_year,
                count(paper.id) as paper_num
                from 
                person left join paper_authors on person.id=paper_authors.person_id
                left join paper on paper_authors.paper_id=paper.id ";
            $create_person_paper_table_where_sql=" where person.valid=1 and paper.valid=1 ";

            $publish_date = "";
            if($pub_start){
                $publish_date.=" and (paper.publish_year>=".$pub_start.") ";
            }
            if($pub_end){
                $publish_date.= " and (paper.publish_year<=".$pub_end.") ";
            }
            if($publish_date){
                $create_person_paper_table_where_sql.=$publish_date;
            }

            $create_person_paper_table_group_sql=" group by person.id,paper.paper_type)";
            $create_person_paper_table_sql =$create_person_paper_table_field_sql.$create_person_paper_table_where_sql.$create_person_paper_table_group_sql;


            $create_ei_table_sql = "create temporary table if not exists ei_table engine=memory (select person_id,paper_num ei_sum from pp where pp.paper_type='ei')";
            $create_sci_table_sql = "create temporary table if not exists sci_table engine=memory (select person_id,paper_num sci_sum from pp where pp.paper_type='sci')";
            $create_cpci_table_sql = "create temporary table if not exists cpci_table engine=memory (select person_id,paper_num cpci_sum from pp where pp.paper_type='cpci-s')";
            $create_total_table_sql = "create temporary table if not exists total_table engine=memory (select person_id,sum(paper_num) total_sum from pp group by person_id)";

            $result_fields_sql = "create temporary table if not exists result_table engine=memory (select
                                    p.id person_id,p.employee_no,p.name person_name,p.gender,p.college_names,title.id title_id,title.name title_name,ei_sum,sci_sum,cpci_sum,total_sum
                                    from person p
                                    left join person_title title on p.title_id = title.id
                                    left join ei_table on p.id = ei_table.person_id
                                    left join sci_table on p.id = sci_table.person_id
                                    left join cpci_table on p.id=cpci_table.person_id
                                    left join total_table on p.id=total_table.person_id";
            $result_where_sql = " where total_sum>0 ";

            if ($college_id){
                $result_where_sql.='AND ( '.$college_id.')';
            }
            if($title_type){
                //实际是title_id
                $ts = htmlspecialchars_decode($title_type);
                $result_where_sql.=' AND ('.$ts.')';
            }
            if($keyword){
                $result_where_sql.=" and ( p.name like '%$keyword%') ";
            }

            $create_result_talbe_sql = $result_fields_sql.$result_where_sql.")";

            $paper->execute($create_person_paper_table_sql);
            $paper->execute($create_ei_table_sql);
            $paper->execute($create_sci_table_sql);
            $paper->execute($create_cpci_table_sql);
            $paper->execute($create_total_table_sql);
            $paper->execute($create_result_talbe_sql);

            $query_result_sql = "select * from result_table order by person_id ";
            $query_result_with_page_limit = " limit $offset,$itemsNum ";

            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $paper->query($query_result_sql);
            }else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $query_result_sql.=$query_result_with_page_limit;
                $result = $paper->query($query_result_sql);
            }else{
                //操作记录日志
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '论文信息';
                $audit['time'] = date('y-m-d h:i:s',time());
                $audit['result'] = '失败';
                $audit['descr'] .= '获取导出论文信息出现错误';
                M('audit')->add($audit);
                return '未知错误';
            }

            //为result的每个元素加入stat_year
            $pub_start= empty($pub_start)?"先前":$pub_start;
            $pub_end= empty($pub_end)?"至今":$pub_end;
            foreach ($result as $k => $v){
                $result[$k]['stat_year']=$pub_start."-".$pub_end;
            }

            $titleMap = array('employee_no'=>'职工号','person_name'=>'专家名','title_name'=>'职称','college_names'=>'所属单位','stat_year'=>'发表年份','sci_sum'=>'sci论文总数','ei_sum'=>'ei论文总数','cpci_sum'=>'cpci论文总数','total_sum'=>'论文总数');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '论文统计信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文统计信息';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出论文统计信息';
            M('audit')->add($audit);
        }
    }


}