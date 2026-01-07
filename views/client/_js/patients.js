'use strict';


app.controller('Patients', function($scope, $timeout, $rootScope, PatientsServices,  global){ 


    global.page.title = 'Patients';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;

    $scope.search = {
        name: '',
        limit : 10,
        from : 0,
        isSubmit: false,
        viewMore: true
    }


    $scope.Data = function(){
        return PatientsServices.Data();
    }


    $scope.Submit_Search = function(){

        $scope.search.isSubmit = true;
        $scope.search.viewMore = false;
        $scope.search.from = 0;

        PatientsServices.Load({SEARCH: $scope.search.name, FROM: $scope.search.from, TO: $scope.search.limit}).then(function(data){

            if( PatientsServices.Data().length >= $scope.search.limit )
                $scope.search.viewMore = true;

            $scope.search.isSubmit = false;
                
        });
    }


    $scope.Submit_Search_More = function(){

        if( $scope.search.isSubmit )
            return false;
        
        $scope.search.isSubmit = true;
        $scope.search.from += $scope.search.limit;

        PatientsServices.Load({SEARCH: $scope.search.name, FROM: $scope.search.from, TO: $scope.search.limit}).then(function(data){
            
            if( ! data ){
                $scope.search.viewMore = false;

                if( $scope.search.from > 0)
                    $scope.search.from -= $scope.search.limit;
            }
            $timeout(function(){
                $scope.search.isSubmit = false;
            },200);
            
        });
    }

    $scope.Class_Animate = function(key){

        if( ! $scope.search.isSubmit && $scope.search.viewMore ){
            if( key > $scope.search.limit && key > $scope.search.from){
                return 'animated fadeInLeft';
            }
            else{
                return '';
            }
        }
        else{
            return '';
        }
    }


    $scope.Init = function(){

        $scope.Submit_Search();
        $scope.isLoaded = true;
    }

});