(function(){
    'use strict';
    angular.module('event').controller('eventController', eventController);
    eventController.$inject = ['$scope', 'sendRequest'];
    function eventController($scope, sendRequest){
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
                    var num = resp[0]['totalNum'];
                    vm.paginationConf.totalItems = num;
                    console.log("返回的结果数量为："+num);
                    if(num>0){
                        vm.eventList = resp;
                    }
                },
                function(resp){
                    console.log('get eventList failed');
                }
            );
        }
    }
})()