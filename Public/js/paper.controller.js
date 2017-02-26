(function(){
    'use strict';
    angular.module('paper').controller('paperController', paperController);
    paperController.$inject = ['$scope', 'sendRequest'];
    function paperController($scope, sendRequest){
    	var vm = this;
    	vm.params = {};
    	vm.paginationConf = {
	        currentPage: 1,
	        totalItems: 0,
	        itemsPerPage: 10,
	        pagesLength: 10,
	        perPageOptions: [5, 10, 20, 30, 50],
	       	onChange: function(){
	       		vm.params.page = vm.paginationConf.currentPage;
	       		vm.params.items = vm.paginationConf.itemsPerPage;
	       		//console.log('page change', vm.params);
	    	}
		};

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getPaperList(vm.params);
		}, true);
		
		function getPaperList(params){
	 		//console.log('getPaperList', vm.params);
	 		// var url = '/paper/PaperList/';
			var url = '/paper/paperList/';
			console.log("访问的url为："+url);
			vm.paperList={};
			vm.paginationConf.totalItems=0;
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
					//获取统计信息
					vm.college_data = resp[0]['itemCollegeCount'];
					vm.type_data = resp[0]['itemTypeCount'];
					// console.log("返回的参数："+vm.college_data);
					if(vm.chartFlag){
						makeMyChart();
					}
					var num = resp[0]['totalNum'];
	 				vm.paperList = resp;
					vm.paginationConf.totalItems = num;
					console.log("返回的结果数量为："+num);
					if(num>0){
						vm.paperList = resp;
					}
	 			},
	 			function(resp){
	 				console.log('无法获取论文列表');
	 			}
	 		);
	 	};

		vm.chartFlag = false;//是否显示统计图表
		vm.count_type = 'college';//默认显示学院统计信息

		vm.showChart = function () {
			vm.chartFlag = true;
			console.log("chartFlag变量更新成功");
		}

		$scope.$watch('vm.count_type',function () {
			console.log("vm.count_type 改变了："+vm.count_type);
			makeMyChart();
		},true);
		$scope.$watch('vm.chartFlag',function () {
			if(vm.chartFlag){
				makeMyChart();
			}
		},true);

		function makeChart(id, option){
			vm.myChart = echarts.init(document.getElementById(id), 'macarons');
			vm.myChart.setOption(option);
			window.onresize = vm.myChart.resize;
		}

		var pieOption = {
			tooltip : {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			legend: {
				orient : 'vertical',
				x : 'left',
				data:[]
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: true},
					dataView : {show: true, readOnly: false},
					magicType : {
						show: true,
						type: ['pie', 'funnel'],
						option: {
							funnel: {
								x: '25%',
								width: '70%',
								funnelAlign: 'right',
								max: 1548
							}
						}
					},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			calculable : true,
		};

		function makeMyChart(){
			if(vm.chartFlag){
				switch (vm.count_type){
					case 'college':
						console.log("显示学院统计信息");
						makeChartAboutCollege();
						break;
					case 'type':
						console.log("显示论文类型统计信息");
						makeChartAboutType();
						break;
					default:
						console.log("默认显示学院统计情况");
						makeChartAboutCollege();
						break;
				}
			}
		}
		function makeChartAboutCollege(){
			//以学院信息为统计尺度
			console.log("正在显示学院信息统计");
			vm.option = $.extend(true, {}, pieOption,
				{
					title: {
						text: '科研成果（论文）统计',
						subtext: '科研单位分布情况',
						x: 'center'
					}
				},
				{
					series: [
						{
							name: '所属科研单位',
							type: 'pie',
							radius: '55%',
							center: ['50%', '60%'],
							data: []
						}
					]
				}
			);
			angular.forEach(vm.college_data,function (item,index) {
				// console.log("填数据中……");
				if(item.col_name!=null){
					console.log(item.col_name+","+item.enum);
					vm.option.legend.data.push(item.col_name);
					vm.option.series[0].data.push({value:item.enum, name:item.col_name});
				}
			});
			if(vm.chartFlag){
				makeChart('p1',vm.option);
			}
		}

		function makeChartAboutType(){
			//以论文类型信息为统计尺度
			console.log("正在显示类型信息统计");
			vm.option = $.extend(true, {}, pieOption,
				{
					title: {
						text: '科研成果（论文）统计',
						subtext: '论文类型分布情况',
						x: 'center'
					}
				},
				{
					series: [
						{
							name: '论文类型',
							type: 'pie',
							radius: '55%',
							center: ['50%', '60%'],
							data: []
						}
					]
				}
			);
			angular.forEach(vm.type_data,function (item,index) {
				// console.log("填数据中……");
				if(item.paper_type!=null){
					console.log(item.paper_type+","+item.enum);
					vm.option.legend.data.push(item.paper_type);
					vm.option.series[0].data.push({value:item.enum, name:item.paper_type});
				}
			});
			if(vm.chartFlag){
				makeChart('p1',vm.option);
			}
		}

		vm.paper_type={
			ei:false,
			sci:false,
			cpci:false
		}

		$scope.$watch('vm.paper_type',function () {
			var temp=[];
			angular.forEach(vm.paper_type,function (value,key) {
				if(value){
					temp.push(" paper_type like '%"+key+"%' ");
				}
			});

			if(temp.length>0){
				vm.params.paper_type=temp.join(' and ');
			}else{
				vm.params.paper_type='';
			}
			console.log("选中的论文类型为："+vm.params.paper_type);
		},true);

		vm.resetPaperType = function(){
			vm.paper_type.cpci=false;
			vm.paper_type.ei=false;
			vm.paper_type.sci=false;
			// console.log("清空后的论文类型："+JSON.stringify(vm.paper_type));
		}

	 	getCollegeList();
	 	vm.collegeMap = [''];
	 	function getCollegeList(){
	 		var url = '/People/getCollegeList';
	 		sendRequest.get(url, {}, {}).then(
	 			function(resp){
	 				vm.collegeList = resp;
	 				angular.forEach(resp, function(item, index){
	 					vm.collegeMap.push(item.name);
	 				});
	 			},
	 			function(resp){
	 				console.log('get collegeList failed');
	 			}
	 		);
	 	};
	 	vm.collegeSelected = '';
	 	vm.collegeSelectedList = [];
	 	$scope.$watch('vm.collegeSelected', function(){
	 		console.log('vm.collegeSelected', vm.collegeSelected)
	 		if (!vm.collegeSelected){
	 			vm.collegeSelectedList = [];
	 			vm.params.college_id = '';
	 		}
	 		else if (vm.collegeSelectedList.indexOf(vm.collegeSelected)<0){
	 			vm.collegeSelectedList.push(vm.collegeSelected);
	 			var temp = [];
	 			for (var i = 0;i < vm.collegeSelectedList.length;i++)
	 				temp.push("college.id = " + vm.collegeSelectedList[i]);
	 			if (temp.length){
	 			 	vm.params.college_id = temp.join(' or ');
	 			}else vm.params.college_id = '';
	 		}
	 		//console.log('vm.collegeSelectedList', vm.collegeSelectedList)
		}, true);

	 	vm.removeCollege = removeCollege;
	 	function removeCollege(id){
	 		vm.collegeSelectedList.splice(vm.collegeSelectedList.indexOf(id), 1);
	 		var temp = [];
 			for (var i = 0;i < vm.collegeSelectedList.length;i++)
 				temp.push("college.id = " + vm.collegeSelectedList[i]);
 			if (temp.length){
 			 	vm.params.college_id = temp.join(' or ');
 			}else{
 				vm.params.college_id = '';
 				vm.collegeSelected = '';
 			}
	 		//console.log('vm.collegeSelectedList', vm.collegeSelectedList)
	 	};

	 	//条件查询
		vm.params.conference_name = '';
		vm.params.person_name = '';
		vm.params.start_time = '';
		vm.params.end_time = '';
		vm.params.college_id = '';
		vm.params.keyword='';

		vm.setStart = function () {
			vm.params.start_time = vm.start_time;
		};

		vm.setEnd = function () {
			vm.params.end_time = vm.end_time;
		};

		$scope.setStartKeyupEvent = function(e){
			var keycode = window.event?e.keyCode:e.which;
			if(keycode==13){
				vm.setStart();
				// console.log("按下的按键："+keycode);
			}
		};
		$scope.setEndKeyupEvent = function(e){
			var keycode = window.event?e.keyCode:e.which;
			if(keycode==13){
				vm.setEnd();
				// console.log("按下的按键："+keycode);
			}
		};

		vm.search = function () {
			console.log("关键词"+vm.keyword)
			vm.params.keyword = vm.keyword;
		};
		$scope.searchKeyupEvent = function(e){
			var keycode = window.event?e.keyCode:e.which;
			if(keycode==13){
				vm.search();
				// console.log("按下的按键："+keycode);
			}
		};
		vm.resetKeyword=function () {
			vm.keyword="";
			vm.params.keyword="";
		};

	 	vm.submit = function(){
	 		vm.params.conference_name = vm.conference_name;
	 		vm.params.person_name = vm.person_name;
	 		vm.params.pub_start = vm.pub_start;
	 		vm.params.pub_end = vm.pub_end;
	 	};
	 	vm.cancel = function(){
	 		vm.params.conference_name = '';
	 		vm.params.person_name = '';
	 		vm.params.pub_start = '';
	 		vm.params.pub_end = '';
	 		vm.conference_name = '';
	 		vm.person_name = '';
	 		vm.pub_start = '';
	 		vm.pub_end = '';
	 	};
		
		

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	};

	 	//导入导出
	 	vm.showCheckbox = false;
	 	vm.exportParams = {first_author:false,contacter_name:false,col_name:false,name:false,paper_type:false,conference_name:false,other_authors_name:false,publish_year:false,comment:false};

	 	vm.exportExcel = exportExcel;
	 	function exportExcel(type){
	 		var field = [];
	 		angular.forEach(vm.exportParams, function(value, key){
	 			if (value) field.push(key);
	 		});
		 	if (field.length)
				field = field.join(',');
 			else{
 				alert('请至少选择一个字段');
 				return;
 			}

			//去除params中的空值
			var parameters = {};//map
			angular.forEach(vm.params,function (value,key) {
					if(value){
						parameters[key]=value;
					}
				}
			);

	 		var url = '/paper/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
			location.href = url;
	 		vm.showCheckbox = false;
	 	};

	 	vm.selectAll = selectAll;
	 	function selectAll(){
	 		angular.forEach(vm.exportParams, function(value, key){
	 			vm.exportParams[key] = !value;
	 		});
	 	};
    }
})()