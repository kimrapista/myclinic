'use strict';


app.controller('MedicalRecords', function($scope, $mdDialog, $q, $location, $timeout, MedicalRecordsServices, SubClinicServices, Preview, global){ 


    global.page.title = 'Medical Records';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;
 
    $scope.search = {
        name: '',
        dateFrom: new Date(),
        dateTo: new Date(),
        limit : 40,
        from : 0,
        isSubmit: false,
        viewMore: true,
        subclinic: 0,
        filterType: 'PATIENT',
        procedures: null
    }

    $scope.summary = {TOTAL_PENDING: 0, TOTAL_SERVED: 0};

    
    $scope.Data = function(){
        return MedicalRecordsServices.Data();
    }

    $scope.Subclinic = function(){
        return SubClinicServices.Data();
    }


    $scope.Submit_Search = function(){

        $scope.search.isSubmit = true;
        $scope.search.viewMore = false;
        $scope.search.from = 0;

        MedicalRecordsServices.Load({
            SEARCH: $scope.search.name,
            SUBCLINICID: $scope.search.subclinic,
            FILTERTYPE: $scope.search.filterType,
            DATEFROM: $scope.search.dateFrom,
            DATETO: $scope.search.dateTo,
            FROM: $scope.search.from, 
            TO: $scope.search.limit
        }).then(function(data){

            if( MedicalRecordsServices.Data().length >= $scope.search.limit )
                $scope.search.viewMore = true;

            $scope.search.isSubmit = false;
        });

        MedicalRecordsServices.Search_Summary($scope.search.dateFrom, $scope.search.dateTo)
		.then(function(data){
            $scope.summary = data;
        });
    }


    $scope.Submit_Search_More = function(){

        if( $scope.search.isSubmit )
            return false;
        
        $scope.search.isSubmit = true;
        $scope.search.from += $scope.search.limit;

        MedicalRecordsServices.Load({
            SEARCH: $scope.search.name, 
            SUBCLINICID: $scope.search.subclinic,
            FILTERTYPE: $scope.search.filterType,
            DATEFROM: $scope.search.dateFrom,
            DATETO: $scope.search.dateTo,
            FROM: $scope.search.from, 
            TO: $scope.search.limit
        }).then(function(data){
            
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

    $scope.MR_Laboratory = function(ID){
        Preview.Laboratory(ID);
    }
    $scope.MR_LabMonitoring = function(ID){
        Preview.LabMonitoring(ID);
    }

    $scope.MR_Preview = function(ID){
        Preview.Medical_Record(ID);
   
    }


    $scope.Edit_MR_Link = function(PATIENTID, MRID){

        $location.url('/patient/'+ PATIENTID +'/'+ MRID +'/medical-record');
    }

    $scope.Patient_Record_Link = function(PATIENTID){

        $location.url('/patient/'+ PATIENTID +'/record');
    }

    
    $scope.Init = function(){
        
        $q.all([
            SubClinicServices.Reload()
        ]).then(function(result){

            $scope.Submit_Search();
            $scope.isLoaded = true;
        })
    }

  
});
