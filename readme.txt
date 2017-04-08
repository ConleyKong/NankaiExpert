需解决问题：
日期选择插件引入回滚，需后期解决
科技事件的导入导出以及科技事件的图片保存
为项目列表增加项目类型的统计
日期选择的时候无法实现指定区间搜索
project真实导入时需要添加参与人和多个合作单位的写入

人员表：人员类型（教学，管理，双肩挑）筛选？
project只能有年不能有月和日子
后期的sql还可以添加索引以更快提升效率
存在难点：用户不同学院但是同名时论文的导入出现缺失

version2.4.3
增加功能：为User添加用户名搜索功能
增加模块：添加学术交流模块，相应的在数据库添加interchanges表，完善搜索，导出，学院筛选，类型筛选和时间筛选功能
修复bug： 修复各模块间日期范围选择不能选择区间的bug
增加功能：为项目表增加教师成员和合作单位
修复bug：用户导入增加职称判断，将博导教授和院士教授并入了教授，将博导研究员并入了研究员，增加空值判断，默认设置为其他
增加功能：为person表添加college_names字段，应对一人多学院的情况，该字段仅用于显示，同时增加person_colleges表，记录person和college的多对多关系，用于统计和检索，同时增加相应导入导出功能
增加功能：更新职称列表，id<5的是正高，id<12的是副高，id<21的是中级，id<28的是初级，id=29是其他
增加功能：科研队伍中添加职称筛选功能，并修改相应的导出功能
增加功能：论文列表中添加职称筛选功能，并修改相应的导出功能
修复bug： 人员一对多学院后修改导入人员信息的方式，导入时将一作的信息也加入到paper_authors表中，该表记录是所有论文的用户id和paper的id
增加功能：为lab添加编辑功能，可以添加或者删除实验室的成员或名称等
实现功能：手工实现论文信息统计功能：
            $paper->startTrans();
            $create_person_paper_table_sql = "create temporary table if not exists pp (
                                                select
                                                person.id as person_id,
                                                paper.paper_type as paper_type,
                                                paper.publish_year as stat_year,
                                                count(paper.id) as paper_num
                                                from
                                                person
                                                left join paper_authors on person.id=paper_authors.person_id
                                                left join paper on paper_authors.paper_id=paper.id
                                                where person.valid=1 and paper.valid=1
                                                group by person.id,paper.paper_type)";

            $create_ei_table_sql = "create temporary table if not exists ei_table (select person_id,paper_num ei_sum from pp where pp.paper_type='ei')";
            $create_sci_table_sql = "create temporary table if not exists sci_table (select person_id,paper_num sci_sum from pp where pp.paper_type='sci')";
            $create_cpci_table_sql = "create temporary table if not exists cpci_table (select person_id,paper_num cpci_sum from pp where pp.paper_type='cpci-s')";
            $create_total_table_sql = "create temporary table if not exists total_table (select person_id,sum(paper_num) total_sum from pp group by person_id)";

            $query_result_sql = "select
                                    p.id person_id,p.employee_no,p.name person_name,p.gender,p.college_names,title.name title_name,ei_sum,sci_sum,cpci_sum,total_sum
                                    from person p
                                    left join person_title title on p.title_id = title.id
                                    left join ei_table on p.id = ei_table.person_id
                                    left join sci_table on p.id = sci_table.person_id
                                    left join cpci_table on p.id=cpci_table.person_id
                                    left join total_table on p.id=total_table.person_id
                                    where total_sum>0
                                    order by person_id
                                    limit $offset,$itemsNum";
            $query_totalNum_sql="select count(person.id) total_num from person;";

            $paper->execute($create_person_paper_table_sql);
            $paper->execute($create_ei_table_sql);
            $paper->execute($create_sci_table_sql);
            $paper->execute($create_cpci_table_sql);
            $paper->execute($create_total_table_sql);
            $result = $paper->query($query_result_sql);
            $totalNum = $paper->query($query_totalNum_sql);
            $paper->commit();
