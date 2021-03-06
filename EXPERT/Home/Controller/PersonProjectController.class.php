<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Conley.K
 * Date: 2017/4/8 0008
 * Time: 15:31
 */
class PersonProjectController extends Controller
{


    public function index()
    {
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {
            $this->display();
        }
    }

    public function pplist()
    {
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum = $param['items'] ? $param['items'] : 10; //每页个数
            $offset = ($pageNum - 1) * $itemsNum;
            unset($param['page']);
            unset($param['items']);

            $project = D();
            $create_person_project_table_field_sql = " create temporary table if not exists ppj engine=memory(
                                              select person.id as person_id,project.id as project_id 
                                              from person left join project on person.id=project.manager_id 
                                              ";
            $create_person_project_table_where_sql = " where person.valid=1 and project.valid=1 ";

            $publish_date = "";
            if ($param['start_time']) {
                $pub_start = $param['start_time'];
                $publish_date .= " and (project.start_time>=" . $pub_start . ") ";
                unset($param['startTime']);
            }
            if ($param['end_time']) {
                $pub_end = $param['end_time'];
                $publish_date .= " and (project.end_time<=" . $pub_end . ") ";
                unset($param['endTime']);
            }
            if ($publish_date) {
                $create_person_project_table_where_sql .= $publish_date;
            }

            $create_person_project_table_sql = $create_person_project_table_field_sql.$create_person_project_table_where_sql.")";

            $create_ppj_total_table_sql = "create temporary table if not exists ppj_total engine=memory(select ppj.person_id,count(ppj.project_id) project_num,sum(project.fund) as total_sum from ppj left join project on ppj.project_id=project.id group by ppj.person_id )";
            $create_ppj_depth_table_sql = "create temporary table if not exists ppj_depth engine=memory(select ppj.person_id,sum(project.direct_fund) dd_sum,sum(project.indirect_fund) as di_sum,sum(project.fund) as dt_sum from ppj left join project on ppj.project_id=project.id where project.depth_flag='是' group by ppj.person_id)";
            $create_ppj_cross_table_sql = "create temporary table if not exists ppj_cross engine=memory(select ppj.person_id,sum(project.fund) as ct_sum from ppj left join project on ppj.project_id=project.id where project.depth_flag='否' group by ppj.person_id)";

            $result_fields_sql = "create temporary table if not exists ppj_result engine=memory(
                        select p.id person_id,p.employee_no,p.name person_name,p.title_id title_id,person_title.name title_name,p.college_names,ppj_depth.dd_sum,ppj_depth.di_sum,ppj_depth.dt_sum,ppj_cross.ct_sum,ppj_total.total_sum,ppj_total.project_num 
                        from ppj_total 
                        left join person p on p.id = ppj_total.person_id 
                        left join person_title on p.title_id = person_title.id 
                        left join ppj_depth on ppj_total.person_id = ppj_depth.person_id 
                        left join ppj_cross on ppj_total.person_id = ppj_cross.person_id ";

            $result_where_sql = " where p.id>0 ";

            if ($param['college_id']) {//实际是college_names的参数
                $result_where_sql .= 'AND ( ' . $param['college_id'] . ')';
                unset($param['college_id']);
            }
            if ($param['title_type']) {
                //实际是title_id
                $ts = htmlspecialchars_decode($param['title_type']);
                $result_where_sql .= ' AND (' . $ts . ')';
                unset($param['title_type']);
            }
            if ($param['keyword']) {
                $keyword = $param['keyword'];
                $result_where_sql .= " and ( p.name like '%$keyword%') ";
                unset($param['keyword']);
            }

            $create_result_talbe_sql = $result_fields_sql . $result_where_sql . " ) ";

            $project->execute($create_person_project_table_sql);
            $project->execute($create_ppj_total_table_sql);
            $project->execute($create_ppj_depth_table_sql);
            $project->execute($create_ppj_cross_table_sql);
            $project->execute($create_result_talbe_sql);

            $query_result_sql = "select * from ppj_result order by person_id limit $offset,$itemsNum";
            $query_totalNum_sql = "select count(person_id) total_num from ppj_result";

            $result = $project->query($query_result_sql);
            $totalNum = $project->query($query_totalNum_sql);
            $result[0]['totalNum'] = $totalNum[0]['total_num'];
            $pub_start = empty($pub_start) ? "先前" : $pub_start;
            $pub_end = empty($pub_end) ? "至今" : $pub_end;

            $result[0]["stat_year"] = $pub_start . "-" . $pub_end;
//            $itemCollegeCount = $project->field('col_name,count(*) enum')->where($query)->group('col_name')->select();
//            $result[0]['itemCollegeCount']=$itemCollegeCount;
//            $itemTypeCount = $project->field('project_type,count(*) enum')->where($query)->group('project_type')->select();
//            $result[0]['itemTypeCount']=$itemTypeCount;
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '项目经费统计列表';
            $audit['time'] = date('y-m-d h:i:s', time());
            $audit['descr'] = "项目经费统计列表成功";
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result, 'json');
        }
    }



    /*
     * 导出功能
     */

    public function export()
    {
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {

            $type = I('get.type');//$param['type'];
            $field = I('get.field');// $param['field'];

            $page = I('get.page');
            $items = I('get.items');
            $pageNum = $page ? $page : 1;
            $itemsNum = $items ? $items : 10;
            $offset = ($pageNum - 1) * $itemsNum;


            $pub_start = I('get.start_time');
            $pub_end = I('get.end_time');
            $college_id = I('get.college_id');
            $title_type = I('get.title_type');
            $keyword = I('get.keyword');
//            $project_type = I('get.project_type');
            $project = D();
            $create_person_project_table_field_sql = " create temporary table if not exists ppj engine=memory(
                                              select person.id as person_id,project.id as project_id 
                                              from person left join project on person.id=project.manager_id 
                                              ";
            $create_person_project_table_where_sql = " where person.valid=1 and project.valid=1 ";

            $publish_date = "";
            if ($pub_start) {
                $publish_date .= " and (project.start_time>=" . $pub_start . ") ";
            }
            if ($pub_end) {
                $publish_date .= " and (project.end_time<=" . $pub_end . ") ";
            }
            if ($publish_date) {
                $create_person_project_table_where_sql .= $publish_date;
            }

            $create_person_project_table_sql = $create_person_project_table_field_sql.$create_person_project_table_where_sql.")";

            $create_ppj_total_table_sql = "create temporary table if not exists ppj_total engine=memory(select ppj.person_id,count(ppj.project_id) project_num,sum(project.fund) as total_sum from ppj left join project on ppj.project_id=project.id group by ppj.person_id )";
            $create_ppj_depth_table_sql = "create temporary table if not exists ppj_depth engine=memory(select ppj.person_id,sum(project.direct_fund) dd_sum,sum(project.indirect_fund) as di_sum,sum(project.fund) as dt_sum from ppj left join project on ppj.project_id=project.id where project.depth_flag='是' group by ppj.person_id)";
            $create_ppj_cross_table_sql = "create temporary table if not exists ppj_cross engine=memory(select ppj.person_id,sum(project.fund) as ct_sum from ppj left join project on ppj.project_id=project.id where project.depth_flag='否' group by ppj.person_id)";

            $result_fields_sql = "create temporary table if not exists ppj_result engine=memory(
                        select p.id person_id,p.employee_no,p.name person_name,p.title_id title_id,person_title.name title_name,p.college_names,ppj_depth.dd_sum,ppj_depth.di_sum,ppj_depth.dt_sum,ppj_cross.ct_sum,ppj_total.total_sum,ppj_total.project_num 
                        from ppj_total 
                        left join person p on p.id = ppj_total.person_id 
                        left join person_title on p.title_id = person_title.id 
                        left join ppj_depth on ppj_total.person_id = ppj_depth.person_id 
                        left join ppj_cross on ppj_total.person_id = ppj_cross.person_id ";

            $result_where_sql = " where p.id>0 ";

            if ($college_id) {//实际是college_names的参数
                $result_where_sql .= 'AND ( ' . $college_id . ')';
            }
            if ($title_type) {
                //实际是title_id
                $ts = htmlspecialchars_decode($title_type);
                $result_where_sql .= ' AND (' . $ts . ')';
            }
            if ($keyword) {
                $result_where_sql .= " and ( p.name like '%$keyword%') ";
            }

            $create_result_talbe_sql = $result_fields_sql . $result_where_sql . " ) ";

            $project->execute($create_person_project_table_sql);
            $project->execute($create_ppj_total_table_sql);
            $project->execute($create_ppj_depth_table_sql);
            $project->execute($create_ppj_cross_table_sql);
            $project->execute($create_result_talbe_sql);

            $query_result_sql = "select * from ppj_result order by person_id ";
            $query_result_with_page_limit = " limit $offset,$itemsNum ";

            if ($type == 'all') {
                $audit['descr'] = '导出所有。';
                $result = $project->query($query_result_sql);
            } else if ($type == 'current') {
                $audit['descr'] = '导出当前。';
                $query_result_sql .= $query_result_with_page_limit;
                $result = $project->query($query_result_sql);
            } else {
                //操作记录日志
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '项目统计';
                $audit['time'] = date('y-m-d h:i:s', time());
                $audit['result'] = '失败';
                $audit['descr'] .= '获取项目统计信息出现错误';
                M('audit')->add($audit);
                return '未知错误';
            }

            //为result的每个元素加入stat_year
            $pub_start = empty($pub_start) ? "先前" : $pub_start;
            $pub_end = empty($pub_end) ? "至今" : $pub_end;
            foreach ($result as $k => $v) {
                $result[$k]['stat_year'] = $pub_start . "-" . $pub_end;
            }

            $titleMap = array('employee_no' => '职工号', 'person_name' => '专家名', 'title_name' => '职称', 'college_names' => '所属单位', 'stat_year' => '项目年份', 'project_num' => '项目总数', 'dd_sum' => '纵向直接经费总数', 'di_sum' => '纵向简介经费总数', 'dt_sum' => '纵向经费总数', 'ct_sum' => '横向经费总数', 'total_sum' => '总经费数');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '项目统计信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '项目统计信息';
            $audit['time'] = date('y-m-d h:i:s', time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出项目统计信息';
            M('audit')->add($audit);
        }
    }
}

