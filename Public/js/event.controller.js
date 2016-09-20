(function(){
    'use strict';
    angular.module('event').controller('eventController', eventController);
    eventController.$inject = ['$scope', 'sendRequest'];
    function eventController($scope, sendRequest){
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
            getEventList(vm.params);
        }, true);

        getEventList(vm.params);
        function getEventList(params){
            //console.log('getauditList', vm.params);
            var url = '/Event/eventList/';
            sendRequest.post(url, {}, jQuery.param(params)).then(
                function(resp){
                    vm.eventList = resp;
                    vm.paginationConf.totalItems = resp[0]['totalNum'];
                },
                function(resp){
                    console.log('get eventList failed');
                }
            );
        }
    }
})()