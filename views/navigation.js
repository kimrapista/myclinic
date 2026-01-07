'use strict';

app.controller('Navigation', function ($scope, $log, $mdSidenav, $mdDialog, $rootScope, MeServices, global, NotifyServices, Preview, ChangelogServices) {

    
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
            templateUrl: 'views/modal_change_username.html',
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
            templateUrl: 'views/modal_change_password.html',
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


    $scope.Change_Profile_Picture = function(){

        $scope.toggleRight('user-sb');

        $mdDialog.show({
            templateUrl: 'views/modal_change_profile.html',
            clickOutsideToClose: true,
            escapeToClose: true,
            fullscreen: false,
            controller: function($scope,$mdDialog, $timeout, MeServices, global){

                $scope.Me = function(){
                    return MeServices.Data();
                }

                $scope.isLoaded = false;
                $scope.isUploadForm = false;
                $scope.isDefault = true;
                $scope.isCrop = false;


                $scope.Upload_Form = function(){

                    $scope.isDefault = false;
                    $scope.isUploadForm = true;

                    $('.dropzone-profile').append('<div id="dzlabel" class="text-center" ><i>Click to add or drop photos</i></div>');
                }

                $scope.dzMethods = {};

                $scope.dzOptions = {
                    url : global.baseUrl + 'account/submit-upload',
                    paramName : 'file',
                    maxFilesize : 5,
                    parallelUploads : 1,
                    acceptedFiles : 'image/jpeg,image/jpg',
                    dictDefaultMessage : 'Click to add or drop photos'
                }

                $scope.dzCallbacks = {
                    'addedfile' : function(file){

                        MeServices.Data().isUploading = true;

                        if( file.status == 'error' ){
                            global.Alert('ERROR');
                        }

                        console.log('addedfile');
                        $('#dzlabel').remove();
                    },
                    'success' : function(file, xhr){

                        try {

                            if( JSON.parse(xhr) ) {
                                var newFile = JSON.parse(xhr);
                                MeServices.Data().AVATAR = global.baseUrl  + newFile.AVATAR;
                                MeServices.Form();
                            }
                        }
                        catch(e){ 
                            console.log('invalid json',e); 
                        }

                        $scope.dzMethods.removeFile(file); 

                        MeServices.Data().isUploading = false;
                        $scope.isDefault = true;
                        $scope.isUploadForm = false;
                        
                        console.log('success');
                        $('#dzlabel').remove();

                        $scope.Crop_Form();
                    },
                    'error' : function(file, xhr){
                        
                        global.Alert(xhr);
                        MeServices.Data().isUploading = false;
                        $scope.dzMethods.removeFile(file); 
                        $scope.isDefault = false;

                        console.log('error');
                        //$('#dzlabel').remove();
                    }
                }


                $scope.Cancel_Upload = function(){

                    $scope.isUploadForm = false;
                    $scope.isDefault = true;
                    $('#dzlabel').remove();
                }

                var profileCrop;

                $scope.Crop_Form = function(){

                    $scope.isUploadForm = false;
                    $scope.isDefault = false;
                    $scope.isCrop = true;

                    profileCrop = $('#crop-profile').croppie({
                        enableExif: true,
                        viewport: {
                            width: 160,
                            height: 160
                            // type: 'circle'
                        },
                        boundary: {
                            width: 300,
                            height: 300
                        },
                        url: MeServices.Data().AVATAR,
                        enableResize: false,
                        enableOrientation: true,
                        showZoomer: false                        
                        //circle: true                   
                    });
                    
                }

                $scope.Crop_Result = function(){
                    
                    $scope.isCrop = false;
                    $scope.isDefault = true;

                    profileCrop.croppie('result', {
                        type: 'base64',
                        size: 'original',
                        format: 'jpeg',
                        quality: 0.5,
                    }).then(function (resp) {

                        MeServices.New_Crop(resp).then(function(data){
                            MeServices.Data().AVATAR = resp;
                        });
                                                                        
                        $('#crop-profile').croppie('destroy');
                    });
                }

                $scope.Crop_Rotate = function(){

                    profileCrop.croppie('rotate', -90);
                }


                $scope.Cancel_Crop = function(){
                    $scope.isCrop = false;
                    $scope.isDefault = true;
                    $('#crop-profile').croppie('destroy');
                }

                
                $scope.Init = function(){
                    $scope.isLoaded = true;
                }

                $scope.Close = function () {
                    $mdDialog.cancel();
                };
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
            templateUrl: 'views/modal_my_account.html',
            clickOutsideToClose: false,
            escapeToClose: false,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, $q, MeServices, SubClinicServices, SpecialistServices, global){

                $scope.isLoaded = false;

                $scope.FORM = angular.copy(MeServices.Data());
                $scope.signaturePad = [];
                
                $scope.colorSel = null

                $scope.colors = [
                    {name:"aliceblue", code: "#f0f8ff"},
                    {name:"antiquewhite", code: "#faebd7"},
                    {name:    "aqua", code: "#00ffff"},
                    {name:    "aquamarine", code: "#7fffd4"},
                    {name:    "azure", code: "#f0ffff"},
                    {name:    "beige", code: "#f5f5dc"},
                    {name:    "bisque", code: "#ffe4c4"},
                    {name:    "black", code: "#000000"},
                    {name:    "blanchedalmond", code: "#ffebcd"},
                    {name:    "blue", code: "#0000ff"},
                    {name:    "blueviolet", code: "#8a2be2"},
                    {name:    "brown", code: "#a52a2a"},
                    {name:    "burlywood", code: "#deb887"},
                    {name:    "cadetblue", code: "#5f9ea0"},
                    {name:    "chartreuse", code: "#7fff00"},
                    {name:    "chocolate", code: "#d2691e"},
                    {name:    "coral", code: "#ff7f50"},
                    {name:    "cornflowerblue", code: "#6495ed"},
                    {name:    "cornsilk", code: "#fff8dc"},
                    {name:    "crimson", code: "#dc143c"},
                    {name:    "cyan", code: "#00ffff"},
                    {name:    "darkblue", code: "#00008b"},
                    {name:    "darkcyan", code: "#008b8b"},
                    {name:    "darkgoldenrod", code: "#b8860b"},
                    {name:    "darkgray", code: "#a9a9a9"},
                    {name:    "darkgreen", code: "#006400"},
                    {name:    "darkgrey", code: "#a9a9a9"},
                    {name:    "darkkhaki", code: "#bdb76b"},
                    {name:   "darkmagenta", code: "#8b008b"},
                    {name:   "darkolivegreen", code: "#556b2f"},
                    {name:   "darkorange", code: "#ff8c00"},
                    {name:   "darkorchid", code: "#9932cc"},
                    {name:   "darkred", code: "#8b0000"},
                    {name:   "darksalmon", code: "#e9967a"},
                    {name:   "darkseagreen", code: "#8fbc8f"},
                    {name:   "darkslateblue", code: "#483d8b"},
                    {name:   "darkslategray", code: "#2f4f4f"},
                    {name:   "darkslategrey", code: "#2f4f4f"},
                    {name:   "darkturquoise", code: "#00ced1"},
                    {name:   "darkviolet", code: "#9400d3"},
                    {name:   "deeppink", code: "#ff1493"},
                    {name:   "deepskyblue", code: "#00bfff"},
                    {name:   "dimgray", code: "#696969"},
                    {name:   "dimgrey", code: "#696969"},
                    {name:   "dodgerblue", code: "#1e90ff"},
                    {name:   "firebrick", code: "#b22222"},
                    {name:   "floralwhite", code: "#fffaf0"},
                    {name:   "forestgreen", code: "#228b22"},
                    {name:   "fuchsia", code: "#ff00ff"},
                    {name:   "gainsboro", code: "#dcdcdc"},
                    {name:   "ghostwhite", code: "#f8f8ff"},
                    {name:   "goldenrod", code: "#daa520"},
                    {name:   "gold", code: "#ffd700"},
                    {name:   "gray", code: "#808080"},
                    {name:   "green", code: "#008000"},
                    {name:   "greenyellow", code: "#adff2f"},
                    {name:   "grey", code: "#808080"},
                    {name:   "honeydew", code: "#f0fff0"},
                    {name:   "hotpink", code: "#ff69b4"},
                    {name:   "indianred", code: "#cd5c5c"},
                    {name:   "indigo", code: "#4b0082"},
                    {name:   "ivory", code: "#fffff0"},
                    {name:   "khaki", code: "#f0e68c"},
                    {name:   "lavenderblush", code: "#fff0f5"},
                    {name:   "lavender", code: "#e6e6fa"},
                    {name:   "lawngreen", code: "#7cfc00"},
                    {name:   "lemonchiffon", code: "#fffacd"},
                    {name:   "lightblue", code: "#add8e6"},
                    {name:   "lightcoral", code: "#f08080"},
                    {name:   "lightcyan", code: "#e0ffff"},
                    {name:   "lightgoldenrodyellow", code: "#fafad2"},
                    {name:   "lightgray", code: "#d3d3d3"},
                    {name:   "lightgreen", code: "#90ee90"},
                    {name:   "lightgrey", code: "#d3d3d3"},
                    {name:   "lightpink", code: "#ffb6c1"},
                    {name:   "lightsalmon", code: "#ffa07a"},
                    {name:   "lightseagreen", code: "#20b2aa"},
                    {name:   "lightskyblue", code: "#87cefa"},
                    {name:   "lightslategray", code: "#778899"},
                    {name:   "lightslategrey", code: "#778899"},
                    {name:   "lightsteelblue", code: "#b0c4de"},
                    {name:   "lightyellow", code: "#ffffe0"},
                    {name:   "lime", code: "#00ff00"},
                    {name:   "limegreen", code: "#32cd32"},
                    {name:   "linen", code: "#faf0e6"},
                    {name:   "magenta", code: "#ff00ff"},
                    {name:   "maroon", code: "#800000"},
                    {name:   "mediumaquamarine", code: "#66cdaa"},
                    {name:   "mediumblue", code: "#0000cd"},
                    {name:   "mediumorchid", code: "#ba55d3"},
                    {name:   "mediumpurple", code: "#9370db"},
                    {name:   "mediumseagreen", code: "#3cb371"},
                    {name:   "mediumslateblue", code: "#7b68ee"},
                    {name:   "mediumspringgreen", code: "#00fa9a"},
                    {name:   "mediumturquoise", code: "#48d1cc"},
                    {name:   "mediumvioletred", code: "#c71585"},
                    {name:   "midnightblue", code: "#191970"},
                    {name:   "mintcream", code: "#f5fffa"},
                    {name:   "mistyrose", code: "#ffe4e1"},
                    {name:   "moccasin", code: "#ffe4b5"},
                    {name:   "navajowhite", code: "#ffdead"},
                    {name:   "navy", code: "#000080"},
                    {name:   "oldlace", code: "#fdf5e6"},
                    {name:   "olive", code: "#808000"},
                    {name:   "olivedrab", code: "#6b8e23"},
                    {name:   "orange", code: "#ffa500"},
                    {name:   "orangered", code: "#ff4500"},
                    {name:   "orchid", code: "#da70d6"},
                    {name:   "palegoldenrod", code: "#eee8aa"},
                    {name:   "palegreen", code: "#98fb98"},
                    {name:   "paleturquoise", code: "#afeeee"},
                    {name:   "palevioletred", code: "#db7093"},
                    {name:   "papayawhip", code: "#ffefd5"},
                    {name:   "peachpuff", code: "#ffdab9"},
                    {name:   "peru", code: "#cd853f"},
                    {name:   "pink", code: "#ffc0cb"},
                    {name:   "plum", code: "#dda0dd"},
                    {name:   "powderblue", code: "#b0e0e6"},
                    {name:   "purple", code: "#800080"},
                    {name:   "rebeccapurple", code: "#663399"},
                    {name:   "red", code: "#ff0000"},
                    {name:   "rosybrown", code: "#bc8f8f"},
                    {name:   "royalblue", code: "#4169e1"},
                    {name:   "saddlebrown", code: "#8b4513"},
                    {name:   "salmon", code: "#fa8072"},
                    {name:   "sandybrown", code: "#f4a460"},
                    {name:   "seagreen", code: "#2e8b57"},
                    {name:   "seashell", code: "#fff5ee"},
                    {name:   "sienna", code: "#a0522d"},
                    {name:   "silver", code: "#c0c0c0"},
                    {name:   "skyblue", code: "#87ceeb"},
                    {name:   "slateblue", code: "#6a5acd"},
                    {name:   "slategray", code: "#708090"},
                    {name:   "slategrey", code: "#708090"},
                    {name:   "snow", code: "#fffafa"},
                    {name:   "springgreen", code: "#00ff7f"},
                    {name:   "steelblue", code: "#4682b4"},
                    {name:   "tan", code: "#d2b48c"},
                    {name:   "teal", code: "#008080"},
                    {name:   "thistle", code: "#d8bfd8"},
                    {name:   "tomato", code: "#ff6347"},
                    {name:   "turquoise", code: "#40e0d0"},
                    {name:   "violet", code: "#ee82ee"},
                    {name:   "wheat", code: "#f5deb3"},
                    {name:   "white", code: "#ffffff"},
                    {name:   "whitesmoke", code: "#f5f5f5"},
                    {name:   "yellow", code: "#ffff00"},
                    {name:   "yellowgreen", code: "#9acd32" }
                ];

                $scope.Change_Color = function(){

                    $scope.signaturePad.penColor = $scope.colorSel;
                }
                
                $scope.Change_Sig = function(){
                    $scope.FORM.ESIGNATURE = '';    
                    $scope.signaturePad.clear();   
                    $scope.signaturePad.penColor = $scope.colorSel ? $scope.colorSel : 'rgb(1,1,1)';        
                }

                $scope.Capture_Signature = function(){                    
                    
                    if( $scope.signaturePad.isEmpty() ){
                        $scope.FORM.ESIGNATURE = '';
                    }
                    else{
                        $scope.FORM.ESIGNATURE = $scope.signaturePad.toDataURL();
                    }
                }
  
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
                                    global.Toast('SAVED');
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
                    $q.all([
                        SubClinicServices.Reload(),
                        SpecialistServices.Reload()
                    ]).then(function(result){

                        var canvas = document.getElementById("signature-pad");

                        // backgroundColor: 'rgba(255, 255, 255, 1)',
                        $scope.signaturePad = new SignaturePad(canvas,{
                            penColor: 'rgb(0, 0, 0)',
                            width: 300,
                            height: 180
                        });
                        $scope.signaturePad.penColor = "rgb(66, 133, 244)";

                        $scope.signaturePad.fromDataURL($scope.FORM.ESIGNATURE);

                        $scope.isLoaded = true;
                    });
                }
            }
        })
        .then(function(answer) {
            $scope.toggleRight('user-sb');
        }, function() {
            $scope.toggleRight('user-sb');
        });
    }



    $scope.Online_Profile = function(){

        $scope.toggleRight('user-sb');

        $mdDialog.show({
            templateUrl: 'views/modal_profile_online.html',
            clickOutsideToClose: false,
            escapeToClose: false,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, global, MeServices, SubClinicServices){

                $scope.isLoaded = false;
                
                $scope.Me = function(){
                    return MeServices.Data();
                }

                $scope.Clinics = function(){
                    return SubClinicServices.Data();
                }

                $scope.Day_Time = function(){
                    return SubClinicServices.Day_Time();
                }


                $scope.FORM = {
                    TOKEN: MeServices.Data().TOKEN,
                    ISONLINE: MeServices.Data().ISONLINE,
                    LINK: MeServices.Data().LINK,
                    MOTTO: MeServices.Data().MOTTO,
                    isSubmit: false
                }
  
                $scope.Submit_Profile = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'account/submit-online-profile', $scope.FORM , global.ajaxConfig ).then(function (response) {

                        $scope.FORM.isSubmit = false;

                        if( response.data.err != '' ){
                            global.Alert(response.data.err);
                        }
                        else{

                            MeServices.Data().ISONLINE = $scope.FORM.ISONLINE;
                            MeServices.Data().LINK = $scope.FORM.LINK;
                            MeServices.Data().MOTTO = $scope.FORM.MOTTO;

                            global.Toast('Saved');
                        }
                     }, 
                    function (err) { 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });
                }


                $scope.Submit_Subclinic_Time = function(){

                    $http.post( global.baseUrl +'clinics/subclinic/submit-time', {
                        TOKEN: MeServices.Data().TOKEN,
                        SUBCLINICS: SubClinicServices.Data()
                    } , global.ajaxConfig ).then(function (response) {

                     }, 
                    function (err) { 
                        global.Alert( err.statusText, 'Error ' + err.status);
                    });
                }


                $scope.Set_Map = function () {

                    $mdDialog.show({
                       templateUrl: 'views/client/modal_form_subclinic_map.html',
                       clickOutsideToClose: false,
                       fullscreen: true,
                       escapeToClose: false,
                       multiple: true,
                       controller: function($scope, $mdDialog, SubClinicServices, global, MeServices, Map){
              
               
                            $scope.FORM ={
                                TOKEN: MeServices.Data().TOKEN,
                                ID: null,
                                COORDLONG_0: null,
                                COORDLONG_1: null,
                                COORDSHORT_0: null,
                                COORDSHORT_1: null,
                                SUBCLINIC: []
                            }
              
                            $scope.Submit_Form = function(){
                
                                $scope.FORM.COORDLONG_0 = $scope.FORM.SUBCLINIC.COORDLONG_0;
                                $scope.FORM.COORDLONG_1 = $scope.FORM.SUBCLINIC.COORDLONG_1;
                                $scope.FORM.COORDSHORT_0 = $scope.FORM.SUBCLINIC.COORDSHORT_0;
                                $scope.FORM.COORDSHORT_1 = $scope.FORM.SUBCLINIC.COORDSHORT_1;
                
                                $http.post( global.baseUrl +'clinics/subclinic/submit-set-map', $scope.FORM , global.ajaxConfig ).then(function (response) {
                                
                                    if( $scope.FORM.COORDLONG_0 ){
                                        global.Toast('LOCATION SET');
                                    }
                                    else{
                                        global.Toast('UNSET LOCATION');
                                    }

                                    // landing.services using localstorage(MAP), we need to clear to load new update
                                    if( localStorage.getItem('MAPS') )
                                        localStorage.removeItem('MAPS');
                                    
                                }, 
                                function (err) { 
                                    global.Alert( err.statusText, 'Error ' + err.status);
                                    $scope.close();
                                });
                            }
                         
              
                            $scope.Clinics = function(){
                                return SubClinicServices.Data();
                            }
              
              
                            $scope.Change_Clinic = function(){
                
                                Map.Remove_Layer_Name(Map.Data().setupLocationName);
                                Map.Clear_Coordinate();
                
                                if( $scope.FORM.ID > 0 ){
                
                                angular.forEach( SubClinicServices.Data(), function(v,k){
                                    if( $scope.FORM.ID == v.ID ){
                
                                        $scope.FORM.SUBCLINIC = v;
                
                                        Map.Add_Layer( Map.Data().setupLocationName, [$scope.FORM.SUBCLINIC.COORDLONG_0, $scope.FORM.SUBCLINIC.COORDLONG_1]);
                
                                        if( $scope.FORM.SUBCLINIC.COORDLONG_0 ){
                                            Map.Goto([$scope.FORM.SUBCLINIC.COORDLONG_0, $scope.FORM.SUBCLINIC.COORDLONG_1]);
                                        }                         
                                    }
                                });
                                
                                }
                            }
              
              
                            $scope.Set_Coordinate= function(){
                                
                                if( $scope.FORM.ID  ){
                
                                    if( Map.Data().click_coord_long ){
                    
                                        $scope.FORM.SUBCLINIC.COORDLONG_0 = Map.Data().click_coord_long[0];
                                        $scope.FORM.SUBCLINIC.COORDLONG_1 = Map.Data().click_coord_long[1];
                                        $scope.FORM.SUBCLINIC.COORDSHORT_0 = Map.Data().click_coord_short[0];
                                        $scope.FORM.SUBCLINIC.COORDSHORT_1 = Map.Data().click_coord_short[1];
                                        
                                        $scope.Submit_Form();
                                    }
                                    else{
                                        global.Alert('Please point the location');
                                    }
                                
                                }
                                else{
                                    global.Alert('Please select clinic before you point in the map');
                                }
                            }
              
              
                            $scope.Unset_Coordinate= function(){
                                
                                if( $scope.FORM.ID ){
                
                                    $scope.FORM.SUBCLINIC.COORDLONG_0 = null;
                                    $scope.FORM.SUBCLINIC.COORDLONG_1 = null;
                                    $scope.FORM.SUBCLINIC.COORDSHORT_0 = null;
                                    $scope.FORM.SUBCLINIC.COORDSHORT_1 = null;
                    
                                    Map.Remove_Layer_Name(Map.Data().setupLocationName);
                                    Map.Clear_Coordinate();
                                    $scope.Submit_Form();
                                }
                                else{
                                    global.Alert('Please select clinic before you unset');
                                }
                            }
              
              
                            $scope.Init = function(){
                
                                SubClinicServices.Reload().then(function(data){
                                    Map.Init();
                                    Map.Set_My_Location();
                                    Map.Data().setupLocationName = 'setup_location';
                                    Map.Data().setupLocation = true;
                                });
                            }
              
                            $scope.Close = function () {
                                $mdDialog.cancel();
                            }

                        }           
                    }).then(function(answer) {
                
                    }, function(cancel) {
              
                    });
                }

                
                        
                $scope.Init = function(){

                    SubClinicServices.Reload().then(function(data){
                        $scope.isLoaded = true;
                    });

                }

                $scope.Close = function () {
                    $mdDialog.cancel();
                };
            }
        })
        .then(function(answer) {
            $scope.toggleRight('user-sb');
        }, function() {
            $scope.toggleRight('user-sb');
        });
    }


    $scope.Preview_MR = function(ID){
        Preview.Medical_Record(ID)
    }

    $scope.Notify = function(){
        return NotifyServices.Data();
    }

    $scope.Refresh_Today_Patients = function(){
        return NotifyServices.Load_Today_Patients();
    }


    $scope.Changelog = function(){
        return ChangelogServices.Data();
    }
    
    $scope.View_Changelog = function(){

        $scope.toggleRight('user-sb'); 
        $scope.toggleRight('changelog-sb');
        
        ChangelogServices.Reload().then(function(data){

        });
    }


    $scope.Init = function(){
        
        NotifyServices.Reload_Today_Patients();

        global.Change_Layout();
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

        if( !$('.profile-sticky').hasClass('ontop') ){
            $('.profile-sticky').hide().addClass('ontop').fadeIn();
        }

        if( !$('.search-form').hasClass('ontop') ){
            $('.search-form').hide().addClass('ontop').fadeIn();
        }

        $('.mdl-layout__header').addClass('ontop');    
    } 
    else if ( $(this).scrollTop() <= 0) {
        
        if( $('.profile-sticky').hasClass('ontop') ){
            $('.profile-sticky').removeClass('ontop');
        }
        
        if( $('.search-form').hasClass('ontop') ){
            $('.search-form').removeClass('ontop');
        }

        $('.mdl-layout__header').removeClass('ontop');    
    }

});


