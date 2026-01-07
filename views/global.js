'use strict';



app.factory('global', function( $mdToast, $log, $q, $mdDialog){
    
    var service = {};

    service.ajaxConfig = {'headers':{'X-Requested-With' :'XMLHttpRequest'}};
    service.baseUrl = '';
    service.TOKEN = '';

    
    service.page = {
        title: '',
        pageBackUrl: '',
        userWarning: false,
        signoutUrl: service.baseUrl + 'signout',
        drawer: localStorage.getItem('drawer') ? (localStorage.getItem('drawer') == 'true' ? true:false ) : true        
    };
    
    service.layout = {
        value: localStorage.getItem('mrLayout') ? localStorage.getItem('mrLayout') :  'DEFAULT',
        isMRDefault: true,
        isMRVLayout: false,
        isMRGLayout: false,
    };

    service.Change_Layout = function (){

        if( service.layout.value == 'DEFAULT'){
            service.layout.isMRDefault= true;
            service.layout.isMRVLayout= false;
            service.layout.isMRGLayout= false;
        }
        else if( service.layout.value == 'VLAYOUT'){
            service.layout.isMRDefault= false;
            service.layout.isMRVLayout= true;
            service.layout.isMRGLayout= false;
        }
        else {
            service.layout.isMRDefault= false;
            service.layout.isMRVLayout= false;
            service.layout.isMRGLayout= true;
        }

        localStorage.setItem('mrLayout', service.layout.value);
    }

    service.Date = function(passDate){

        if( passDate == undefined || passDate == null || passDate == '' ){
            passDate = new Date();
            passDate = passDate.getFullYear()+'-'+passDate.getMonth()+'-'+passDate.getDate()+' '+passDate.getHours()+':'+passDate.getMinutes()+':'+passDate.getSeconds();
        }

        var dateTime = passDate.split(' ');
        var date = dateTime[0].split('-');

        var time;

        if( dateTime[1] == undefined ){
            time = '00:00:00';
            time = time.split(':');
        }
        else{
            time = dateTime[1].split(':');    
        }

        passDate = new Date( date[0], date[1]-1, date[2], time[0], time[1], time[2], 0);

        return passDate;
    }

    service.FormatDateTime = function(dateVal, defaultVal){

        if( dateVal == null || dateVal == '' ){
            return defaultVal;
        }
        else{

            var tDate =new Date(dateVal);
    
            if( tDate == 'Invalid Date' ){
                return defaultVal;
            }
            else if( angular.isDate(tDate) ){
                return tDate;
            }
            else{
                return defaultVal;
            }
        }
    }

    service.Date_String_Format = function(date){

        if (date == null) {
        return null;
        }

        const pad = (n) => n.toString().padStart(2, '0');

        const formatted =
            date.getFullYear() + '-' +
            pad(date.getMonth() + 1) + '-' +
            pad(date.getDate()) + 'T' + // Use 'T' instead of space
            pad(date.getHours()) + ':' +
            pad(date.getMinutes()) + ':' +
            pad(date.getSeconds());

        return new Date(formatted); // Returns ISO-compatible string
    }

       
    service.Toast = function(msg , redirect){

        $mdToast.show(
            $mdToast.simple()
            .textContent(msg)
            .position('top right')
            .hideDelay(3000))
        .then(function() {
            $log.log('Toast dismissed.');

            if( redirect != undefined ){
                location.replace(redirect);
            }
            
        }).catch(function() {
            $log.log('Toast failed or was forced to close early by another toast.');
        });
    }


    
    

    service.Alert = function(msg, title , redirect) {

        if( title == undefined || title == null){ title = 'System'};
        
        $mdDialog.show(
            $mdDialog.alert()
            .clickOutsideToClose(true)
            .title(title)
            .textContent(msg)
            .multiple(true)
            .ariaLabel('Alert Dialog Demo')
            .ok('OK')
        )
        .then(function(answer) {
            if( redirect != undefined ){
                location.replace(redirect);
            }
        });
    };

    return service;
});




