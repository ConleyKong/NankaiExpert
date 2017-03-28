(function(){
    'use strict';
    angular.module('project').controller('projectController', projectController);
    projectController.$inject = ['$scope', 'sendRequest'];
    function projectController($scope, sendRequest){
    	var vm = this;
    	vm.params = {};
		vm.baseIndex = 0;
    	vm.paginationConf = {
	        currentPage: 1,
	        totalItems: 0,
	        itemsPerPage: 10,
	        pagesLength: 10,
	        perPageOptions: [5, 10, 20, 30, 50],
	       	onChange: function(){
	       		vm.params.page = vm.paginationConf.currentPage;
	       		vm.params.items = vm.paginationConf.itemsPerPage;
				vm.baseIndex = (vm.paginationConf.currentPage-1) * vm.paginationConf.itemsPerPage;
	    	}
		};

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getProjectList(vm.params);
		}, true);

		function getProjectList(params){
	 		var url = '/project/ProjectList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
					//获取统计信息
					vm.college_data = resp[0]['itemCollegeCount'];
					// console.log("返回的参数："+vm.college_data);
					if(vm.chartFlag){
						makeMyChart();
					}
					var num = resp[0]['totalNum'];
					vm.paginationConf.totalItems = num;
					vm.projectList={};
					vm.totalFund=0;
					if(num>0){
						vm.projectList = resp;
						vm.totalFund = resp[0]['totalFund'];
					}
	 			},
	 			function(resp){
	 				console.log('get projectList failed');
	 			}
	 		);
	 	}

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
					default:
						console.log("默认显示学院统计情况");
						makeChartAboutCollege();
						break;
				}
			}
		}
		function makeChartAboutCollege(){
			//以学院信息为统计尺度
			vm.option = $.extend(true, {}, pieOption,
				{
					title: {
						text: '科研项目统计',
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
				if(item.join_unit!=null){
					console.log(item.college_name+","+item.enum);
					vm.option.legend.data.push(item.join_unit);
					vm.option.series[0].data.push({value:item.enum, name:item.join_unit});
				}
			});
			if(vm.chartFlag){
				makeChart('p1',vm.option);
			}
		}


		////////////////////////////////////////////
		//学院类型选择
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
	 	}
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
	 	}

		getTypeList();
		vm.typeMap = [''];
		function getTypeList(){
			var url = '/Project/getTypeList';
			sendRequest.get(url, {}, {}).then(
				function(resp){
					vm.typeList = resp;
					angular.forEach(resp, function(item, index){
						vm.typeMap.push(item.name);
					});
				},
				function(resp){
					console.log('get typeList failed');
				}
			);
		}
		vm.typeSelected = '';
		vm.typeSelectedList = [];
		$scope.$watch('vm.typeSelected', function(){
			console.log('选中的类型为：', vm.typeSelected)
			if (!vm.typeSelected){
				vm.typeSelectedList = [];
				vm.params.type_id = '';
			}
			else if (vm.typeSelectedList.indexOf(vm.typeSelected)<0){//若selectedList中不存在当前项目
				vm.typeSelectedList.push(vm.typeSelected);
				console.log("typeSelectedList中注入"+vm.typeSelected);
				var temp = [];
				for (var i = 0;i < vm.typeSelectedList.length;i++)
					temp.push("project.type_id = " + vm.typeSelectedList[i]);
				if (temp.length){
					vm.params.type_id = temp.join(' or ');
				}else vm.params.type_id = '';
			}
		}, true);

		vm.removeType = removeType;
		function removeType(id){
			vm.typeSelectedList.splice(vm.typeSelectedList.indexOf(id), 1);
			var temp = [];
			for (var i = 0;i < vm.typeSelectedList.length;i++)
				temp.push("project.type_id = " + vm.typeSelectedList[i]);
			if (temp.length){
				vm.params.type_id = temp.join(' or ');
			}else{
				vm.params.type_id = '';
				vm.typeSelected = '';
			}
		}

		////////////////////////////////////////////////////////////////////////
	 	//条件查询
	 	// vm.params.person_name = '';
 		// vm.params.name = '';
 		vm.params.start_time = '';
 		vm.params.end_time = '';
		vm.params.keyword='';

		vm.setStart = function () {
			vm.params.start_time = vm.start_time;
		}

		vm.setEnd = function () {
			vm.params.end_time = vm.end_time;
		}

		vm.search = function () {
			console.log("关键词"+vm.keyword)
			vm.params.keyword = vm.keyword;
		}


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
		}

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}

		///////////////////////////////////////////////////////////////////////////////////////
	 	//导入导出
	 	vm.showCheckbox = false;
	 	vm.exportParams = {person_name:false,name:false,type_name:false,depth_flag:false,support_no:false,join_unit:false,source:false,source_department:false,subtype:false,start_time:false,end_time:false,fund:false,direct_fund:false,indirect_fund:false,financial_account:false,comment:false};

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

	 		var url = '/project/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
			location.href = url;
	 		vm.showCheckbox = false;
	 	}

	 	vm.selectAll = selectAll;
	 	function selectAll(){
	 		angular.forEach(vm.exportParams, function(value, key){
	 			vm.exportParams[key] = !value;
	 		});
	 	}

    }
})()