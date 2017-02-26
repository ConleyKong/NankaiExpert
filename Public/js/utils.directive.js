/**
 * Created by liangcj on 2016/03/15
 * 项目公共模块————时间选框
 */

(function(){
    'use strict'; 

    angular.module('utils', ['tm.pagination'])
    .directive("ngDate",ngDate)
    .directive("dateRange",dateRange)
    .directive("multiCondition",multiCondition)
    .directive("showMore",showMore)

    function ngDate() {
        return {
            restrict: 'A',
            require: '?ngModel',
            link: function ($scope, $element, $attrs, $ngModel) {
                if (!$ngModel) {
                    return;
                }
                $('.form_datetime').datetimepicker({
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    forceParse: 0,
                    showMeridian: 1
                });
                $('.form_year').datetimepicker({
                    language: 'fr',
                    todayBtn: 1,
                    autoclose: 1,
                    startView: 4,
                    minView: 4,
                    forceParse: 0
                });
                $('.form_date').datetimepicker({
                    language: 'fr',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0
                });
                $('.form_time').datetimepicker({
                    language: 'fr',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 1,
                    minView: 0,
                    maxView: 1,
                    forceParse: 0
                });
            },
        };
    }

    function dateRange(){
        return {
            restrict:'AE',
            scope:{
                startTime: '=',
                endTime: '='
            },
            templateUrl:'/Public/js/templates/dateRange.html',
            replace:true,
            link:link
        }
        function link(scope, element, attrs){ 
            var now = new Date();
            var endDate = now.getFullYear() + '-' + (now.getMonth()+1) + '-' + (now.getDate()-1);
            var startDate = now.getFullYear() + '-' + (now.getMonth()+1) + '-' + (now.getDate()-7);
            var dateRange = new pickerDateRange('date', {
                // aRecent7Days : 'aRecent7Days',
                // aToday: 'aToday',
                // aRecent14Days : 'aRecent14Days',
                // aRecent30Days : 'aRecent30Days',
                isTodayValid : false,
                startDate : '',
                endDate : '',
                defaultText : ' 至 ',
                inputTrigger : 'input_date',
                theme : 'ta',
                success : function(obj) {
                    scope.startTime = obj.startDate;
                    scope.endTime = obj.endDate;
                    scope.$apply("scope.startTime");
                    scope.$apply("scope.endTime");
                }
            });
            element.find('#all').click(function(){
                scope.startTime = '';
                scope.endTime = '';
                scope.$apply("scope.startTime");
                scope.$apply("scope.endTime");
            });
            element.find('#a60').click(function(){
                scope.startTime = '1960-01-01';
                scope.endTime = '1969-12-31';
                scope.$apply("scope.startTime");
                scope.$apply("scope.endTime");
            });
            element.find('#a70').click(function(){
                scope.startTime = '1970-01-01';
                scope.endTime = '1979-12-31';
                scope.$apply("scope.startTime");
                scope.$apply("scope.endTime");
            });
            element.find('#a80').click(function(){
                scope.startTime = '1980-01-01';
                scope.endTime = '1989-12-31';
                scope.$apply("scope.startTime");
                scope.$apply("scope.endTime");
            });
            element.find('#a90').click(function(){
                scope.startTime = '1990-01-01';
                scope.endTime = '1999-12-31';
                scope.$apply("scope.startTime");
                scope.$apply("scope.endTime");
            });
            element.children('a').each(function(){
                $(this).click(function(){
                    $(this).addClass('cond-selected');
                    $(this).siblings().removeClass('cond-selected');
                });
            });
        }
    }

    function multiCondition(){
        return {
            restrict : 'AE',
            scope:{
                freqData : '=',
                otherData : '=',
                modelType : '=',
            },
            templateUrl:'/Public/js/templates/multiCondition.html',
            controller : controller,
            controllerAs : 'vm',
            replace:false,
            transclude:true,
            link:link
        }

        controller.$inject = ['$scope'];
        function controller($scope){
            var vm = this;
            vm.other = true;
            vm.name = $scope.name;
            vm.freqData = $scope.freqData;
            vm.modelType = $scope.modelType;
            if (typeof $scope.otherData == 'undefined'){
                vm.other = false;
            } else {
                vm.otherData = [];
                angular.forEach($scope.otherData, function(value, key){
                    vm.otherData.push({key:key, value:value});
                });
                vm.other = true;
            }
            $scope.$watch('vm.modelType', function(){
                if (vm.modelType != '-1')
                    $scope.modelType = vm.modelType;
            });
            vm.changeFreq = changeFreq;
            vm.changeOther = changeOther;
            vm.otherValue = '-1';
            vm.otherText = '其它';
            function changeFreq(){
                vm.otherText = '其它';
                vm.otherValue = '-1';
            }
            function changeOther(key,value){
                vm.modelType = key;
                vm.otherValue = key;
                vm.otherText = value;
            }
        }

        function link(scope, element, attrs){
            element.find('.col-sm-12 label:last').click(function(){
                var _cond = element.find('.condition-dropdown');
                var top = $(this).offset().top - 30;
                var left = $(this).offset().left;
                _cond.css({
                    'position':'absolute', 
                    'top':top, 
                    'left':left, 
                    'margin-top':'0',
                });
                _cond.find('.content ul li label a').each(function(){
                    $(this).click(function(){
                        _cond.hide();
                    });
                });
                if (_cond.is(':visible'))
                    _cond.slideUp('fast');
                else
                    _cond.slideDown('fast');
            });
            element.click(function(e){
                var _cond = $(this).find('.condition-dropdown');
                var _other = $(this).find('.col-sm-12 label:last');
                e = e || window.event;
                if (e.target != _cond[0] && !jQuery.contains(_cond[0], e.target) && e.target != _other[0]){
                    _cond.slideUp(200);
                    e.stopPropagation();
                }
            })
        }
    }

    function showMore(){
        return {
            restrict : 'EA',
            link:link
        }

        function link(scope, element, attrs){
            var selector = 'div:gt(' + (attrs['count']-1) + '):not(:last)';
            var el = element.children(selector);
            el.hide();
            $('#more').click(function(){
                if(el.is(':visible')){
                    el.slideUp('fast');
                }else{
                    el.slideDown('fast'); 
                }
                $(this).find('span').toggleClass('glyphicon-triangle-bottom glyphicon-triangle-top');

            });
        }
    }

})()