app.factory('Preview', function($mdDialog, $http){


    function Format(detail){

		detail.MRID = parseInt(detail.MRID);

		if ( typeof detail.ANNUAL != 'boolean' )
			detail.ANNUAL = detail.ANNUAL == 'Y' ? true : false;

		if ( typeof detail.EXTERNAL_EYE != 'boolean' )
			detail.EXTERNAL_EYE = detail.EXTERNAL_EYE == 'Y' ? true : false;

		if ( typeof detail.GLAUCOMA != 'boolean' )
			detail.GLAUCOMA = detail.GLAUCOMA == 'Y' ? true : false;

		if ( typeof detail.HEADACHE != 'boolean' )
			detail.HEADACHE = detail.HEADACHE == 'Y' ? true : false;

		if ( typeof detail.PITY != 'boolean' )
			detail.PITY = detail.PITY == 'Y' ? true : false;

		if ( typeof detail.RETINA != 'boolean' )
			detail.RETINA = detail.RETINA == 'Y' ? true : false;

		if ( typeof detail.CATARACT != 'boolean' )
			detail.CATARACT = detail.CATARACT == 'Y' ? true : false;

		if ( typeof detail.FB != 'boolean' )
			detail.FB = detail.FB == 'Y' ? true : false;

		if ( typeof detail.LASER != 'boolean' )
			detail.LASER = detail.LASER == 'Y' ? true : false;

		if ( typeof detail.REFRACTION != 'boolean' )
			detail.REFRACTION = detail.REFRACTION == 'Y' ? true : false;

		detail.isSubmit = false;

		return detail;
	}

    return {
        Medical_Record: function(MRID){

            $mdDialog.show({
                templateUrl: 'views/client/modal_preview_medical_record.html',
                clickOutsideToClose: true,
                fullscreen: true,
                escapeToClose: false,
                multiple: true,
                locals:{
                    MRID : MRID
                },
                controller: function($scope, $http, $mdDialog, $location, $timeout, MRID, global, MeServices){

                    $scope.Me = function(){
                        return MeServices.Data();
                    }
    
                    $scope.isLoaded = false;
                    $scope.MR = [];
    
                    $http.post( global.baseUrl +'patients/medical-record-preview',{MRID: MRID}, global.ajaxConfig)
                    .then(function(response){
    
                        $scope.MR = response.data;
    
                        $scope.MR.CHECKUPDATE =  global.Date($scope.MR.CHECKUPDATE);
    
                        if( $scope.MR.LMP != null )
                            $scope.MR.LMP = global.Date($scope.MR.LMP);
    
                        $scope.isLoaded = true;

                        if( response.data.error ){
                            global.Alert(response.data.error);
                            $location.url('/patient/'+ PATIENTID+ '/record');
                            return false;
                        }

                        $scope.MR.GROUP_LAB_MONITORING = [];

                        $scope.MR.LABMONITORING.forEach((v)=>{
                            v.INDENT = parseInt(v.INDENT);

                            if( $scope.MR.GROUP_LAB_MONITORING.filter((v1) => v1.GROUPNAME === v.GROUPNAME).length == 0 ){
                                $scope.MR.GROUP_LAB_MONITORING.push({
                                    GROUPNAME: v.GROUPNAME,
                                    DATA: [v]
                                })
                            }
                            else{
                                $scope.MR.GROUP_LAB_MONITORING.forEach((v1)=>{
                                    if( v1.GROUPNAME == v.GROUPNAME){
                                        v1.DATA.push(v);
                                    }
                                })
                            }
                        })

                        if( $scope.MR.PREVIOUS ){
							$scope.MR.PREVIOUS.CHECKUPDATE =  global.Date($scope.MR.PREVIOUS.CHECKUPDATE);
							$scope.MR.PREVIOUS.WEIGHT 	= $scope.MR.PREVIOUS.WEIGHT ? parseFloat($scope.MR.PREVIOUS.WEIGHT) : null;
							$scope.MR.PREVIOUS.HEIGHT 	= $scope.MR.PREVIOUS.HEIGHT ? parseFloat($scope.MR.PREVIOUS.HEIGHT) : null;							
							$scope.MR.PREVIOUS.BMI 		= $scope.MR.PREVIOUS.BMI ? parseFloat($scope.MR.PREVIOUS.BMI) : 0;	
						}

                        return Format(response.data);
                    }, 
                    function(err){ 
                        global.Alert( err.statusText, 'Error ' + err.status);
                        return false;
                    });

                    $scope.Lab_View = function(LAB) {

                        $mdDialog.show({
                            templateUrl: 'views/client/modal_view_files.html',
                            fullscreen: true,
                            multiple: true,
                            clickOutsideToClose: true,
                            locals:{
                                LAB:{
                                    FILENAME: LAB.FILENAME,
                                    IMAGEPATH: LAB.IMAGEPATH
                                }
                            },
                            controller: function($scope,$mdDialog, LAB){
                
                                $scope.FILE = LAB;
                
                                $scope.Close = function(){
                                    $mdDialog.cancel();
                                }
                            }
                        })
                        .then(function(answer) {
                
                        }, function() {
                
                        });
                    };

                    $scope.Redirect_Edit = function(RECORD){

                        $location.url('/patient/'+ RECORD.PATIENTID +'/'+ RECORD.ID +'/medical-record');
                        // for self modal close
                        $mdDialog.cancel();
                        // other modal to close
                        $timeout(function(){ $mdDialog.cancel(); }, 100);
                    }
    
                    $scope.Close = function () {
                        $mdDialog.cancel();
                    };
                }
            })
            .then(function(answer) {
    
            }, function() {
    
            });
        },
        Laboratory: function(MRID){

            $mdDialog.show({
                templateUrl: 'views/client/modal_preview_laboratory.html',
                clickOutsideToClose: true,
                escapeToClose: false,   
                hasBackdrop : false,    
                multiple: true,
                locals:{
                    MRID : MRID
                },
                controller: function($scope,$mdDialog, $http, MRID, global){
    
                    $scope.isLoaded = false;
                    $scope.PATIENT = [];
                    $scope.Laboratories = [];
    
                    $http.post( global.baseUrl +'patients/medical-record-preview-laboratory',{MRID: MRID}, global.ajaxConfig)
                    .then(function(response){
                     
                        $scope.PATIENT = response.data.PATIENT;
                        $scope.Laboratories = response.data.LABORATORY;


                        $scope.GROUP_LAB_MONITORING = [];

                        response.data.LAB_MONITORING.forEach((v)=>{
                            v.INDENT = parseInt(v.INDENT);
                            
                            if( $scope.GROUP_LAB_MONITORING.filter((v1) => v1.GROUPNAME === v.GROUPNAME).length == 0 ){
                                $scope.GROUP_LAB_MONITORING.push({
                                    GROUPNAME: v.GROUPNAME,
                                    DATA: [v]
                                })
                            }
                            else{
                                $scope.GROUP_LAB_MONITORING.forEach((v1)=>{
                                    if( v1.GROUPNAME == v.GROUPNAME){
                                        v1.DATA.push(v);
                                    }
                                })
                            }
                        })

                        $scope.isLoaded = true;
                    },
                    function(err){ 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });


                    $scope.Lab_View = function(LAB) {

                        $mdDialog.show({
                            templateUrl: 'views/client/modal_view_files.html',
                            fullscreen: true,
                            multiple: true,
                            clickOutsideToClose: true,
                            locals:{
                                LAB:{
                                    FILENAME: LAB.FILENAME,
                                    IMAGEPATH: LAB.IMAGEPATH
                                }
                            },
                            controller: function($scope,$mdDialog, LAB){
                
                                $scope.FILE = LAB;
                
                                $scope.Close = function(){
                                    $mdDialog.cancel();
                                }
                            }
                        })
                        .then(function(answer) {
                
                        }, function() {
                
                        });
                    };
    
                    $scope.Close = function () {
                        $mdDialog.cancel();
                    };
                }
            })
            .then(function(answer) {
    
            }, function() {
    
            });
        }, 
        Report: function(url, title, OPTION){
            
            if( title == undefined || title == '' ) title = 'Report';
            if(OPTION == undefined) OPTION = {};
            
            
            $mdDialog.show({
                templateUrl: 'views/modal_pdf.html',
                locals:{
                    url: url,
                    title: title,
                    OPTION: OPTION
                },  
                clickOutsideToClose: true,
                fullscreen: true,
                multiple: true,
                controller: function($scope,$mdDialog, $http, global, PDF, url, title, OPTION){
    
                    $scope.PDF = function(){ return PDF; } 
                    $scope.deleteUrlReport = '';

                    PDF.Title = title;
            
                    $http.post( url, OPTION, global.ajaxConfig) .then( function(response) {
                        
                        var pdfUrl = response.data +'?time' + (new Date).getTime();
                        $scope.deleteUrlReport = response.data;

                        PDF.Init('pdf_wrapper', pdfUrl ).then(function(data){ });
                    }, 
                    function(err){ 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });   

                    
                    $scope.$on('$destroy', function() {
                        $scope.deleteUrlReport = $scope.deleteUrlReport.replace('temp_files_pdf', 'temp-delete')                        
                        $http.get( $scope.deleteUrlReport  );
                    });
    
                    $scope.close = function () {
                        $mdDialog.cancel();
                    };
                }
            })
            .then(function(answer) {
                    
            }, function() {

            });
        },
        Report1: function(url, title){

            $mdDialog.show({
                templateUrl: 'views/modal_preview.html',
                locals:{
                    url: url,
                    title: title
                },  
                clickOutsideToClose: true,
                fullscreen: true,
                multiple: true,
                controller: function($scope, $mdDialog, $http, global, url, title){
                    
                    $scope.Title = title;
                    $scope.reportUrl = url + '?imgTime=' + (new Date()).getTime();

                    $scope.close = function () {
                        $mdDialog.cancel();
                    };
                }
            })
            .then(function(answer) {
    
            }, function() {
    
            });
        }
    }
});