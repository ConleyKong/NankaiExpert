(function(){
    'use strict';
    angular.module('patent').controller('patentController', patentController);
    patentController.$inject = ['$scope', 'sendRequest'];
    function patentController($scope, sendRequest){
    	var vm = this;
    	vm.params = {};
		vm.baseIndex;
		vm.isUploading=false;
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
	       		//console.log('page change', vm.params);
	    	}
		}

		$scope.$watch('vm.params', function(){
			console.log('params change', vm.params);
			getPatentList(vm.params);
		}, true);


		function getPatentList(params){
	 		//console.log('getPatentList', vm.params);
	 		var url = '/Patent/patentList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
					var num = resp[0]['totalNum'];
					vm.paginationConf.totalItems = num;
					console.log("返回的结果数量为："+num);
					if(num>0){
						vm.patentList = resp;
					}
	 			},
	 			function(resp){
	 				console.log('get patentList failed');
	 			}
	 		);
	 	}




	 	getCollegeList();
	 	vm.collegeMap1 = [''];
        vm.collegeMap2 = [''];
	 	function getCollegeList(){
	 		var url = '/People/getCollegeList';
	 		sendRequest.get(url, {}, {}).then(
	 			function(resp){
	 				vm.collegeList = resp;
	 				angular.forEach(resp, function(item, index){
	 					vm.collegeMap1.push(item.name);
                        vm.collegeMap2.push(item.name);
	 				});
	 			},
	 			function(resp){
	 				console.log('get collegeList failed');
	 			}
	 		);
	 	}
	 	vm.collegeSelected1 = '';
	 	vm.collegeSelectedList1 = [];
	 	$scope.$watch('vm.collegeSelected1', function(){
	 		console.log('vm.collegeSelected1', vm.collegeSelected1)
	 		if (!vm.collegeSelected1){
	 			vm.collegeSelectedList1 = [];
	 			vm.params.ownercollege_id = '';
	 		}
	 		else if (vm.collegeSelectedList1.indexOf(vm.collegeSelected1)<0){
	 			vm.collegeSelectedList1.push(vm.collegeSelected1);
	 			var temp = [];
	 			for (var i = 0;i < vm.collegeSelectedList1.length;i++)
	 				temp.push("ownercollege.id = " + vm.collegeSelectedList1[i]);
	 			if (temp.length){
	 			 	vm.params.ownercollege_id = temp.join(' or ');
	 			}else vm.params.ownercollege_id = '';
	 		}
	 		//console.log('vm.collegeSelected1List1', vm.collegeSelected1List1)
		}, true);

        vm.collegeSelected2 = '';
        vm.collegeSelectedList2 = [];
        $scope.$watch('vm.collegeSelected2', function(){
            console.log('vm.collegeSelected2', vm.collegeSelected2)
            if (!vm.collegeSelected2){
                vm.collegeSelectedList2 = [];
                vm.params.inventorcollege_id = '';
            }
            else if (vm.collegeSelectedList2.indexOf(vm.collegeSelected2)<0){
                vm.collegeSelectedList2.push(vm.collegeSelected2);
                var temp = [];
                for (var i = 0;i < vm.collegeSelectedList2.length;i++)
                    temp.push("inventorcollege.id = " + vm.collegeSelectedList2[i]);
                if (temp.length){
                    vm.params.inventorcollege_id = temp.join(' or ');
                }else vm.params.inventorcollege_id = '';
            }
            //console.log('vm.collegeSelected1List1', vm.collegeSelected1List1)
        }, true);

	 	vm.removeCollege1 = removeCollege1;
	 	function removeCollege1(id){
	 		vm.collegeSelectedList1.splice(vm.collegeSelectedList1.indexOf(id), 1);
	 		var temp = [];
 			for (var i = 0;i < vm.collegeSelectedList1.length;i++)
 				temp.push("ownercollege.id = " + vm.collegeSelectedList1[i]);
 			if (temp.length){
 			 	vm.params.ownercollege_id = temp.join(' or ');
 			}else{
 				vm.params.ownercollege_id = '';
 				vm.collegeSelected1 = '';
 			}
	 		//console.log('vm.collegeSelected1List1', vm.collegeSelected1List1)
	 	}

        vm.removeCollege2 = removeCollege2;
        function removeCollege2(id){
            vm.collegeSelectedList2.splice(vm.collegeSelectedList2.indexOf(id), 1);
            var temp = [];
            for (var i = 0;i < vm.collegeSelectedList2.length;i++)
                temp.push("inventorcollege.id = " + vm.collegeSelectedList2[i]);
            if (temp.length){
                vm.params.inventorcollege_id = temp.join(' or ');
            }else{
                vm.params.inventorcollege_id = '';
                vm.collegeSelected2 = '';
            }
            //console.log('vm.collegeSelected1List1', vm.collegeSelected1List1)
        }

	 	//条件查询
	 	vm.params.keyword = '';
 		// vm.params.owner_name = '';
 		// vm.params.firstinventor_name = '';
	 	vm.params.ownercollege_id = '';
        vm.params.inventorcollege_id = '';
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
		vm.resetKeyword=function () {
			vm.keyword="";
			vm.params.keyword="";
		}
	 	// vm.submit = function(){
	 	// 	vm.params.firstinventor_name = vm.firstinventor_name;
	 	// 	vm.params.owner_name = vm.owner_name;
	 	// 	vm.params.name = vm.name;
	 	// }
	 	// vm.cancel = function(){
	 	// 	vm.params.name = '';
          //   vm.params.owner_name = '';
          //   vm.params.firstinventor_name = '';
	 	// 	vm.name = '';
          //   vm.owner_name = '';
          //   vm.firstinventor_name = '';
	 	// }

	 	vm.resetCheckbox = function(param){
	 		for (var i = 0; i < param.length;i++)
	 			param[i] = false;
	 	}

	 	//导入导出
	 	vm.showCheckbox = false;
	 	vm.exportParams = {name:false,first_inventor:false,owner_college_name:false,apply_no:false,apply_date:false,type:false,all_inventors:false,grant_date:false,patent_state:false,comment:false};

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

	 		var url = '/patent/export/?type=' + type +'&'+jQuery.param(parameters)  + '&field=' + field;
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