(function(){
    'use strict';
    angular.module('user').controller('userController', userController);
    userController.$inject = ['$scope', 'sendRequest'];
    function userController($scope, sendRequest){
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
			console.log('params changes new params:', vm.params);
			getUserList(vm.params);
		}, true);


		function getUserList(params){
	 		var url = '/User/getUserList/';
			console.log("url: ",url);
	 		//console.log('getPersonList', vm.params);
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.userList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
	 			},
	 			function(resp){
	 				console.log('get userList failed');
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
	 	vm.params.startTime = '';
	 	vm.params.endTime = '';
	 	vm.params.college_id = '';
	 	vm.submit = function(){
	 		vm.params.name = vm.name;
	 	}
	 	vm.cancel = function(){
	 		vm.params.name = '';
	 		vm.name = '';
	 	}

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}


	
    }
})()