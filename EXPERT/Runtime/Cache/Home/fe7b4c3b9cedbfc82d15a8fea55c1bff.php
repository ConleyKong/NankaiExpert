<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache"> 
<meta http-equiv="Cache-Control" content="no-cache"> 
<meta http-equiv="Expires" content="0">
<title>高校专家信息管理系统</title>
<link rel="stylesheet" type="text/css" href="/Public/bootstrap/3.3.2/css/bootstrap.min.css">
<!-- <link media="screen" rel="stylesheet" type="text/css" href="/Public/angular-block-ui/0.2.2/angular-block-ui.min.css"> -->
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/nav.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/base.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/dateRange.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/showLoading/css/showLoading.css">



</head>
<body>
	<div class="head-v3">
		<div class="navigation-up">
			<div class="navigation-inner">
				<div class="navigation-v3">
					<ul>
						<!-- <li style="margin-right:50px;font-size:22px;line-height:2.5;color:#000">南开大学专家信息管理系统</li> -->
						<li><img src="/Public/image/logo.gif" style="vertical-align:inherit!important" width="350" height="60"></li>
						<li class="nav-up-selected-inpage" _t_nav="home">
							<h2>
								<a href="/Homepage/index">首页</a>
							</h2>
						</li>
						<li class="" _t_nav="people">
							<h2>
								<a href="javascript:void(0);">科研队伍</a>
							</h2>
						</li>
						<li class="" _t_nav="project">
							<h2>
								<a href="javascript:void(0);">科研项目</a>
							</h2>
						</li>
						<!-- <li class="" _t_nav="funds">
							<h2>
								<a href="javascript:void(0);">科研经费</a>
							</h2>
						</li> -->
						<li class="" _t_nav="paper">
							<h2>
								<a href="javascript:void(0);">科研成果</a>
							</h2>
						</li>
						<li _t_nav="patent">
							<h2>
								<a href="javascript:void(0);">知识产权</a>
							</h2>
						</li>
						<li _t_nav="award">
							<h2>
								<a href="javascript:void(0);">成果获奖</a>
							</h2>
						</li>
						<li _t_nav="lab">
							<h2>
								<a href="/Lab/index">科研平台</a>
							</h2>
						</li>
						<li _t_nav="event">
							<h2>
								<a href="/Event/index">科技事件</a>
							</h2>
						</li>
						<li _t_nav="audit">
							<h2>
								<a href="/Audit/index">审计日志</a>
							</h2>
						</li>
						<li class="pull-right" _t_nav="logout">
							<h2>
								<a href="/Index/logout">退出</a>
							</h2>
						</li>
						<li class="pull-right" _t_nav="user">
							<h2>
								<a href="javascript:void(0);" style="padding:0 10px">你好，<?php echo $_SESSION['username'];?></a>
							</h2>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="navigation-down">
			<div id="people" class="nav-down-menu menu-1" style="display: none;" _t_nav="people">
				<div class="navigation-down-inner">
					<dl style="margin-left: 250px;">
						<dt>科研人员</dt>
						<dd>
							<a href="/People/index">人员列表</a>
						</dd>
						<dd>
							<a href="/People/query">人员查询</a>
						</dd>
						<!-- <dd>
							<a href="/People/detail">人员详情</a>
						</dd> -->
						<!-- <dd>
							<a href="javascript:void(0);">常用报表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">人员查重</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">荣誉称号</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">职称设置</a>
						</dd> -->
					</dl>
					<dl>
						<dt>组织结构</dt>
						<dd>
							<a href="javascript:void(0);">院系单位</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">管理单位</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">其他单位</a>
						</dd>
					</dl>
					<!--<dl>-->
				</div>
			</div>
			<div id="project" class="nav-down-menu menu-1" style="display: none;" _t_nav="project">
				<div class="navigation-down-inner">
					<!-- <dl>
						<dt>项目评审</dt>
						<dd>
							<a href="javascript:void(0);">评审计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审通知</a>
						</dd>
					</dl> -->
					<dl style="margin-left: 500px;">
						<dt>项目立项</dt>
						<dd>
							<a href="/Project/index">项目列表</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">项目审核</a>
						</dd> -->
						<dd>
							<a href="javascript:void(0);">项目查询</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">常用报表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">项目查重</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>项目变更</dt>
						<dd>
							<a href="javascript:void(0);">变更列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目中检</dt>
						<dd>
							<a href="javascript:void(0);">中检计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">中检查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目结项</dt>
						<dd>
							<a href="javascript:void(0);">结项计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">未结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">已结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">结项待审核</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<!-- <div id="funds" class="nav-down-menu menu-1" style="display: none;" _t_nav="funds">
				<div class="navigation-down-inner">
					<dl style="margin-left: 300px;">
						<dt>纵向经费</dt>
						<dd>
							<a href="javascript:void(0);">项目经费</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费认领</a>
						</dd>
					</dl>
					<dl>
						<dt>横向经费</dt>
						<dd>
							<a href="javascript:void(0);">项目经费</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费认领</a>
						</dd>
					</dl>
					<dl>
						<dt>项目变更</dt>
						<dd>
							<a href="javascript:void(0);">变更列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目中检</dt>
						<dd>
							<a href="javascript:void(0);">中检计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">中检查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目结项</dt>
						<dd>
							<a href="javascript:void(0);">结项计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">未结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">已结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">结项待审核</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
				</div>
			</div> -->
			<div id="paper" class="nav-down-menu menu-1" style="display: none;" _t_nav="paper">
				<div class="navigation-down-inner">
					<dl style="margin-left: 600px;">
						<dt>论文成果</dt>
						<dd>
							<a href="/Paper/index">论文列表</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">收录列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">历史论文</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>研究报告</dt>
						<dd>
							<a href="javascript:void(0);">研究报告列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">研究报告查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">研究报告查重</a>
						</dd>
					</dl>
					<dl>
						<dt>著作成果</dt>
						<dd>
							<a href="javascript:void(0);">著作列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作查重</a>
						</dd>
					</dl>
					<dl>
						<dt>鉴定成果</dt>
						<dd>
							<a href="javascript:void(0);">申请鉴定</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">鉴定列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">鉴定查重</a>
						</dd>
					</dl>
					<dl>
						<dt>成果登记</dt>
						<dd>
							<a href="javascript:void(0);">成果登记列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果登记查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果登记查重</a>
						</dd>
					</dl>
					<dl>
						<dt>成果转化</dt>
						<dd>
							<a href="javascript:void(0);">成果转化列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果转化查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果转化查重</a>
						</dd>
					</dl>
					<dl>
						<dt>分类维护</dt>
						<dd>
							<a href="javascript:void(0);">期刊源</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">转载名称</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<div id="patent" class="nav-down-menu menu-1" style="display: none;" _t_nav="patent">
				<div class="navigation-down-inner">
					<dl style="margin-left: 700px;">
						<dt>专利成果</dt>
						<dd>
							<a href="/Patent/index">专利列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">专利查询</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">专利查重</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>著作权</dt>
						<dd>
							<a href="javascript:void(0);">著作权列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权查重</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权类型</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<div id="award" class="nav-down-menu menu-1" style="display: none;" _t_nav="award">
				<div class="navigation-down-inner">
					<dl style="margin-left: 800px;">
						<dt>成果获奖</dt>
						<dd>
							<a href="/Award/index">获奖列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">获奖查重</a>
						</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
	
