(function(){
    'use strict';
    angular.module('person_detail').controller('person_detailController', person_detailController);
    person_detailController.$inject = ['$scope', 'sendRequest'];
    function person_detailController($scope, sendRequest){
    	var vm = this;
    	var params = {};
    	params.id = 184;
    	getPersonDetail(params);
    	function getPersonDetail(params){
	 		var url = '/People/detailAPI/';
	 		sendRequest.post(url, {}, jQuery.param(params)).then(
	 			function(resp){
	 				vm.personDetail = resp;
	 			},
	 			function(resp){
	 				console.log('get personDetail failed');
	 			}
	 		);
	 	}
    }
})()