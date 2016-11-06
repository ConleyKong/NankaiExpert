(function(){
    'use strict';
    angular.module('lab').controller('labController', labController);
    labController.$inject = ['$scope', 'sendRequest'];
    function labController($scope, sendRequest){
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
	    	}
		}
		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getLabList(vm.params);
		}, true);

		getLabList(vm.params);

		function getLabList(params){
	 		//console.log('getLabList', vm.params);
	 		var url = '/Lab/labList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.labList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
	 			},
	 			function(resp){
	 				console.log('get labList failed');
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
		vm.params.manager_name = '';
		// vm.params.formed_start = '';
		// vm.params.formed_end = '';
		vm.params.college_id = '';
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
		$scope.searchKeyupEvent = function(e){
			var keycode = window.event?e.keyCode:e.which;
			if(keycode==13){
				vm.search();
				// console.log("按下的按键："+keycode);
			}
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

		vm.resetKeyword=function () {
			vm.keyword="";
			vm.params.keyword="";
		}
		// vm.submit = function(){
		// 	vm.params.manager_name = vm.manager_name;
		// 	vm.params.formed_start = vm.formed_start;
		// 	vm.params.formed_end = vm.formed_end;
		// }
		// vm.cancel = function(){
		// 	vm.params.manager_name = '';
		// 	vm.params.formed_start = '';
		// 	vm.params.formed_end = '';
		// 	vm.manager_name = '';
		// 	vm.formed_start = '';
		// 	vm.formed_end = '';
		// }

		vm.resetCheckbox = function(param){
			for (var i = 0; i < param.length;i++)
				param[i] = false;
		}

		//导入导出
		vm.showCheckbox = false;
		vm.exportParams = {name:false,manager_name:false,contact_name:false,location:false,formed_time:false,college_name:false,member:false};

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
			
			
			// query用来进行分页查询
			var url = '/Lab/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
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
})();