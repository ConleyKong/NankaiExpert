(function(){
    "use strict";

    angular.module('person').factory('sendRequest', sendRequest);

    angular.module('home').factory('sendRequest', sendRequest);

    angular.module('award').factory('sendRequest', sendRequest);

    angular.module('person_detail').factory('sendRequest', sendRequest);

    angular.module('paper').factory('sendRequest', sendRequest);

    angular.module('person_paper').factory('sendRequest', sendRequest);

    angular.module('person_project').factory('sendRequest', sendRequest);

    angular.module('patent').factory('sendRequest', sendRequest);

    angular.module('project').factory('sendRequest', sendRequest);

    angular.module('inter_changes').factory('sendRequest', sendRequest);

    angular.module('lab').factory('sendRequest', sendRequest);

    angular.module('event').factory('sendRequest', sendRequest);

    angular.module('audit').factory('sendRequest', sendRequest);

    angular.module('user').factory('sendRequest', sendRequest);

    sendRequest.$inject = ['$q', '$http'];

    // 请求数据的接口,加密和认证
    function sendRequest($q, $http){
        'use strict';
        var simba = {};
        $('body').showLoading();
        //发送请求
        simba.send = function(method, url, params, data){
            var deferred = $q.defer();
            $http({
                url: url,
                method: method,
                params: params,
                data: data,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                timeout: 30000
            }).then(function(resp){
                $('body').hideLoading();
                deferred.resolve(resp.data);
            },function(resp){
                $('body').hideLoading();
                deferred.reject(resp);
            });

            return deferred.promise;
        };

        simba.get = function(url, params){
            return this.send('GET', url, params, {});
        };

        simba.post = function(url, params, data){
            return this.send('POST', url, params, data);
        };

        simba.put = function(url, params, data){
            return this.send('PUT', url, params, data);
        };
        simba.delete = function(url, params){
            return this.send('DELETE', url, params, {});
        };

        return simba;
    }    


})();
