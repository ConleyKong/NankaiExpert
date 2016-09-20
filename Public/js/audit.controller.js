(function(){
    'use strict';
    angular.module('audit').controller('auditController', auditController);
    auditController.$inject = ['$scope', 'sendRequest'];
    function auditController($scope, sendRequest){
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
			getAuditList(vm.params);
		}, true);

		getAuditList(vm.params);
		function getAuditList(params){
	 		//console.log('getauditList', vm.params);
	 		var url = '/Audit/auditList/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.auditList = resp;
	 				vm.paginationConf.totalItems = resp[0]['totalNum'];
	 			},
	 			function(resp){
	 				console.log('get auditList failed');
	 			}
	 		);
	 	}
    }
})()