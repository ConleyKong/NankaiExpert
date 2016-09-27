(function(){
    'use strict';
    angular.module('award').controller('awardController', awardController);
    awardController.$inject = ['$scope', 'sendRequest'];
    function awardController($scope, sendRequest){
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
			getAwardList(vm.params);
		}, true);

		$scope.$watch('vm.grade_id', function(){
			var temp = [];
 			for (var i = 1;i < vm.grade_id.length;i++)
 				if (vm.grade_id[i])
 					temp.push("person_title.id = " + i);
 			if (temp.length){
 			 	vm.params.grade_id = temp.join(' or ');
 			}else vm.params.grade_id = '';
		}, true);

		$scope.$watch('vm.academichonor_id', function(){
			var temp = [];
 			for (var i = 1;i < vm.academichonor_id.length;i++)
 				if (vm.academichonor_id[i])
 					temp.push("academic_honor.id = " + i);
 			if (temp.length){
 			 	vm.params.academichonor_id = temp.join(' or ');
 			}else vm.params.academichonor_id = '';
		}, true);

		$scope.$watch('vm.level', function(){
			var level = ['国家级', '省级奖', '部级奖','其它'], temp = [];
 			for (var i = 0;i < vm.level.length;i++)
 				if (vm.level[i])
 					temp.push("level = '" + level[i] + "'");
 			if (temp.length){
 			 	vm.params.level = temp.join(' or ');
 			}else vm.params.level = '';
 			console.log('vm.params.level', vm.params.level);
		}, true);


		function getAwardList(params){
	 		//console.log('getawardList', vm.params);
	 		var url = '/Award/awardList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.awardList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
	 			},
	 			function(resp){
	 				console.log('get awardList failed');
	 			}
	 		);
	 	}

	 	getGradeList();
	 	function getGradeList(){
	 		var url = '/Award/getGradeList/';
	 		vm.grade_id = [];
	 		sendRequest.get(url, {}, {}).then(
	 			function(resp){
	 				vm.gradeList = resp;
	 				for(var i = 0; i < resp.length;i++)
	 					vm.grade_id.push(false);
	 			},
	 			function(resp){
	 				console.log('get gradeList failed');
	 			}
	 		);
	 	}

	 	getAcademichonorList();
	 	function getAcademichonorList(){
	 		var url = '/Award/getAcademichonorList/';
	 		vm.academichonor_id = [];
	 		sendRequest.get(url, {}, {}).then(
	 			function(resp){
	 				vm.academichonorList = resp;
	 				for(var i = 0; i < resp.length;i++)
	 					vm.academichonor_id.push(false);
	 			},
	 			function(resp){
	 				console.log('get AcademichonorList failed');
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
	 	}

	 	//条件查询
	 	vm.params.level = '';
	 	vm.level = [false, false, false];
	 	vm.params.grade_id = '';
	 	vm.params.academichonor_id = '';
	 	// vm.params.startTime = [];
	 	// vm.params.endTime = [];
		vm.params.startTime = '';
		vm.params.endTime = '';
	 	vm.params.person_startTime = '';
	 	vm.params.person_endTime = '';
	 	vm.params.college_id = '';
	 	vm.submit = function(){
	 		vm.params.name = vm.name;
	 		vm.params.person_name = vm.person_name;
			vm.params.startTime = vm.startTime;
			vm.params.endTime = vm.endTime;
	 	}
	 	vm.cancel = function(){
	 		vm.params.name = '';
	 		vm.name = '';
	 		vm.params.person_name = '';
	 		vm.person_name = '';
			vm.startTime='';
			vm.endTime='';
	 	}

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}

	 	//导入导出
	 	vm.showCheckbox = false;
	 	vm.exportParams = {person_name:false,birthday:false,col_name:false,grade_name:false,academichonor_name:false,name:false,level:false,time:false,comment:false};

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

	 		var url = '/Award/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
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