app.controller('Navigation', function ($scope, $log, $mdSidenav, $mdDialog, $rootScope, MeServices, global) {

	global.baseUrl = startUp.baseUrl;

    MeServices.Form().then(function(data){ });

    $scope.Me = function(){
        return MeServices.Data();
    }

    $scope.Page = global.page;    


    $scope.toggleRight = function (sidepanel) {
        $mdSidenav(sidepanel)
        .toggle()
        .then(function () {
            $log.debug("toggle right is done");
        });
    }


    $scope.Toggle_Drawer = function () {
        localStorage.setItem('drawer', global.page.drawer);
    }


    $scope.Change_Username = function(){

        $scope.toggleRight('user-sb');

        $mdDialog.show({
            templateUrl: 'views/admin/modal_change_username.html',
            clickOutsideToClose: false,
            escapeToClose: false,
            fullscreen: false,
            controller: function($scope,$mdDialog, $http, MeServices, global){

                $scope.isLoaded = false;

                $scope.FORM = {
                    TOKEN: MeServices.Data().TOKEN,
                    USERNAME: MeServices.Data().USERNAME,
                    NEWUSERNAME: '',
                    PASSWORD:'',
                    isSubmit: false
                }
  
                $scope.Submit_Username = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'account/submit-username', $scope.FORM , global.ajaxConfig ).then(function (response) {

                        $scope.FORM.isSubmit = false;

                        if( $.trim(response.data) == 'RELOGIN'){
                            global.Alert('Submit failed. Please try again');
                        }
                        else{

                            if( $.trim(response.data.err) != '' ){
                                global.Alert(response.data.err);
                            }
                            else{
                                MeServices.Data().ID = 0;
                                MeServices.Form().then(function(data){
                                    global.Toast('Username changed');
                                    $mdDialog.hide();
                                });
                            }
                        }
                    }, 
                    function (err) { 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });
                }
                
                $scope.Close = function () {
                    $mdDialog.cancel();
                };

                $scope.Init = function(){
                    $scope.isLoaded = true;
                }
            }
        })
        .then(function(answer) {
            $scope.toggleRight('user-sb');
        }, function() {
            $scope.toggleRight('user-sb');
        });
    }


    $scope.Change_Password = function(){

        $scope.toggleRight('user-sb');

        $mdDialog.show({
            templateUrl: 'views/admin/modal_change_password.html',
            clickOutsideToClose: false,
            escapeToClose: false,
            fullscreen: false,
            controller: function($scope,$mdDialog, $http, MeServices, global){

                $scope.isLoaded = false;

                $scope.FORM = {
                    TOKEN: MeServices.Data().TOKEN,
                    CPASSWORD: '',
                    NPASSWORD: '',
                    RPASSWORD:'',
                    isSubmit: false
                }
  
                $scope.Submit_Password = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'account/submit-password', $scope.FORM , global.ajaxConfig ).then(function (response) {

                        $scope.FORM.isSubmit = false;

                        if( $.trim(response.data) == 'RELOGIN'){
                            global.Alert('Submit failed. Please try again');
                        }
                        else{

                            if( $.trim(response.data.err) != '' ){
                                global.Alert(response.data.err);
                            }
                            else{

                                MeServices.Form().then(function(data){
                                    global.Toast('Password changed');
                                    $mdDialog.hide();
                                });
                            }
                        }
                    }, 
                    function (err) { 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });
                }
                
                $scope.Close = function () {
                    $mdDialog.cancel();
                };

                $scope.Init = function(){
                    $scope.isLoaded = true;
                }
            }
        })
        .then(function(answer) {
            $scope.toggleRight('user-sb');
        }, function() {
            $scope.toggleRight('user-sb');
        });
    }

    
    $scope.My_Account = function(){

        $scope.toggleRight('user-sb');

        $mdDialog.show({
            templateUrl: 'views/admin/modal_my_account.html',
            clickOutsideToClose: false,
            escapeToClose: false,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, MeServices, SubClinicServices, SpecialistServices, global){

                $scope.isLoaded = false;

                $scope.FORM = angular.copy(MeServices.Data());
  
                $scope.Submit_Account = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'account/submit-account', $scope.FORM , global.ajaxConfig ).then(function (response) {

                        $scope.FORM.isSubmit = false;

                        if( $.trim(response.data) == 'RELOGIN'){
                            global.Alert('Submit failed. Please try again');
                        }
                        else{

                            if( $.trim(response.data.err) != '' ){
                                global.Alert(response.data.err);
                            }
                            else{

                                MeServices.Form().then(function(data){
                                    global.Toast('SAVED.');
                                    $mdDialog.hide();
                                });
                            }
                        }
                    }, 
                    function (err) { 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });
                }
                
                $scope.Close = function () {
                    $mdDialog.cancel();
                };

                $scope.SubClinics = function(){
                    return SubClinicServices.Data();
                }

                $scope.Specialist = function(){
                    return SpecialistServices.Data();
                }

                $scope.Init = function(){
                    SubClinicServices.Reload().then(function(data){});
                    SpecialistServices.Reload().then(function(data){});
                    $scope.isLoaded = true;
                }
            }
        })
        .then(function(answer) {
            $scope.toggleRight('user-sb');
        }, function() {
            $scope.toggleRight('user-sb');
        });
    }


    angular.element(document.querySelector('.mdl-layout__content')).bind('scroll', function(){
        $rootScope.$emit('CONTENT_SCROLL',{
            scrollTop: this.scrollTop,
            scrollTopMax: this.scrollTopMax
        });
    });


});
 




// AUTO CLOSE LEFT PANEL AFTER CLICKING LINK
$(function(){

    $('.mdl-layout__drawer *[href]').on('click', function(event) {
        $('.mdl-layout__drawer').removeClass('is-visible');
        $('.mdl-layout__obfuscator').removeClass('is-visible');
    });

});


$('.mdl-layout__content').scroll(function (e) {

    if ( $(this).scrollTop() > 200) {

        if( !$('.profile-sticky').hasClass('ontop') )
            $('.profile-sticky').hide().addClass('ontop').fadeIn();

        if( !$('.search-form').hasClass('ontop') )
            $('.search-form').hide().addClass('ontop').fadeIn();
    } 
    else if ( $(this).scrollTop() <= 0) {

        if( $('.profile-sticky').hasClass('ontop') )
            $('.profile-sticky').hide().removeClass('ontop').fadeIn();
        
        if( $('.search-form').hasClass('ontop') )
            $('.search-form').hide().removeClass('ontop').fadeIn();
    }

});