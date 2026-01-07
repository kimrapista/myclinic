
'use strict';

// DOCTOR

app.controller('Services',function($scope, $http, $timeout, $filter, $rootScope, global){ 


	$rootScope.$emit("Title_Global", { TITLE : 'SERVICES', BACKURL:'' });


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		searchUrl : global.baseUrl + 'settings/services/index',
		newUrl : '#!/settings/services/new',
		editUrl :  '#!/settings/services/',
		isDisabled : global.USER.LEVEL == 'BRANCH ADMINISTRATOR' ? false : true
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
