(function(){
    'use strict';
    angular.module('person').controller('personController', personController);
    personController.$inject = ['$scope', 'sendRequest'];
    function personController($scope, sendRequest){
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
		}
		vm.chartFlag = false;//是否显示统计图表
		vm.count_type = 'college';//默认显示学院统计信息

		$scope.$watch('vm.count_type',function () {
			console.log("vm.count_type 改变了："+vm.count_type);
			makeMyChart();
		},true);
		$scope.$watch('vm.chartFlag',function () {
			if(vm.chartFlag){
				makeMyChart();
			}
		},true);

		vm.showChart = function () {
			vm.chartFlag = true;
			// angular.element("#pi").style('cursor','default');
			// makeChart('p1', vm.option);
		}

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

		$scope.$watch('vm.showCheckbox',function () {
			console.log("vm.showcheckbox 改变了："+vm.showCheckbox)
		},true);

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getPersonList(vm.params);
		}, true);

		getAcademichonorList();
		function getAcademichonorList(){
			var url = '/Award/getAcademichonorList/';
			vm.academichonor_id = [];
			sendRequest.get(url, {}, {}).then(
				function(resp){
					// id,abbr_name
					vm.academichonorList = resp;
					// console.log("头衔列表："+vm.academichonorList.toString())

					for(var i = 0; i < resp.length;i++){
						// var aname = vm.academichonorList[i].name;
						// console.log(aname);
						vm.academichonor_id.push(false);
						console.log(vm.academichonor_id[i]);
					}
				},
				function(resp){
					console.log('get AcademichonorList failed');
				}
			);
		}
		
		$scope.$watch('vm.academichonor_id', function(){
			var temp = [];
 			for (var i = 0;i < vm.academichonor_id.length;i++){
                if (vm.academichonor_id[i]){
					 // var aname = JSON.stringify(vm.academichonorList[i+1].name).replace(/\"/g, "");
					var aname = vm.academichonorList[i-1].name;
					console.log("选中的学术称号为："+aname);
                    temp.push(" honor_records like '%"+aname+"%'");
                }
            }

 			if (temp.length){
 			 	vm.params.academichonor_id = temp.join(' and ');
 			}else vm.params.academichonor_id = '';
		}, true);


		function getPersonList(params){
	 		var url = '/People/personList/';
	 		//console.log('getPersonList', vm.params);
			vm.personList={};
			vm.paginationConf.totalItems=0;
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
					var num = resp[0]['totalNum'];
					//更新统计信息
					vm.college_data = resp[0]['itemCollegeCount'];
					vm.title_data = resp[0]['itemTitleCount'];
					if(vm.chartFlag){
						console.log(vm.chartFlag);
						makeMyChart();
					}
					vm.paginationConf.totalItems = num;
					console.log("返回的结果数量为："+num);
					if(num>0){
						vm.personList = resp;
					}
	 			},
	 			function(resp){
	 				console.log('get personList failed');
	 			}
	 		);
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
	 			for (var i = 0;i < vm.collegeSelectedList.length;i++){
					temp.push("college.id = " + vm.collegeSelectedList[i]);
					// vm.pieCollegeLegend.push(vm.collegeMap[vm.collegeSelectedList[i]]);
				}
	 			if (temp.length){
	 			 	vm.params.college_id = temp.join(' or ');
	 			}else vm.params.college_id = '';
	 		}
	 		console.log('vm.collegeSelectedList', vm.collegeSelectedList)
		}, true);

	 	vm.removeCollege = removeCollege;
	 	function removeCollege(id){
	 		vm.collegeSelectedList.splice(vm.collegeSelectedList.indexOf(id), 1);
	 		var temp = [];
			// vm.pieCollegeLegend = [''];
 			for (var i = 0;i < vm.collegeSelectedList.length;i++){
				temp.push("college.id = " + vm.collegeSelectedList[i]);
				// vm.pieCollegeLegend.push(vm.collegeMap[vm.collegeList[i]]);
				// vm.pieCollegeLegend.push(vm.collegeMap[vm.collegeSelectedList[i]]);
			}
 			if (temp.length){
 			 	vm.params.college_id = temp.join(' or ');
 			}else{
 				vm.params.college_id = '';
 				vm.collegeSelected = '';
 			}
	 		console.log('vm.collegeSelectedList', vm.collegeSelectedList)
	 	}

	 	//条件查询
	 	vm.params.gender = '';
	 	vm.params.postdoctor = '';
	 	vm.params.startTime = '';
	 	vm.params.endTime = '';
	 	vm.params.academichonor_id = '';
	 	vm.params.college_id = '';
	 	vm.submit = function(){
	 		vm.params.name = vm.name;
	 		vm.params.employee_no = vm.employee_no;
	 	}
		vm.search = function () {
			vm.params.name = vm.name;
		}
		vm.resetName=function () {
			vm.name="";
			vm.params.name="";
		}


		$scope.searchKeyupEvent = function(e){
			var keycode = window.event?e.keyCode:e.which;
			if(keycode==13){
				vm.search();
				// console.log("按下的按键："+keycode);
			}
		};

	 	vm.cancel = function(){
	 		vm.params.name = '';
	 		vm.name = '';
	 		vm.params.employee_no = '';
	 		vm.employee_no = '';
	 	}

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}

	 	//导入导出
	 	vm.showCheckbox = false;
	 	vm.exportParams = {name:false,gender:false,employee_no:false,college_name:false,title_name:false,postdoctor:false,birthday:false,email:false,phone:false,first_class:false,second_class:false,credit:false,honor_records:false};

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

	 		var url = '/People/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
			console.log(url);
			location.href = url;
	 		vm.showCheckbox = false;

	 	}

	 	vm.selectAll = selectAll;
	 	function selectAll(){
	 		angular.forEach(vm.exportParams, function(value, key){
	 			vm.exportParams[key] = !value;
	 		});
	 	}

		function makeMyChart(){
			if(vm.chartFlag){
				switch (vm.count_type){
					case 'college':
						console.log("显示学院统计信息");
						makeChartAboutCollege();
						break;
					case 'title':
						console.log("显示职称统计信息");
						makeChartAboutTitle();
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
						text: '专家人才成分统计',
						subtext: '科研单位分布情况',
						x: 'center'
					}
				},
				{
					series: [
						{
							name: '所属学院',
							type: 'pie',
							radius: '55%',
							center: ['50%', '60%'],
							data: []
						}
					]
				}
			);
			angular.forEach(vm.college_data,function (item,index) {
				if(item.college_name!=null){
					// console.log(item.college_name);
					vm.option.legend.data.push(item.college_name);
					vm.option.series[0].data.push({value:item.enum, name:item.college_name});
				}
			});
			if(vm.chartFlag){
				makeChart('p1',vm.option);
			}
		}

		function makeChartAboutTitle(){
			//以职称信息为统计尺度
			vm.option = $.extend(true, {}, pieOption,
				{
				title : {
					text: '专家人才成分统计',
					subtext: '职称分布情况',
					x:'center'
				}
			},
				{
					series: [
						{
							name: '教师职称',
							type: 'pie',
							radius: '55%',
							center: ['50%', '60%'],
							data: []
						}
					]
				});
			angular.forEach(vm.title_data,function (item,index) {
				if(item.title_name!=null){
					// console.log(item.college_name);
					vm.option.legend.data.push(item.title_name);
					vm.option.series[0].data.push({value:item.enum, name:item.title_name});
				}
			});
			if(vm.chartFlag){
				makeChart('p1',vm.option);
			}
		}

    }
})()