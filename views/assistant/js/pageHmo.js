
'use strict';
//ASSISTANT

app.controller('Hmo', function($scope, $http, $timeout, $filter, $rootScope, global){ 


	$rootScope.$emit("Title_Global", { TITLE : 'HMO', BACKURL:'' });

	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		searchUrl : global.baseUrl + 'settings/hmo/index',
		newUrl : '#!/settings/hmo/new',
		editUrl :  '#!/settings/hmo/'
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