<div class="container">
	<div class="row">
		<div class="col-md-2">
			<div class="btn-group" role="group">
    			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">选择统计类型
      				<span class="caret"></span>
    			</button>
    			<ul class="dropdown-menu">
      				<li><a href="javascript:void(0)" onclick="personQuery()">按人员</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,col_name','col_id','col_name');">按学院</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,men_name','men_id','men_name');">按导师类型</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,deg_name','deg_id','deg_name');">按学位</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,gra_name','gra_id','gra_name');">按职称</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,per_name','per_id','per_name');">按人员类型</a></li>
					<li><a href="javascript:void(0)" onclick="query(null, 'count(*) as count,aca_name','aca_id','aca_name');">按学术荣誉</a></li>
		    	</ul>
  			</div>
  			<div class="btn-group top-10" role="group">
  				<button type="button" class="btn btn-info" onclick="queryAgeAndCollege()">按年龄段和学院
    			</button>
    		</div>
		</div>
	    <div class="col-sm-10">
	    	<div id="college" class="col-md-12" style="height:500px;"></div>
	   	</div>
	</div>
</div>

</body>

<script src="/Public/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
<script src="/Public/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/Public/angular.js/1.3.0/angular.min.js" type="text/javascript"></script>
<!-- <script type="text/javascript" src="/Public/angular-block-ui/0.2.2/angular-block-ui.min.js"></script> -->
<script type="text/javascript" src="/Public/showLoading/js/jquery.showLoading.min.js"></script>
<script type="text/javascript" src="/Public/js/app.module.js"></script>
<script type="text/javascript" src="/Public/js/app.service.js"></script>
<script type="text/javascript" src="/Public/js/dateRange.js"></script>
<script type="text/javascript" src="/Public/js/utils.directive.js"></script>
<script type="text/javascript" src="/Public/js/tm.pagination.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	var qcloud={};
	$('[_t_nav]').hover(function(){
		var _nav = $(this).attr('_t_nav');
		clearTimeout( qcloud[ _nav + '_timer' ] );
		qcloud[ _nav + '_timer' ] = setTimeout(function(){
		$('[_t_nav]').each(function(){
		$(this)[ _nav == $(this).attr('_t_nav') ? 'addClass':'removeClass' ]('nav-up-selected');
		});
		$('#'+_nav).stop(true,true).slideDown(200);
		}, 150);
	},function(){
		var _nav = $(this).attr('_t_nav');
		clearTimeout( qcloud[ _nav + '_timer' ] );
		qcloud[ _nav + '_timer' ] = setTimeout(function(){
		$('[_t_nav]').removeClass('nav-up-selected');
		$('#'+_nav).stop(true,true).slideUp(200);
		}, 150);
	});
});
function selectMenu(first, second, third){
	$("li[_t_nav]").each(function(index, value){
		if ($(this).attr('_t_nav') == first) $(this).addClass('nav-up-selected-inpage');
		else $(this).removeClass('nav-up-selected-inpage');
	});
	$('#'+first).find('dl').eq(second).find('a').eq(third).addClass('third-menu-selected');
}
</script>

