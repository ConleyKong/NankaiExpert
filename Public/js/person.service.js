(function(){
    'use strict';

    angular.module('person').factory('personService', personService);

    personService.$inject = ['$scope', 'sendRequest'];

    function personService($scope, sendRequest){
    var services =  {
            getPersonList: getPersonList
        };
        return services;

        function getPersonList(params){
            var url = '/People/personList/';
            return sendRequest.post(url, {}, params);
        }
    }
    


})();
