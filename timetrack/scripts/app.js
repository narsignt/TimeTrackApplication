var app = angular.module("myapp", ['ngCookies','angularUtils.directives.dirPagination','ngAnimate', 'ui.bootstrap','ui.router','ngTable']);

function parseDate(input) {
    return Date.parse(input);
}

app.filter("dateRangeFilter", function() {
    return function(userdetails, from, to) {
        var df = parseDate(from);
        var dt = parseDate(to);
        var result = [];
        for (var i = 0; i < userdetails.length; i++) {
            var date_bet = userdetails[i].datetime;
            if (date_bet > df && dt > date_bet) {
                result.push(userdetails[i]);
            }
        }
        return result;
    };
});
app.filter('sumByKey', function () {
    return function (data, key) {
        if (typeof (data) === 'undefined' || typeof (key) === 'undefined') {
            return 0;
        }

        var sum = 0;
        for (var i = data.length - 1; i >= 0; i--) {
            sum += parseInt(data[i][key]);
        }

        return sum;
    };
})
app.directive('noSpecialChar', function() {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function(scope, element, attrs, modelCtrl) {
            modelCtrl.$parsers.push(function(inputValue) {
                if (inputValue == undefined)
                    return ''
                cleanInputValue = inputValue.replace(/[^\w\s]/gi, '');
                if (cleanInputValue != inputValue) {
                    modelCtrl.$setViewValue(cleanInputValue);
                    modelCtrl.$render();
                }
                return cleanInputValue;
            });
        }
    }
});
app.service('TaskService',function($http, $q, $log){
    var timetrackdetails = [];
    var totaltimetrack = [];
    var projectDetails;

    this.list = function(timetrackdetails,userdetails){
        if(userdetails[0].role == "user"){
            angular.forEach(timetrackdetails, function(timetrack){
                var tempData = {
                    "id" : timetrack.id,
                    "project_id" : timetrack.project_id,
                    "project_name" : timetrack.project_name,
                    "date" : timetrack.date,
                    "hours" : timetrack.hours,
                    "minutes" : timetrack.minutes,
                    "description" : timetrack.description
                };
                totaltimetrack.push(tempData);
            });
            //totaltimetrack = timetrackdetails;
            //  return totaltimetrack;
            return totaltimetrack;
        }else{
            totaltimetrack = timetrackdetails;
            return totaltimetrack;
        }
    }
    this.saveNewTrack = function(newtrack,uid,description){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrack/timetrackInsert.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                'date': newtrack.date,
                'projectid': newtrack.projectinfo,
                'hours': newtrack.hours,
                'minutes': newtrack.minutes,
                'user_id': uid,
                'description': description,
                'track_id': newtrack.track_id
            }
        })
            .success(function(response, status, headers, config) {
                deferred.resolve({
                    response: response
                });
            })
            .error(function(response){
                deferred.reject(response);
                $log.error(response);
            });
        return deferred.promise;
    }


    this.timetrackInfo= function(uid,uname,UserName){
        var url = "/timetrack/timetrack.php?user_id="+uid+"&uname="+uname+"&UserName="+UserName;
        console.log("url "+url);
        var deferred = $q.defer();
        $http.get(url)
            .success(function(response){
                deferred.resolve({
                    projectdetails: response.projectdetails,
                    timetrackdetails: response.timetrackdetails,
                    userdetails: response.userdetails,
                    totalresponse: response
                });
            })
            .error(function(response){
                deferred.reject(response);
                $log.error(response);
            });
        return deferred.promise;
    }


    this.projectList = function(projectdetails){
        this.projectDetails = projectdetails;
        return this.projectDetails;
    }
    this.plist = function(){
        return this.projectDetails;
    }

    this.addprojectName = function(projectname){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrack/projectinsert.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                'project_name': projectname
            }
        })
            .success(function(response, status, headers, config) {
                deferred.resolve({
                    projectdetails: response.projectdetails
                });
            }).error(function(data){
                deferred.reject(response);
                $log.error(response);
            });
        return deferred.promise;

    }

    this.deleteprojectName = function(projectid){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrack/projectupdate.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                'project_id': projectid
            }
        })
            .success(function(response, status, headers, config) {
                deferred.resolve({
                    projectdetails: response.projectdetails
                });
            }).error(function(data){
                deferred.reject(response);
                $log.error(response);
            });
        return deferred.promise;
    }

    this.delete = function(track_id,uid){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrack/delete_timetrack_record.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                'track_id': track_id,
                'user_id': uid
            }
        })
            .success(function(response, status, headers, config) {
                deferred.resolve({
                    response: response
                });
            })
            .error(function(response){
                deferred.reject(response);
                $log.error(response);
            });
        return deferred.promise;
    }



    this.deparam = function (querystring) {
        var deferred = $q.defer();
        querystring = querystring.substring(querystring.indexOf('?')+1).split('&');
        var params = {}, pair, d = decodeURIComponent;
        for (var i = querystring.length - 1; i >= 0; i--) {
            pair = querystring[i].split('=');
            params[d(pair[0])] = d(pair[1]);
        }
        deferred.resolve({
            userdetails: params
        });

        return deferred.promise;
    };

});
app.controller("mycontroller",['$rootScope','$location','$scope','$http','TaskService','$filter','$window','ngTableParams', function($rootScope,$location,$scope,$http,TaskService,$filter,$window,ngTableParams) {
    console.log(($location.absUrl()));
    TaskService.deparam($location.absUrl()).then(function(details){
        $rootScope.globals={
            currentUser:{
                uid:details.userdetails.uid,
                uname:details.userdetails.uname,
				UserName:details.userdetails.UserName,
            }
        };
        // Create Base64 Object
        var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
        $scope.uid = Base64.decode(details.userdetails.uid);
        $scope.uname = Base64.decode(details.userdetails.uname);
		$scope.UserName = Base64.decode(details.userdetails.UserName);
        //$scope.uid = details.userdetails.uid;
        //$scope.uname = details.userdetails.uname;
        TaskService.timetrackInfo(details.userdetails.uid,details.userdetails.uname,details.userdetails.UserName).then(function(details){
            //$scope.newtrack.projectDetails = details.projectdetails
            if(details.totalresponse.error == "undefined"){
                $window.location.href = 'https://italent.jiveon.com/welcome';
            }else{
                $scope.newtrack.projectDetails = TaskService.projectList(details.projectdetails);
                $scope.userdetails = TaskService.list(details.timetrackdetails,details.userdetails);
                if(details.userdetails[0].role == "user"){
                    $scope.isadmin = false;
                    /*document.getElementById("addproject").style.display = "none";
                     document.getElementById("projectsubmit").style.display = "none";
                     document.getElementById("deleteproject").style.display = "none";*/
                }else{
                    $scope.isadmin = true;
                }
            }


        });
        //$cookieStore.put('globals',$rootScope.globals);
    });

    $scope.submit = function(){
        if($scope.newtrack.date !=null && $scope.newtrack.description !=null && $scope.newtrack.hours !=null && $scope.newtrack.projectinfo !=null){
            description=encodeURIComponent($scope.newtrack.description);
            if(angular.isUndefined($scope.newtrack.minutes)){
                $scope.newtrack.minutes = '00' ;
            }
            $scope.newtrack.date = $filter('date')($scope.newtrack.date, "yyyy-MM-dd");
            TaskService.saveNewTrack($scope.newtrack,$scope.uid,description).then(function(details){
                //$scope.userdetails = TaskService.list(details.response.timetrackdetails,details.response.userdetails);
                $scope.userdetails = details.response.timetrackdetails;
                if(details.response.userdetails[0].role == "user"){
                    $scope.isadmin = false;
                }else{
                    $scope.isadmin = true;
                }
                $scope.newtrack.date = "";
                $scope.newtrack ={};
                $scope.newtrack.projectDetails = TaskService.plist();
            });

        }
    };

    $scope.edit = function(trackdetails){
        $scope.newtrack.date = trackdetails.date;
        $scope.newtrack.hours = trackdetails.hours;
        $scope.newtrack.minutes = trackdetails.minutes;
        $scope.newtrack.projectinfo = trackdetails.project_id;
        $scope.newtrack.description = trackdetails.description;
        $scope.newtrack.track_id = trackdetails.id;
        $scope.newtrack.uid = trackdetails.user_id;

    }; 
	$scope.delete =function(user){

        TaskService.delete(user.id,user.user_id).then(function(details){
            //$scope.userdetails = TaskService.list(details.response.timetrackdetails,details.response.userdetails);
            $scope.userdetails = details.response.timetrackdetails;
            if(details.response.userdetails[0].role == "user"){
                $scope.isadmin = false;
            }else{
                $scope.isadmin = true;
            }
            $scope.newtrack.date = "";
            $scope.newtrack ={};
            $scope.newtrack.projectDetails = TaskService.plist();
        });
    };

    $scope.deleteproject = function(){
        if ($window.confirm("Are you Sure You Want To Delete This Project ")) {
            $scope.Message = "You clicked YES.";
            TaskService.deleteprojectName($scope.newtrack.projectinfo).then(function(details){
                $scope.newtrack.projectDetails = TaskService.projectList(details.projectdetails);
            });
        } else {
            $scope.Message = "You clicked NO.";
        }

    };
	$scope.projectsubmit = function(){
        TaskService.addprojectName($scope.projectname).then(function(details){
            $scope.newtrack.projectDetails = TaskService.projectList(details.projectdetails);
            $scope.projectname = '';
        });
        $scope.isprojecttext = false;
    };

    $scope.reset = function(){
        $scope.newtrack ={};
        $scope.newtrack.projectDetails = TaskService.plist();
    };
    $scope.getBtDates = function(dt1,dt2,userdetails){
        var df = parseDate(dt1);
        var dt = parseDate(dt2);
        var result = [];
        for (var i = 0; i < userdetails.length; i++) {
            var date_bet = parseDate(userdetails[i].date);

            if (date_bet > df && dt > date_bet) {
                result.push(userdetails[i]);
            }
        }
        $scope.userdetails = result;

    };
    $scope.newtrack={};
    $scope.newtrack.projectDetails=[];
    $scope.isprojecttext = false;


    $scope.today = function () {
        $scope.newtrack.date = new Date();
    };
    $scope.dateformat="yyyy-MM-dd";
    $scope.today();
    $scope.showcalendar = function ($event) {
        $scope.showdp = true;
    };
    $scope.showdp = false;
    $scope.dtmax = new Date();

    $scope.addproject = function(){
        $scope.isprojecttext = true;
    };
    $scope.toggleaddproject = function() {
        $scope.addproject = $scope.addproject === false ? true: false;
    };

    $scope.sort = function(keyname){
        $scope.sortKey = keyname;   //set the sortKey to the param passed
        $scope.reverse = !$scope.reverse; //if true make it false and vice versa
    }

    $scope.exportData = function () {
        alasql('SELECT project_name, date, hours, minutes, description, email_id, u_name, updated_date INTO XLSX("timetrack.xlsx",{headers:true}) FROM ?',[$scope.userdetails]);
    };
    $scope.today = function() {
        $scope.dt2 = new Date();
        var previousMonth = new Date($scope.dt2);
        $scope.dt1=previousMonth.setMonth($scope.dt2.getMonth()-1);
    };
    $scope.today();
    $scope.toggleMin = function() {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();
    $scope.maxDate = new Date();

    $scope.open1 = function($event) {
        $scope.status1.opened = true;
    };

    $scope.open2 = function($event) {
        $scope.status2.opened = true;
    };

    $scope.setDate = function(year, month, day) {
        $scope.dt1 = new Date(year, month, day);
        $scope.dt2 = new Date(year, month, day);

    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['yyyy-MM-dd'];
    $scope.format = $scope.formats[0];

    $scope.status1 = {
        opened: false
    };

    $scope.status2 = {
        opened: false
    };


}]);
