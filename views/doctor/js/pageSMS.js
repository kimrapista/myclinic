
'use strict';

// DOCTOR

app.controller('SMS',function($scope, $http, $timeout, $filter, $rootScope, global){ 


	$rootScope.$emit("Title_Global", { TITLE : 'SMS', BACKURL:'' });


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		searchUrl : global.baseUrl + 'sms/index',
		newUrl : '#!/sms/new',
		editUrl :  '#!/sms/'
	}


	$scope.Load_List = function() {

		$scope.opt.isSearch = true;

		$http.get($scope.opt.searchUrl, global.ajaxConfig) .then( function(response) {

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.LIST = [];
				$scope.LIST = response.data;

				angular.forEach( $scope.LIST, function(v,k){
					v.SENDDATE = global.Date(v.SENDDATE);
					v.CREATEDTIME = global.Date(v.CREATEDTIME);
				})
			}

			$scope.opt.isLoaded = true;
			$scope.opt.isSearch = false;

		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isSearch = false;
			$scope.opt.isLoaded = true;
		});

	}


	$scope.Load_List();


	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Load_List();
    });

    $scope.$on('$destroy', function() {
        dList();
    });
	
});