<script type="text/javascript" src="/Public/echarts/2.2.7/echarts-all.js"></script>
<script type="text/javascript">
	function makeChart(id, option){
	  var myChart = echarts.init(document.getElementById(id), 'macarons');
	  myChart.setOption(option);
	  window.onresize = myChart.resize;
	}
	selectMenu('people', 0, 1);
	var pieOptionTemp = {
	    title : {
		    text: '按学院划分人数占比',
		    show:false
		},
	    tooltip : {
	        trigger: 'item',
	        formatter: "{a} <br/>{b} : {c} ({d}%)"
	    },
	    legend: {
	        orient : 'vertical',
	        x : 'right',
	        y : 'bottom',
	        data:[]
	    },
	    toolbox: {
	        show : true,
	        feature : {
	            dataView : {show: true, readOnly: false},
	            magicType : {
	                show: true, 
	                type: ['pie', 'funnel'],
	                option: {
	                    funnel: {
	                        x: '25%',
	                        width: '50%',
	                        funnelAlign: 'left',
	                        max: 1548
	                    }
	                }
	            },
	            restore : {show: true},
	            saveAsImage : {show: true}
	        }
	    },
	    calculable : true,
	    series : [
	        {
	            name:'院系占比',
	            type:'pie',
	            radius : '55%',
	            center: ['40%', '50%'],
	            data:[]
	        }
	    ]
	};
	var barOptionTemp = {
	    tooltip : {
	        trigger: 'axis',
	        axisPointer : {       
	            type : 'line'        // 默认为直线，可选为：'line' | 'shadow'
	        }
	    },
	    legend: {
	        data:[]
	    },
	    toolbox: {
	        show : true,
	        feature : {
	            dataView : {show: true, readOnly: false},
	            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
	            restore : {show: true},
	            saveAsImage : {show: true}
	        }
	    },
	    calculable : true,
	    xAxis : [
	        {
	            type : 'value'
	        }
	    ],
	    yAxis : [
	        {
	            type : 'category',
	            data : []
	        }
	    ],
	    series : [
	    ]
	};
                    
	function dateCompare(startdate,enddate)   
	{   
		var arr = startdate.split("-");    
		var starttime = new Date(arr[0],arr[1],arr[2]);    
		var starttimes = starttime.getTime();   
		var arrs = enddate.split("-");    
		var lktime = new Date(arrs[0],arrs[1],arrs[2]);    
		var lktimes = lktime.getTime();   
		return starttimes <= lktimes; //起始日期大，则返回false 
	}  

	function queryAgeAndCollege(){
		var option = $.extend(true, {}, pieOptionTemp);
		var url = '/People/queryAPI', field = 'birthday,col_name';
		var collegeList = [], age50 = [], age60 = [], age70 = [], age80 = [], age90 = [];//60后到80后
		$.post(url, {field:field,group:null}, function(resp){
			$.each(resp, function(index, item){
				if (collegeList.indexOf(item.col_name)<0){
					collegeList.push(item.col_name);
					age50.push(0);
					age60.push(0);
					age70.push(0);
					age80.push(0);
					age90.push(0);
				}
			});
			$.each(collegeList, function(index){
				$.each(resp, function(i, item){
					if (dateCompare('1950-01-01', item.birthday) && dateCompare(item.birthday, '1959-12-31') && (item.col_name == collegeList[index]))
						age50[index]++;
					else if (dateCompare('1960-01-01', item.birthday) && dateCompare(item.birthday, '1969-12-31') && (item.col_name == collegeList[index]))
						age60[index]++;
					else if (dateCompare('1970-01-01', item.birthday) && dateCompare(item.birthday, '1979-12-31') && (item.col_name == collegeList[index]))
						age70[index]++;
					else if (dateCompare('1980-01-01', item.birthday) && dateCompare(item.birthday, '1989-12-31') && (item.col_name == collegeList[index]))
						age80[index]++;
					else if (dateCompare('1990-01-01', item.birthday) && dateCompare(item.birthday, '1999-12-31') && (item.col_name == collegeList[index]))
						age90[index]++;
				});
			});
			var option = $.extend(true, {}, barOptionTemp); //post有延迟
			option.yAxis[0].data = collegeList;
			option.legend.data = ['50后', '60后', '70后', '80后', '90后'];
			var series = [
				{
		            name:'50后',
		            type:'bar',
		            stack: '总量',
		            itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
		            data:age50
		        },
		        {
		            name:'60后',
		            type:'bar',
		            stack: '总量',
		            itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
		            data:age60
		        },
		        {
		            name:'70后',
		            type:'bar',
		            stack: '总量',
		            itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
		            data:age70
		        },
		        {
		            name:'80后',
		            type:'bar',
		            stack: '总量',
		            itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
		            data:age80
		        },
		        {
		            name:'90后',
		            type:'bar',
		            stack: '总量',
		            itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
		            data:age90
		        }
		    ];
		    option.series = series;
		    query(option);
		});
	}

	personQuery();
	function personQuery(){
		var url = '/People/queryAPI';
		var field = 'birthday', index = 0;
		var ageRange = [['50后', 0], ['60后', 0], ['70后', 0], ['80后', 0], ['90后', 0]];
		$.post(url, {field:field, group:null}, function(resp){
			$.each(resp, function(index, item){
				if (dateCompare('1950-01-01', item.birthday) && dateCompare(item.birthday, '1959-12-31'))
					ageRange[0][1]++;
				else if (dateCompare('1960-01-01', item.birthday) && dateCompare(item.birthday, '1969-12-31'))
					ageRange[1][1]++;
				else if (dateCompare('1970-01-01', item.birthday) && dateCompare(item.birthday, '1979-12-31'))
					ageRange[2][1]++;
				else if (dateCompare('1980-01-01', item.birthday) && dateCompare(item.birthday, '1989-12-31'))
					ageRange[3][1]++;
				else if (dateCompare('1990-01-01', item.birthday) && dateCompare(item.birthday, '1999-12-31'))
					ageRange[4][1]++;
			});
			var option = $.extend(true, {}, pieOptionTemp); //post有延迟
			option.title.text = '各年龄段人数占比';
			$.each(ageRange, function(index, item){
				option.legend.data.push(item[0]);
			    option.series[0].data.push({value:item[1], name:item[0]});
			});
			query(option);
		});
	}
	function query(option, field, group, name){
		if (option) 
			makeChart('college', option);
		else
		{
			var option = $.extend(true, {}, pieOptionTemp);
			var url = '/People/queryAPI';
			$.post(url, {field:field, group:group}, function(resp){
				$.each(resp, function(index, item){
					option.legend.data.push(item[name]);
			        option.series[0].data.push({value:item.count, name:item[name]});
				});
				makeChart('college', option);
			});
		}
	}             
</script>

</html>