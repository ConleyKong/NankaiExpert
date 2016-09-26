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

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getPersonList(vm.params);
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

		function getPersonList(params){
	 		var url = '/People/personList/';
	 		//console.log('getPersonList', vm.params);
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.personList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
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
	 			for (var i = 0;i < vm.collegeSelectedList.length;i++)
	 				temp.push("college.id = " + vm.collegeSelectedList[i]);
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
 			for (var i = 0;i < vm.collegeSelectedList.length;i++)
 				temp.push("college.id = " + vm.collegeSelectedList[i]);
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
	 	vm.exportParams = {name:false,gender:false,employee_no:false,college_name:false,postdoctor:false,academic_name:false,birthday:false,email:false,phone:false,first_class:false,second_class:false};

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

		// function objectToArray(o)
		// {
		// 	this.data=[];
		// 	var keyset=o.keys();
		// 	if(typeof (o)=='object'){
        //
		// 	}
		// 	foreach ($_array as $key => $value) {
		// 	$value = (is_array($value) || is_object($value)) ? objectToArray($value) : $value;
		// 	$array[$key] = $value;
		// 	return this.data;
		// }
		// 	return $array;
		// }

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


	 	vm.selectAll = selectAll;
	 	function selectAll(){
	 		angular.forEach(vm.exportParams, function(value, key){
	 			vm.exportParams[key] = !value;
	 		});
	 	}
    }
})()