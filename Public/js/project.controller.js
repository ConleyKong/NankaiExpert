(function(){
    'use strict';
    angular.module('project').controller('projectController', projectController);
    projectController.$inject = ['$scope', 'sendRequest'];
    function projectController($scope, sendRequest){
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

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getProjectList(vm.params);
		}, true);


		function getProjectList(params){
	 		var url = '/project/ProjectList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.projectList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
	 			},
	 			function(resp){
	 				console.log('get projectList failed');
	 			}
	 		);
	 	}

		getProjectTypeList();
		function getProjectTypeList(){
			var url = '/Project/getProjectTypeList/';
			vm.projecttype_id = [];
			sendRequest.get(url, {}, {}).then(
				function(resp){
					vm.projecttypeList = resp;
					for(var i = 0; i < resp.length;i++)
						vm.projecttype_id.push(false);
				},
				function(resp){
					console.log('get projectypeList failed');
				}
			);
		}

		$scope.$watch('vm.projecttype_id', function(){
			var temp = [];
			for (var i = 1;i < vm.projecttype_id.length;i++)
				if (vm.projecttype_id[i])
					temp.push("project_type.id = " + i);
			if (temp.length){
				vm.params.projecttype_id = temp.join(' or ');
			}else vm.params.projecttype_id = '';
		}, true);

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

	 	//条件查询
	 	vm.params.person_name = '';
 		vm.params.name = '';
 		vm.params.start_time = '';
 		vm.params.end_time = '';
	 	vm.submit = function(){
	 		vm.params.person_name = vm.person_name;
 			vm.params.name = vm.name;
 			vm.params.start_time = vm.start_time;
 			vm.params.end_time = vm.end_time;
	 	}
	 	vm.cancel = function(){
	 		vm.params.person_name = '';
 			vm.params.name = '';
 			vm.person_name = '';
 			vm.name = '';
 			vm.params.start_time = '';
 			vm.params.end_time = '';
 			vm.start_time = '';
 			vm.end_time = '';
	 	}

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}

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