var app = angular.module("myapp", ['ngCookies','angularUtils.directives.dirPagination','ngAnimate', 'ui.bootstrap','ui.router','ngTable','angularMoment','angular-confirm']);

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
            if (date_bet >= df && dt >= date_bet) {
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
                    "description" : timetrack.description,
                    "email_id" : timetrack.email_id,
                    "u_name" : timetrack.u_name,
                    "first_name":timetrack.first_name,
                    "last_name":timetrack.last_name,
                    "user_id" : timetrack.user_id,
                    "updated_date" : timetrack.updated_date,
                    "empno":timetrack.empno,
                    "country":timetrack.country
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
    this.saveNewTrack = function(newtrack,description,user_id,sessionid){
        console.log(user_id+"dd");
        var deferred = $q.defer();
        var dataObj = {
                "date": newtrack.date,
                "projectid": newtrack.projectinfo,
                "hours": newtrack.hours,
                "minutes": newtrack.minutes,
                "description": description,
                "track_id": newtrack.track_id,
                "sessionid":sessionid,
                'user_id':user_id,
        };
        //$http.get('/timetrackdemo2/timetrackInsert.php', dataObj).
        // $http.post('/timetrackdemo2/timetrackInsert.php?hours='+newtrack.hours+'&user_id='+user_id+'&date='+newtrack.date+'&projectid='+newtrack.projectinfo+'&minutes='+newtrack.minutes+'&description='+description+'&track_id='+newtrack.track_id+'&sessionid='+sessionid).
        // success(function(response, status, headers, config) {
        //     deferred.resolve({
        //         response: response
        //     });
        // })
        // .error(function(data, status, headers, config) {
        //     alert( "failure message: " + JSON.stringify({data: data}));
        // });
        $http({
            method: 'POST',
            url: '/timetrackdemo2/timetrackInsert.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    if(p == "user_id"){
                        var user_id =encodeURIComponent(obj[p]);
                        str.push(encodeURIComponent(p) + "=" + user_id.replace('%00%00',''));
                    }else{
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }

                return str.join("&");
            },
            data: {
                "date": newtrack.date,
                "projectid": newtrack.projectinfo,
                "hours": newtrack.hours,
                "minutes": newtrack.minutes,
                "description": description,
                "track_id": newtrack.track_id,
                "sessionid":sessionid,
                'user_id':user_id
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


    this.timetrackInfo= function(uid,uname,UserName,givenName,lastName,EmpNumber,Country){
        var url = "/timetrackdemo2/timetrack.php?user_id="+uid+"&uname="+uname+"&UserName="+UserName+"&givenName="+givenName+"&familyName="+lastName+"&EmpNumber="+EmpNumber+"&Country="+Country;
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
	this.weektrackInfo= function(weeks, user_id,isadmin,sessionid){
		//var url = "/timetrack/update_week.php?data="+weeks+"&user_id="+user_id+"&isadmin="+isadmin;
		//console.log("week:"+url);
		 var deferred = $q.defer();
		  $http({
            method: 'POST',
            url: '/timetrackdemo2/update_week.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    if(p == "user_id"){
                        var user_id =encodeURIComponent(obj[p]);
                        str.push(encodeURIComponent(p) + "=" + user_id.replace('%00%00',''));
                    }else{
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }
                return str.join("&");
            },
            data: {
                'data': weeks,
				'user_id': user_id,
				'isadmin': isadmin,
                'sessionid': sessionid
            }
        })
            .success(function(response){
                deferred.resolve({
                    weeklytrackdetails: response
                });
            })
            .error(function(response){
                deferred.reject(response);
                $log.error(response);
            });
			return deferred.promise;
	}

	/*this.getMonthlyTrackInfo= function(startDate,endDate,uid){
		var url = "/timetrack/update_week.php?user_id="+uid+"&start_date="+startDate+"&end_date="+endDate;
		var deferred = $q.defer();
		   $http.get(url)
            .success(function(response){
                deferred.resolve({
                    weektrackdetails: response

                });
            })
            .error(function(response){
                deferred.reject(response);
                $log.error(response);
            });
			return deferred.promise;
	}*/
	this.getMonthlyTrackInfo= function(startDate,endDate,uid){
		var deferred = $q.defer();
		   $http({
            method: 'POST',
            url: '/timetrackdemo2/week_track.php',
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    if(p == "user_id"){
                        var user_id =encodeURIComponent(obj[p]);
                        str.push(encodeURIComponent(p) + "=" + user_id.replace('%00%00',''));
                    }else{
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }
                return str.join("&");
            },
            data: {
                'user_id': uid,
				'start_date': startDate,
				'end_date': endDate
            }
        })
            .success(function(response){
                deferred.resolve({
                    weektrackdetails: response

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

    this.addprojectName = function(projectname,sessionid){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrackdemo2/projectinsert.php',
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
                'project_name': projectname,
                'sessionid' : sessionid
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

    this.deleteprojectName = function(projectid,sessionid,project_name){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrackdemo2/projectupdate.php',
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
                'project_id': projectid,
                'sessionid' : sessionid,
                'project_name' : project_name
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

    this.delete = function(track_id,uid,isadmin,start_date,end_date,sessionid){
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrackdemo2/delete_timetrack_record.php',
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
                'user_id': uid,
                'isadmin': isadmin,
                'start_date' : start_date,
                'end_date' : end_date,
                'sessionid' : sessionid
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
 //Added by sunil for get session log data
 this.getAuditLog = function(start_date,end_date){
    console.log(start_date);
    console.log(end_date);
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: '/timetrackdemo2/sessionLog.php',
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
                 'start_date' : start_date,
                 'end_date' : end_date
            }
        })
            .success(function(response, status, headers, config) {
                deferred.resolve({
                    auditResponse: response
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
    this.filterBtDates = function(dt1,dt2,userdetails){
        var df = parseDate(dt1);
        var dt = parseDate(dt2);
        var result = [];
        if(dt1 > dt2){
            //alert("To Date should be greater than From date");
            $scope.alertDialog = true;
            // $scope.datemsg = "To Date should be greater than From date";

        }
        for (var i = 0; i < userdetails.length; i++) {
            var date_bet = parseDate(userdetails[i].date);

            if (date_bet >= df && dt >= date_bet) {
                result.push(userdetails[i]);
            }

        }
        return result;
    };

    this.individualweek = function(week,date,isadmin,weeklyInfo,message){
        weeks = [];
        for (var j = 0; j < 8; j++) {
            if (j == 0) {
                weekStartDate = "";
                weekEndDate = "";
               // if(isadmin){
                    //weekStartDate = moment(date).add(1, 'days');
                    weekStartDate = moment(date).format("MM/DD");
                    weekEndDate = moment(date).add(6, 'days');
                    weekEndDate = moment(weekEndDate).format("MM/DD");
                /*}else{
                    weekStartDate = moment(date).add(1, 'days');
                    weekStartDate = moment(weekStartDate).format("MM/DD");
                    weekEndDate = moment(date).add(5, 'days');
                    weekEndDate = moment(weekEndDate).format("MM/DD");
                }*/
                var day = {
                    "date": weekStartDate + " to " + weekEndDate,
                    "placeholder": weekStartDate + " to " + weekEndDate,
                    "readonly": true,
                    "flag": true
                };
                weeks.push(day);
                continue;
            } /*else if(j <= gapInFirstWeek) {
                if($scope.isadmin){
                    var day = {
                        "date": moment(date).format("DD-MM-YYYY"),
                        "placeholder": moment(date).format("DD-MM-YYYY"),
                        "readonly": true,
                        "flag": false
                    };
                    weeks.push(day);
                }else if (j != 1 && j !=7) {
                    var day = {
                        "date": moment(date).format("DD-MM-YYYY"),
                        "placeholder": moment(date).format("DD-MM-YYYY"),
                        "readonly": true,
                        "flag": false
                    };
                    weeks.push(day);
                }
            }*/ else {
                //if(isadmin){
                    if (weeklyInfo.length == 0 || (message === "No Records found for user")) {
                        var day = {
                            "date": moment(date).format("DD-MM-YYYY"),
                            "placeholder": moment(date).format("DD-MM-YYYY"),
                            "readonly": false,
                            "flag": false
                        };
                        weeks.push(day);
                    } else {
                        date = moment(date).format("YYYY-MM-DD");
                        for (z = 0; z < weeklyInfo.length; z++) {
                            if (weeklyInfo[z].date === date) {
                                //console.log("JS date is " + date + " and respose date is " + weeklyInfo[z].date);
                                var day = {
                                    "date": moment(date).format("DD-MM-YYYY"),
                                    "placeholder": moment(date).format("DD-MM-YYYY"),
                                    "readonly": false,
                                    "time": weeklyInfo[z].hours,
                                    "flag": false
                                };
                                weeks.push(day);
                                weeklyInfo.splice(z, 1);//breaking
                                break;
                            }
                        }
                        if (weeks[weeks.length - 1].date != moment(date).format("DD-MM-YYYY")) {
                            var day = {
                                "date": moment(date).format("DD-MM-YYYY"),
                                "placeholder": moment(date).format("DD-MM-YYYY"),
                                "readonly": false,
                                "flag": false
                            };
                            weeks.push(day);
                        }
                    }

                /*}else if(j != 1 && j !=7) {
                    if (weeklyInfo.length == 0 || (message === "No Records found for user")) {
                        var day = {
                            "date": moment(date).format("DD-MM-YYYY"),
                            "placeholder": moment(date).format("DD-MM-YYYY"),
                            "readonly": false,
                            "flag": false
                        };
                        weeks.push(day);
                    } else {
                        date = moment(date).format("YYYY-MM-DD");
                        for (z = 0; z < weeklyInfo.length; z++) {
                            if (weeklyInfo[z].date === date) {
                                console.log("JS date is " + date + " and respose date is " + weeklyInfo[z].date);
                                var day = {
                                    "date": moment(date).format("DD-MM-YYYY"),
                                    "placeholder": moment(date).format("DD-MM-YYYY"),
                                    "readonly": false,
                                    "time": weeklyInfo[z].hours,
                                    "flag": false
                                };
                                weeks.push(day);
                                weeklyInfo.splice(z, 1);//breaking
                                break;
                            }
                        }
                        if (weeks[weeks.length - 1].date != moment(date).format("DD-MM-YYYY")) {
                            var day = {
                                "date": moment(date).format("DD-MM-YYYY"),
                                "placeholder": moment(date).format("DD-MM-YYYY"),
                                "readonly": false,
                                "flag": false
                            };
                            weeks.push(day);
                        }
                    }
                }*/
            }if(j!=7) {
                date = moment(date).add(1, 'days');
            }
        }
        var weeklydata = {
            "week" : weeks,
            "date" : date,
            "weeklyInfo" : weeklyInfo
        };
        return weeklydata;

    };

});
app.controller("mycontroller",['$rootScope','$location','$scope','$http','TaskService','$filter','$window','ngTableParams','$timeout','$confirm', function($rootScope,$location,$scope,$http,TaskService,$filter,$window,ngTableParams,$timeout,$confirm) {
    console.log(($location.absUrl()));
    TaskService.deparam($location.absUrl()).then(function(details){
        $rootScope.globals={
            currentUser:{
                uid:details.userdetails.user_id,
                uname:details.userdetails.uname,
				UserName:details.userdetails.UserName,
                EmpNumber:details.userdetails.EmpNumber,
                Country:details.userdetails.Country
            }
        };
        console.log($rootScope.globals.currentUser);
        currentdate = new Date();

        $scope.current_month = moment(currentdate).format("MMMM");
        $scope.current_year = moment(currentdate).format("YYYY");
		//$scope.showDialog = true;

        // Create Base64 Object
        var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
        $scope.uid = Base64.decode(details.userdetails.user_id);

        $scope.uname = Base64.decode(details.userdetails.uname);
		$scope.UserName = Base64.decode(details.userdetails.UserName);

        //$scope.uid = details.userdetails.uid;
        //$scope.uname = details.userdetails.uname;

        TaskService.getAuditLog(moment(new Date()).subtract(1,'days').format('YYYY-MM-DD'),moment(new Date()).format('YYYY-MM-DD')).then(function(auditDetails){
            $scope.auditlogdetails=auditDetails.auditResponse.sessionarray;
        });
        TaskService.timetrackInfo(details.userdetails.user_id,details.userdetails.uname,details.userdetails.UserName,details.userdetails.givenName,details.userdetails.familyName,details.userdetails.EmpNumber,details.userdetails.Country).then(function(responseDetails){
            console.log(responseDetails);
		   //$scope.newtrack.projectDetails = details.projectdetails
            if(responseDetails.totalresponse.error == "undefined"){
                $window.location.href = 'https://italent.jiveon.com/welcome';
            }else{

                $scope.newtrack.projectDetails = TaskService.projectList(responseDetails.projectdetails);
                $scope.sessionid = responseDetails.totalresponse.sessionid;
                $scope.userdetails = TaskService.list(responseDetails.timetrackdetails,responseDetails.userdetails);
				$scope.entire_data = $scope.userdetails;
                if(responseDetails.userdetails[0].role == "user"){
                    $scope.isadmin = false;
					$scope.current_username = responseDetails.userdetails[0].u_name+"!";
					//$scope.current_usermail = details.userdetails[0].email_id;
                    //weekly data for user
                    currentdate = new Date();
                    current_month = moment(currentdate).format("MM");
                    current_year = moment(currentdate).format("YYYY");
                    startDate = moment([current_year, current_month - 1]).toDate();
                    startDayOfFirstWeek = moment(startDate).startOf('week').toDate();
                    endDate = moment(startDate).endOf('month').toDate();
                    startDayOfLastWeek = moment(endDate).startOf('week').toDate();
                    lastDayOfLastWeek = moment(endDate).endOf('week').toDate();
                    totalDayInMonth = new Date(current_year, current_month, 0).getDate();
                    gapInFirstWeek = moment(startDate).diff(startDayOfFirstWeek, 'days');
                    gapInLastWeek = 6 - moment(endDate).diff(startDayOfLastWeek, 'days');
                    weekTotal = (totalDayInMonth + gapInFirstWeek + gapInLastWeek) / 7;
                    console.log("WeekTotal : " + weekTotal);
                    weeks = [];
                    month = [];
                    weeklyInfo = [];
					candidate={};
					candidates=[];
                    var str = "";
                    date = startDayOfFirstWeek;//moment(startDate).format("MM")
                    $scope.month = "";
                    $scope.startDayOfFirstWeek = moment(startDayOfFirstWeek).format("YYYY-MM-DD");
                    $scope.lastDayOfLastWeek = moment(lastDayOfLastWeek).format("YYYY-MM-DD");
                    $scope.weekTotal = weekTotal;
                    TaskService.getMonthlyTrackInfo($scope.startDayOfFirstWeek, $scope.lastDayOfLastWeek, $scope.uid).then(function (details) {
                        console.log(details.weektrackdetails.weekdetailsarray);
                        if (details.weektrackdetails.message === "Records found successfully") {
                            weeklyInfo = details.weektrackdetails.weekdetailsarray;
                        }
                        for (var i = 0; i < weekTotal; i++) {
                            weekdate = TaskService.individualweek(i,date,$scope.isadmin,weeklyInfo,details.weektrackdetails.message);
                            month.push(weekdate.week);
                            date = moment(weekdate.date).add(1, 'days');
                            weeklyInfo = weekdate.weeklyInfo;
                        }
                        //$scope.month = month;
						 console.log(month);
                    candidate={"userName":responseDetails.userdetails[0].email_id,"diableForAdmin":false,"data":month};
                    //candidate[$scope.wuname]=month;
                    candidates.push(candidate);
                    console.log(candidate);
                     console.log(candidates);
                    //  var name=$scope.wuname;
                    // candidate[name].push(month);
                    console.log(candidates);
                    $scope.month = candidates;
                    console.log($scope.month);
                    });

                    /*document.getElementById("addproject").style.display = "none";
                     document.getElementById("projectsubmit").style.display = "none";
                     document.getElementById("deleteproject").style.display = "none";*/

                }else{
                    $scope.isadmin = true;
					$scope.current_usermail = responseDetails.userdetails.email_id;
					$scope.current_username = responseDetails.userdetails[0].u_name+"!";
                    $scope.usernames = responseDetails.userdetails;
                    var alluser ={
                        "user_id" : "alluser",
                        "u_name" : "All Users"
                    };
                    var indianuser ={
                        "user_id" : "indianuser",
                        "u_name" : "India Users"
                    };
                    var ususer ={
                        "user_id" : "ususer",
                        "u_name" : "US Users"
                    };
                    $scope.usernames.splice(0, 0, alluser);
                    $scope.usernames.splice(1, 0, indianuser);
                    $scope.usernames.splice(2, 0, ususer);
                }
            }


        });
	
        //$cookieStore.put('globals',$rootScope.globals);
    });

    $scope.validHour = function(data){
        if(data.day.time < 0 || data.day.time > 24){
             data.day.time = 0;
            alert("Hours per day cannot exceed 24");
			//$scope.hoursmsg = "Hours must be less than 24"; 
			//$scope.showDialog = false;
            
        }
    };

    $scope.auditlog = function(){
        TaskService.getAuditLog(moment($scope.logfrom).format("YYYY-MM-DD"),moment($scope.logto).format("YYYY-MM-DD") ).then(function(details){
            console.log(details);
            $scope.auditlogdetails = details.auditResponse.sessionarray; 
			 $scope.logto = moment(new Date()).format('YYYY-MM-DD');
        $scope.logfrom = moment($scope.logto).subtract(1,'days').format('YYYY-MM-DD');
			
        });
    };
	 $scope.auditlog();
    $scope.monthfunction = function(){//adminfromdate,admintodate,entire_data
		$scope.month="";


        if(!angular.isUndefined($scope.adminfromdate) && !angular.isUndefined($scope.admintodate) && !angular.isUndefined($scope.wuname) && $scope.wuname != null ) {
            //startDate = moment([$scope.wyear, $scope.wmonth - 1]).toDate();
            startDate = $scope.adminfromdate;
            $scope.selecteduser = $("#username option:selected").html();
            startDayOfFirstWeek = moment($scope.adminfromdate).startOf('week').toDate();
            //endDate = moment(startDate).endOf('month').toDate();
            endDate = $scope.admintodate;
            startDayOfLastWeek = moment(endDate).startOf('week').toDate();
            lastDayOfLastWeek = moment($scope.admintodate).endOf('week').toDate();
            totalDayInMonth = new Date($scope.wyear, $scope.wmonth, 0).getDate();
            gapInFirstWeek = moment(startDate).diff(startDayOfFirstWeek, 'days');
            gapInLastWeek = 6 - moment(endDate).diff(startDayOfLastWeek, 'days');
            weekTotal = (moment(lastDayOfLastWeek).diff(moment(startDayOfFirstWeek), 'days') + 1)/7;
            //weekTotal = (totalDayInMonth + gapInFirstWeek + gapInLastWeek) / 7;
            console.log("WeekTotal : " + weekTotal);
            weeks = [];
            month = [];
            weeklyInfo = [];
            candidate={};
            candidates=[];
            var str = "";
            date = startDayOfFirstWeek;
            if(weekTotal>4){
                alert("Data has exceeded 1 month. Please use the Download Data button to generate your report.");
                $scope.admintodate=moment(new Date()).format('YYYY-MM-DD');
                $scope.adminfromdate=moment(new Date()).subtract(1,'months').format('YYYY-MM-DD');
                startDayOfFirstWeek=moment(new Date()).subtract(1,'months').startOf('week').toDate();
                lastDayOfLastWeek=moment(new Date()).endOf('week').toDate();
                date = startDayOfFirstWeek;
                weekTotal=(moment(lastDayOfLastWeek).diff(moment(startDayOfFirstWeek), 'days') + 1)/7;
            }
            TaskService.getMonthlyTrackInfo(moment(startDayOfFirstWeek).format("YYYY-MM-DD"), moment(lastDayOfLastWeek).format("YYYY-MM-DD"), $scope.wuname).then(function (details) {
                //console.log(details);
                if($scope.wuname !="alluser" && $scope.wuname !="indianuser" && $scope.wuname !="ususer") {
                    if (details.weektrackdetails.message === "Records found successfully") {
                        weeklyInfo = details.weektrackdetails.weekdetailsarray;

                        
                    }
                    for (var i = 0; i < weekTotal; i++) {
                        weekdate = TaskService.individualweek(i, date, $scope.isadmin, weeklyInfo, details.weektrackdetails.message);
                        console.log(weekdate);
                        month.push(weekdate.week);
                        console.log(month);
                        date = moment(weekdate.date).add(1, 'days');
                        console.log(date);
                        weeklyInfo = weekdate.weeklyInfo;
                        console.log(weeklyInfo);
                    }
                    console.log(month);
                    candidate={"userName": $scope.wuname,"diableForAdmin":false,"data":month};
                    //candidate[$scope.wuname]=month;
                    candidates.push(candidate);
                    console.log(candidate);
                     console.log(candidates);
                    //  var name=$scope.wuname;
                    // candidate[name].push(month);
                    console.log(candidates);
                    $scope.month = candidates;
                    console.log($scope.month);
                }else
                {


                    if (details.weektrackdetails.message === "Records found successfully") {

                        weeklyInfo = details.weektrackdetails.weekdetailsarray;

                    }
                    if(details.weektrackdetails.message === "No Records found for user")
                    {
                      alert("No Records found for user");
                    }
                    var sortWeeklyInfo={};
                    for(i=0;i<weeklyInfo.length;i++)
                    {
                        var name=weeklyInfo[i].email_id;

                        if(typeof (sortWeeklyInfo[name])=="undefined"){
                            sortWeeklyInfo[name]=[];
                        }
                        sortWeeklyInfo[name].push(weeklyInfo[i]);

                    }
                    angular.forEach(sortWeeklyInfo,function(value,key){

                            if (details.weektrackdetails.message === "Records found successfully") {
                                weeklyInfo = value;
                                fname=value[0].first_name;
                                lname=value[0].last_name;
                            }

                            for (var j = 0; j < weekTotal; j++) {
                                weekdate = TaskService.individualweek(j, date, $scope.isadmin, weeklyInfo, details.weektrackdetails.message);
                                month.push(weekdate.week);
                                date = moment(weekdate.date).add(1, 'days');
                                weeklyInfo = weekdate.weeklyInfo;
                            }
                            candidate={"userName":key,"diableForAdmin":true,"data":month,"fname":fname,"lname":lname};
                            month = [];
                            weeklyInfo = [];
                            date=startDayOfFirstWeek;
                            candidates.push(candidate);
                    });
                   $scope.month = candidates;

                }
            });
        }else if(angular.isUndefined($scope.wyear)){
            $scope.wmonth = "";
            $scope.month =[];
        }
        else{
            $scope.month =[];
        }
    };

    $scope.updateWeek = function(week){
		$scope.showWeekDialog = false;
        dailyReport = [];
        uid ="";
        checkHour = false;
        for(i =1 ;i<week.length; i++){
            if(week[i].hasOwnProperty("time")){
				if(!angular.isUndefined(week[i].time)){
					var day = {
						"day" : week[i].date,
						"hours" : week[i].time
					}
					dailyReport.push(day);
				}else{
                checkHour = true;
                break;
				}
            }else{
				if(!angular.isUndefined(week[i].time)){
					var day = {
							"day" : week[i].date,
							"hours" : week[i].time
						}
						dailyReport.push(day);
				}
			}
        }
        if(checkHour == false) {
            var data = {};
            data['weeks'] = dailyReport;
            data['year'] = $scope.wyear;
            data['month'] = $scope.wmonth;
            //console.log(data);
            $scope.data = JSON.stringify(data);
            console.log("data" + data);
            if ($scope.isadmin) {
                uid = $scope.wuname;
            } else {
                uid = $scope.uid;
            }
            console.log();
            TaskService.weektrackInfo($scope.data, uid, $scope.isadmin, $scope.sessionid).then(function (details) {
                if (details.weeklytrackdetails.message === "Updated Successfully") {
                    $scope.weekmsg = "Updated Successfully";
                    $scope.showWeekDialog = true;
                    $scope.userdetails = "";
                    userdetails = details.weeklytrackdetails.timetrackdetails;//TaskService.list(details.weeklytrackdetails.timetrackdetails,details.weeklytrackdetails.userdetails);
                    $scope.entire_data = userdetails;
                    $scope.userdetails = TaskService.filterBtDates($scope.dt1,$scope.dt2,userdetails);

                   // $confirm({text: 'User details updated successfully.',  ok: 'Yes'});
                } else {
                    $scope.weekmsg = "Error while updating in DB. Please try again later";
                    $scope.showWeekDialog = true;
                   // $confirm({text: 'Server is down. Please try later.',  ok: 'Yes'});
                }

            });
        }else{
            //remove blur
			$scope.weekmsg = "Hours per day cannot exceed 24";
			$scope.showWeekDialog = true;
			
        }
    };

	$scope.getSumOfWeekHours = function(week){
        total =0;
        for(i=0;i<week.length;i++){
            if((!angular.isUndefined(week[i].time)) && (week[i].time != null) && (week[i].time != "")) {
                total += parseInt(week[i].time);
				if(total<40){
					
				}else if(total>40){
					
				}
            }
        }
        return total;
		
    };

    $scope.submit = function(){
        if($scope.newtrack.date !=null && $scope.newtrack.description !=null && $scope.newtrack.hours !=null && $scope.newtrack.projectinfo !=null){
            $scope.showDialog = true;
			$scope.submitmsg = "Submitted Successfully";
			//alert("Submitted Successfully");
			description=encodeURIComponent($scope.newtrack.description);
            if(angular.isUndefined($scope.newtrack.minutes)){
                $scope.newtrack.minutes = '00' ;
            }
            $scope.newtrack.date = $filter('date')($scope.newtrack.date, "yyyy-MM-dd");
            console.log($scope.uid);

            TaskService.saveNewTrack($scope.newtrack,description,$scope.uid,$scope.sessionid).then(function(details){
                //$scope.userdetails = TaskService.list(details.response.timetrackdetails,details.response.userdetails);
                $scope.userdetails = details.response.timetrackdetails;
				$scope.entire_data = $scope.userdetails;
				console.log("submit"+$scope.entire_data.length);
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
	/*$scope.refreshdata = function () {
		$scope.userdetails = userdetails;

	$scope.tableParams.total(userdetails.length);
    $scope.tableParams.reload();
	}*/
    $scope.edit = function(trackdetails){
        $scope.shouldBeOpen = true;
		if(trackdetails.hours.charAt(0) == "0"){
			trackdetails.hours = trackdetails.hours.slice(1);
		}
        $scope.newtrack.date = trackdetails.date;
        $scope.newtrack.hours = trackdetails.hours;
        $scope.newtrack.minutes = trackdetails.minutes;
        $scope.newtrack.projectinfo = trackdetails.project_id;
        $scope.newtrack.description = trackdetails.description;
        $scope.newtrack.track_id = trackdetails.id;
        $scope.newtrack.uid = trackdetails.user_id;

    }; 
	$scope.delete =function(user) {
        isadmin = "";
        if($scope.isadmin){
            isadmin = true;
            startDayOfFirstWeek = "";
            lastDayOfLastWeek = "";
        }else{
            isadmin = false;
            startDayOfFirstWeek = $scope.startDayOfFirstWeek;
            lastDayOfLastWeek = $scope.lastDayOfLastWeek;
        }
        $confirm({text: 'Are you sure you want to delete this record?',  ok: 'Yes', cancel: 'No'})
            .then(function() {
                TaskService.delete(user.id,user.user_id,isadmin,startDayOfFirstWeek,lastDayOfLastWeek,$scope.sessionid).then(function(details){
                    //$scope.userdetails = TaskService.list(details.response.timetrackdetails,details.response.userdetails);
                    $scope.userdetails = details.response.timetrackdetails;
                    /*if(details.response.userdetails[0].role == "user"){
                     $scope.isadmin = false;
                     }else{
                     $scope.isadmin = true;
                     }*/
                    $scope.newtrack.date = "";
                    $scope.newtrack ={};
                    $scope.newtrack.projectDetails = TaskService.plist();
                    weeklyInfo = [];
                    month = [];
                    candidate={};
                    candidates=[];
                    if($scope.isadmin){
                        $scope.month =[];
                        $scope.wyear = "";
                        $scope.wmonth = "";
                        $scope.wuname = "";

                    }else {
                        day = new Date(startDayOfFirstWeek);
                        if (details.response.wmessage === "Records found successfully") {
                            weeklyInfo = details.response.weekdetailsarray;
                        }
                        for (var i = 0; i < parseInt($scope.weekTotal); i++) {
                            weekdate = TaskService.individualweek(i, day, $scope.isadmin, weeklyInfo, details.response.wmessage);
                            month.push(weekdate.week);
                            day = moment(weekdate.date).add(1, 'days');
                            weeklyInfo = weekdate.weeklyInfo;
                        }
                        candidate={"userName": $scope.wuname,"diableForAdmin":false,"data":month};
                        candidates.push(candidate);
                        $scope.month = candidates;
                        //$scope.month = month;
                    }

                });
            });
        $scope.isdeletedaydetail = true;

    };

    $scope.deleteproject = function(){
        if(!angular.isUndefined($scope.newtrack.projectinfo)) {
			
            $confirm({text: 'Are you sure you want to delete this project?', ok: 'Yes', cancel: 'No'})
                .then(function () {
                    TaskService.deleteprojectName($scope.newtrack.projectinfo, $scope.sessionid, $scope.projectname).then(function (details) {
                        $scope.newtrack.projectDetails = TaskService.projectList(details.projectdetails);
                    });
                });
            }else{
                alert("Please select the project to delete");
				//$scope.deleteDialog = true;
				//$scope.deletemsg = "Please select the Project to delete";
            }
    };
	$scope.projectsubmit = function(){
        if(angular.isDefined($scope.projectname)) {
            if($scope.projectname.length>0) {
                TaskService.addprojectName($scope.projectname,$scope.sessionid).then(function (details) {
                    $scope.newtrack.projectDetails = TaskService.projectList(details.projectdetails);
                    $scope.projectname = '';
                });
                $scope.isprojecttext = false;
            }else{
                alert("Project name should not be empty");
                $scope.projectname = '';
            }
        }else{
            alert("Project name should not be empty");
            $scope.projectname = '';
        }
    };

    $scope.reset = function(){
        $scope.newtrack ={};
        $scope.newtrack.projectDetails = TaskService.plist();
    };
	 $scope.exportData = function (userdetails) {
        var extexelfileName = "timetrack_Day.xls";
        if (!$scope.isadmin){
            extexelfileName = "TimeTrack_Day_" + $scope.UserName + ".xls";
        }
        console.log(userdetails);
		if(userdetails.length > 0){
        //alasql.promise('SELECT * INTO XLS("timetrack.xls",{headers:true}) FROM HTML("#exportable", {headers:true})');
            alasql('SELECT empno as EmployeeNumber, first_name as FirstName,last_name as LastName,country as Country,email_id as email, project_name as ProjectName,date as Date, hours as Hours, description as Description, updated_date as UpdatedDate INTO XLS("' + extexelfileName + '",{headers:true}) FROM ?',[userdetails]);

        }else{
			//console.log(userdetails.length);
			alert("nodata");
		}
   };
   /* $scope.exportData = function () {
        var blob = new Blob([document.getElementById('exportable').innerHTML], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
        });
        saveAs(blob, "Report.xls");
    };*/
	/*$scope.bwdatess = function(dt1,dt2,uid){
		console.log("bwdate"+dt1+dt2+uid);
		TaskService.bwdates(dt1,dt2,uid).then(function(details){
		
        if(details.weeklytrackdetails.message === "Getting Successfully"){
            $scope.weekmsg = "Updated Successfully";
            $scope.showWeekDialog = true;
			 //alert("Updated Successfully");
        }else{
            $scope.weekmsg = "Error while updating in DB. Please try again later";
            $scope.showWeekDialog = true;
        }
		//$window.location.reload();
		
		//-------------
    });
	}*/
    $scope.getBtDates = function(dt1,dt2,userdetails){
        //filterBtDates
        $scope.shouldBeOpen = false;
        $scope.userdetails = TaskService.filterBtDates(dt1,dt2,userdetails);

   /*     var df = parseDate(dt1);
        var dt = parseDate(dt2);
        var result = [];
		 if(dt1 > dt2){
              //alert("To Date should be greater than From date"); 
				$scope.alertDialog = true;
            // $scope.datemsg = "To Date should be greater than From date";
			  
        }
        for (var i = 0; i < userdetails.length; i++) {
            var date_bet = parseDate(userdetails[i].date);

            if (date_bet >= df && dt >= date_bet) {
                result.push(userdetails[i]);
            }
			
        }
        $scope.userdetails = result;*/

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

   
    $scope.today = function() {
        $scope.dt2 = moment(new Date()).format('YYYY-MM-DD');
        $scope.dt1 = moment($scope.dt2).subtract(1,'months').format('YYYY-MM-DD');
        /*var previousMonth = new Date($scope.dt2);
        $scope.dt1=previousMonth.setMonth($scope.dt2.getMonth()-1);*/
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
    $scope.downloadData = function(){
        $scope.selecteduser = $("#username option:selected").html();
        lastDayOfLastWeek = moment($scope.admintodate).endOf('week').toDate();
        startDayOfFirstWeek = moment($scope.adminfromdate).startOf('week').toDate();
            TaskService.getMonthlyTrackInfo(moment(startDayOfFirstWeek).format("YYYY-MM-DD"), moment(lastDayOfLastWeek).format("YYYY-MM-DD"), $scope.wuname).then(function (details) {

                console.log(lastDayOfLastWeek);
                console.log(startDayOfFirstWeek);
                console.log(details.weektrackdetails.weekdetailsarray);
                 var extexelfileName="TimeTrack_Week.xls";
                if ($scope.wuname =="alluser"){
                    extexelfileName ="TimeTrack_Week_AllUsers.xls";
                }else if ($scope.wuname =="indianuser"){
                    extexelfileName ="TimeTrack_Week_IndiaUsers.xls";
                }else if ($scope.wuname =="ususer"){
                    extexelfileName ="TimeTrack_Week_USAUsers.xls";
                } else {
                    extexelfileName = "TimeTrack_Week_" + $scope.selecteduser+ ".xls";
                }
            
                
                alasql('SELECT empno as EmployeeNumber, first_name as FirstName, last_name as LastName, email_id as email, country as Country, project_name as ProjectName, date as Date,hours as Hours,updated_date as UpdatedDate INTO XLS("' + extexelfileName + '",{headers:true}) FROM ?', [details.weektrackdetails.weekdetailsarray]);
                //alasql('SELECT empno as EmployeeNumber, user_id as UserId, u_name as UserName, email_id as EmailId, country as Country, project_name as ProjectName, date as Date,hours as Hours,updated_date as UpdatedDate INTO XLS("weektrack_Week_.xls",{headers:true}) FROM ?', [details.weektrackdetails.weekdetailsarray]);

            });
        //alasql('SELECT uid,uname,email_id,date,hours INTO XLS("weektrack.xls",{headers:true}) FROM  ?',[downloadDetailsArray]);

    };
}]);

app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$setViewValue(transformedInput);
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
app.directive('focusMe', function($timeout) {
    return {
        scope: { trigger: '@focusMe' },
        link: function(scope, element) {
            scope.$watch('trigger', function(value) {
                if(value === "true") {
                    // console.log('trigger',value);
                    $timeout(function() {
                        element[0].focus();
                    });
                }
            });
        }
    };
});