性能提升：使用engine=memory提升查询性能，由原来的2.18s的响应时间提升到0.8秒
增加功能：为论文统计表增加关键词搜索功能，增加职称筛选功能，增加单位筛选功能，增加年份筛选功能
增加功能：为Project模块增加报表：职工号、姓名、职称、学院、纵向直接(dd_sum<-depth direct)、纵向间接(di_sum)、纵向总经费(dt_sum)、横向总经费(ct_sum)、到账总经费(total_sum)。
修改lab导入功能，让lab的成员的单位信息多个该重点实验室，对于成员的编辑也应该修改成员对应的单位

-------------------------------------------------------------------------------------------------------

version2.4.2
增加限制：论文的模糊搜索关键词匹配增加其他作者和通讯作者
修复bug：项目类型的选择因为底层id的设置出现离散错误，已更正为连续id分布
添加字段：为project添加manager_name用于存储非专家信息，添加participates字段存储其他参与人信息
添加字段：为person添加group_name和group_id字段用于保存所属的团队信息
增加限制：为people，paper，project，patent，award，lab，event添加导入权限控制只有管理员可以导入数据
增加功能：为paper添加自定义搜索功能，可以自主选择需要搜索的field
增加功能：为user添加导入功能
修复bug：修复页面载入过程中的隐藏按钮的闪现
增加功能：为User,People,Project,Paper,Patent,Event,Lab模块添加导入警示标示

version2.4.1
修复用户退出时不检查用户状态的bug
增加限制：项目统计图只有在选择多个学院的时候才显示
修复界面bug：人员信息所选学院中错误限制col为3，已修改为12
事务同步管理：为People,Project,Paper，Patent,Award,Lab的import增加事务过程
修复bug：paper列表里通讯作者直接显示姓名，不通过id
修复bug：一作的id为88888时不显示链接
增加限制：列表显示的项目序号不直接使用id而是使用ng-repeat的$index+1来显示当前状态下的项目真实序列号
    vm.baseIndex = (vm.paginationConf.currentPage-1) * vm.paginationConf.itemsPerPage;
    id = vm.baseIndex+$index+1
调整people表中各列比例，增加一级学科和二级学科宽度


version2.4
完善科研平台列表列的比例
修复未知人员错误显示的情况
修复筛选为空时列表显示过短的问题
为操作栏添加style="white-space: nowrap;"阻止换行

version 2.3.4
修复屏幕调整后缩放统计图无反应
为Person模块添加党派项
修复论文表的日期范围错误
修复首页统计表显示比例失调

version 2.3.3
修复导航栏的宽度问题,将操作日志列表放到了账户管理里
对删除操作增加权限检查
账户管理添加权限分类

version 2.3.2
修复project type类型列表
首页的图标布局匀称一些
显示和隐藏图标按钮位置固定

version 2.3.1
调整各个模块列表显示顺序

version 2.3
添加针对p1的width调整代码（nav中的js）

version 2.2
导入专利信息，导入实验平台信息
修复college外键bug：
    当外键为空时，ModelView就不会显示：
    解决方法：$college_name = $college_name==''?'其他':$college_name;
修复lab，patent，paper导出的字段映射不匹配问题

version 2.1
导入论文信息：
判断论文重名逻辑：
    论文名一样
    第一作者一样
    出版时间一样
    期刊名字（去掉'和,后）一样
    期号一样（去掉字母n)而且页码一样（去掉字母p）
    使用两种思路：
    1.第一作者一样，而且出版时间一样以及论文名一样，
    2.第一作者一样，而且出版时间一样以及期号和页码一样
*由于EI的论文名称出现冒号的时候会丢失冒号后面的标题，因此
    1.先导入sci论文，论文名以sci为主
    2.再导入CPCI-S,该索引是以会议论文为主的
    3.最后导入ei，
论文表结构修改为：


version 2.0.2
添加项目的学院分布统计

version 2.0.1
添加职称列
添加职称分布统计

version1.5
1.项目表添加 参加人员、项目立项时间、项目结项时间列
2.导入时项目类型中的括号统一转化为了全角，

version1.4
学术称号排序按规定
添加学术称号全称和缩略语区分
修复项目类型选择出错的bug

version1.3
1.修复定点导出功能
2.修改系统名称为：南开大学科技管理系统
3.账号权限限制，申请账号需要（李老师）审核