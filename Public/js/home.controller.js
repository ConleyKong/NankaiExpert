(function(){
    'use strict';
    angular.module('home').controller('homeController', homeController);
    homeController.$inject = ['$scope', 'sendRequest'];
    function homeController($scope, sendRequest){
    	var vm = this;
    	vm.params = {};

        getProjectType();
    	function getProjectType(){
            var url = '/Project/getProjectTypeCount/';
            sendRequest.post(url, {}, {}).then(
                function(resp){
                    vm.projectType = resp.type;
                    // vm.projectSupport = resp.support;
                },
                function(resp){
                    console.log('get projectType failed');
                }
            );
        }
    }
})()