'use strict';


app.factory('Map',function ($http, $q, $timeout, $filter, global) {
   
   
   var map;
   var view;
   var geolocation;
   var accuracyFeature;
   var positionFeature;
   var overlay;
   var queLoad;

   var loading = '<div class="sk-folding-cube sm"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div> </div>';

   var data = {
      my_location: [],
      click_coord_long: [],
      click_coord_short: [],
      setupLocationName: '',
      setupLocation: false,
      lastPopName: null,
      enablePopup: false
   }

   
   var city = {
      cagayandeoro: [13875180.029802153,946422.5304604203]
   };


   function Init(){


      overlay = new ol.Overlay(({
         element: document.getElementById('mapPopup'),
         autoPan: true,
         autoPanAnimation: {
            duration: 250
         }
      }));

      
      view = new ol.View({
         center: city.cagayandeoro,
         zoom: 14
      });

      map = new ol.Map({
         layers: [
            new ol.layer.Tile({
               source: new ol.source.OSM()
            })
         ],
         overlays: [overlay],
         target: 'map',
         controls: ol.control.defaults({
            attributionOptions:  ({
               collapsible: false
            })
         }),
         view: view
      });



      map.on('click', function(evt){

         data.click_coord_long = angular.copy(evt.coordinate);
         data.click_coord_short = angular.copy(ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326'));
         
         if( data.setupLocation ){
            Remove_Layer_Name(data.setupLocationName);
            Add_Layer(data.setupLocationName, data.click_coord_long);
         }

         if( data.enablePopup ){
            var temp = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
               return [feature,layer];
            });

            if( temp ){
               Popup_Info(temp[0], temp[1], evt.coordinate);
            }
            else{
               Popup_Info(null);
            }
         }
      });


      map.on('pointermove', function(evt) {
         // map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
         //    Popup_Info(feature, layer, evt.coordinate);
         // });
      });

      map.on("moveend", function(evt){
         // 
      });
   }


   

   function Popup_Info(feature,layer,coordinate){
         
      if( feature ){ 
         
         if( data.lastPopName != layer.get('name') ){

            data.lastPopName = layer.get('name');
            
            $('#mapPopup').stop().hide().empty().append('<div class="content-wrapper animated fadeIn fast arrow_box"><div class="content"></div></div>');
            $('#mapPopup .content').empty().append(loading);
            $('#mapPopup').stop().show();

            if(queLoad )
            queLoad.resolve();
            
            Load_Schedules(layer.get('name'));

            overlay.setPosition(coordinate);

            Goto(coordinate, true);
         }
         else{
            $('#mapPopup').stop().show();
         }
      }
      else{
         
         $('#mapPopup').hide();
         data.lastPopName = null;
      }
   }



   function Load_Schedules(NAME){

      queLoad = $q.defer();

      $http.post( global.baseUrl + 'landing/map-clinic-schedules',{
         TOKEN: global.TOKEN,
         SUBCLINIC: NAME
      }, global.ajaxConfig).then(function(response){

         var clinicHtml = '';
         var scheduleHtml = '';
         var viewHtml = '';

         if( response.data.suc.CLINIC ){

            var clinic = response.data.suc.CLINIC;
            
            clinicHtml  += '<p class="font-weight-bold m-0">' + clinic.CLINICNAME+'</p>';

            if( clinic.CLINICSUBNAME != '')
            clinicHtml  += '<span>' + clinic.CLINICSUBNAME+'</span><br>';


            if( clinic.CLINICSUBNAME1 != '' )
            clinicHtml  += '<span>' + clinic.CLINICSUBNAME1+'</span><br>';


            if( clinic.EMAIL ){
               clinicHtml  += `
                  <div class="dflex">
                     <md-icon class="material-icons sm m-0">email</md-icon>
                     <div class="pl-2">`+ clinic.EMAIL + `</div>
                  </div>
               `;
            }

            if( clinic.CONTACTNO || clinic.MOBILENO  ){
               clinicHtml  += `
                  <div class="dflex">
                     <md-icon class="material-icons sm m-0">call</md-icon>
                     <div class="pl-2">
                        <div>`+ clinic.CONTACTNO + `</div>
                        <div>`+ clinic.MOBILENO + `</div>
                     </div>
                  </div>
               `;
            }
            
            if( clinic.MONTIME != null || clinic.TUETIME != null || clinic.WEDTIME != null || clinic.THUTIME != null || clinic.FRITIME != null || clinic.SATTIME != null || clinic.SUNTIME != null ){
               clinicHtml += `
                  <div class="table-pop">
                     <table>
                        <thead>
                           <tr>
                              <th colspan="7">Clinic Hours</th>
                           </tr>
                           <tr>
                              `+(clinic.MONTIME != null ? '<th>Mon</th>' : '' )+`
                              `+(clinic.TUETIME != null ? '<th>Tue</th>' : '' )+`
                              `+(clinic.WEDTIME != null ? '<th>Wed</th>' : '' )+`
                              `+(clinic.THUTIME != null ? '<th>Thu</th>' : '' )+`
                              `+(clinic.FRITIME != null ? '<th>Fri</th>' : '' )+`
                              `+(clinic.SATTIME != null ? '<th>Sat</th>' : '' )+`
                              `+(clinic.SUNTIME != null ? '<th>Sun</th>' : '' )+`
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              `+ (clinic.MONTIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.MONTIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.TUETIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.TUETIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.WEDTIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.WEDTIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.THUTIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.THUTIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.FRITIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.FRITIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.SATTIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.SATTIME))), 'h:mm a')+'</td>' : '') +`
                              `+ (clinic.SUNTIME != null ? '<td class="text-nowrap">'+$filter('date')( ((new Date(2020,1,1)).setMilliseconds(parseInt(clinic.SUNTIME))), 'h:mm a')+'</td>' : '') +`
                           </tr>
                        </tbody>
                     </table>
                  </div>
               `;
            }

            if( clinic.MONTIME != null ){
               clinic.MONTIME = parseInt(clinic.MONTIME);
               clinic.MONDATE = new Date(2020,1,1);
               clinic.MONDATE.setMilliseconds(clinic.MONTIME);

            }
            
                
            if( response.data.suc.SCHEDULES.length > 0 ){

               scheduleHtml = `
                  <thead>
                     <tr>
                        <th colspan="2">Online Appointment</th>
                     </tr>
                  </thead>
               `;
               angular.forEach( response.data.suc.SCHEDULES, function(v,k){

                  var remain = parseInt(v.MAXPATIENT) - parseInt(v.TOTAL_ACKNOWLEDGED);
                  if( remain < 0 ) remain =0;

                  scheduleHtml += '<tr>';
                  scheduleHtml += '<td>'+ $filter('date')(global.Date(v.SDATETIME), 'M/d/y hh:mm a' ) +'</td>';
                  scheduleHtml += '<td class="text-center"><a href="'+v.PROFILE+'">'+remain+'</a></td>';
                  scheduleHtml += '</tr>';
               });

               scheduleHtml = '<div class="table-pop"><table>'+ scheduleHtml +'</table></div>';
            }

            
            if( clinic.PROFILE ){
               viewHtml += `
                  <div class="text-center mt-3">
                     <a href="`+ clinic.PROFILE+`">view</a>
                  </div>
               `;
            }

            viewHtml += `
               <div class="top-right">   
                  <button onclick="$('#mapPopup').hide()" class="close">
                     <md-icon class="material-icons">close</md-icon>
                  </button>
               </div> `;



            $('#mapPopup .content').empty().append(clinicHtml + scheduleHtml + viewHtml);

            
         }
         else{
            $('#mapPopup').hide();
            data.lastPopName = null;
         }

         queLoad.resolve(true);
         
      }, function(err){
         $('#mapPopup .content').empty().append('<div>Please try again.</div>');
      });

      return queLoad.promise;
   }


   
   function Find_My_Location(){

      geolocation = new ol.Geolocation({
         projection: view.getProjection(),
         tracking: true,
         trackingOptions: {
            enableHighAccuracy: true,
            maximumAge: 2000  
         }
      });

      geolocation.on('error', function(error) {
         console.error(error);
      });
      
      geolocation.on('change:position', function() {
         My_Location(geolocation.getPosition());
      });


      // geolocation.on('change', function() {
      //    console.log(
      //    'Accuracy:'+geolocation.getAccuracy(),
      //    'Altitude:'+ geolocation.getAltitude(),
      //    'Alt Accuracy:' + geolocation.getAltitudeAccuracy(),
      //    'Heading:'+ geolocation.getHeading(),
      //    'Speed'+ geolocation.getSpeed()
      //    )
      // });


      // geolocation.setTracking(true);
      // accuracyFeature = new ol.Feature();

      // geolocation.on('change:accuracyGeometry', function() {
      //    accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
      // });

      // positionFeature = new ol.Feature();

      // positionFeature.setStyle(new ol.style.Style({
      //   image: new ol.style.Circle({
      //     radius: 5,
      //     fill: new ol.style.Fill({
      //       color: '#3399CC'
      //     }),
      //     stroke: new ol.style.Stroke({
      //       color: '#fff',
      //       width: 2
      //     })
      //   })
      // }));

      // geolocation.on('change:position', function() {
      //    var coordinate = geolocation.getPosition();
      //    positionFeature.setGeometry(coordinate ? new ol.geom.Point(coordinate) : null);
      //    My_Location(coordinate);
      // });
 
      // new ol.layer.Vector({
      //    map: map,
      //    source: new ol.source.Vector({
      //       features: [accuracyFeature, positionFeature]
      //    })
      // });
   }


   function My_Location(coordinate){

      geolocation.setTracking(false);
      // view.setZoom(15);

      data.my_location = angular.copy(coordinate);
      view.setCenter(coordinate);

      var vectorLayer = new ol.layer.Vector({
         name: 'my_location',
         source: new ol.source.Vector({
            features: [new ol.Feature({
                 geometry: new ol.geom.Point(coordinate),
            })]
         }),
         style: new ol.style.Style({
            image: new ol.style.Icon({
               anchor: [0.5, 1],
               anchorXUnits: "fraction",
               anchorYUnits: "fraction",
               src: global.baseUrl + 'assets/css/images/map/my_location_24.svg'
            })
         })
      });

      // image: new ol.style.Circle({
      //    radius: 6,
      //    fill: new ol.style.Fill({
      //       color: '#03A9F4'
      //    }),
      //    stroke: new ol.style.Stroke({
      //       color: '#333333',
      //       width: 1
      //    })
      // })
 
      map.addLayer(vectorLayer);
      Goto(coordinate);
   }



   function Add_Layer(name, coordinate, imageType){

      if( imageType == 'focus' ){
         var imageSrc = global.baseUrl + 'assets/css/images/map/clinic_focus.png';
      }
      else{
         var imageSrc = global.baseUrl + 'assets/css/images/map/clinic1.png'; 
      }

      var vectorLayer = new ol.layer.Vector({
         name: name,
         source: new ol.source.Vector({
            features: [new ol.Feature({
                 geometry: new ol.geom.Point(coordinate)
                 //geometry: new ol.geom.Point(ol.proj.transform([parseFloat(lng), parseFloat(lat)], 'EPSG:4326', 'EPSG:3857')),
            })]
         }),
         style: new ol.style.Style({
            image: new ol.style.Icon({
               anchor: [0.5, 1],
               anchorXUnits: "fraction",
               anchorYUnits: "fraction",
               src: imageSrc
            })
         })
      });
 
      map.addLayer(vectorLayer);     
   }


   function Remove_Layer_Name(name){
      map.getLayers().forEach(layer => {
         if (layer && layer.get('name') === name) {
            map.removeLayer(layer);
         }
      });
   }


   function Remove_All_Layer(){
      map.getLayers().forEach(layer => {
         map.removeLayer(layer);
      });
   }


   function Goto(location, focus) {

      if( data.enablePopup && focus == undefined )
      $('#mapPopup').hide();

      var pan = ol.animation.pan({
        source: map.getView().getCenter()
      });

      map.beforeRender(pan);

      map.getView().setCenter(location);
   }




   return{
      Data: function(){
         return data;
      },
      Init: function(){
         Init();        
      },
      Set_My_Location: function(){
         Find_My_Location();
      },
      Add_Layer: function(name, coordinate, imageType){
         Add_Layer(name, coordinate, imageType);
      },
      Remove_All_Layer: function(){
         Remove_All_Layer();
      },
      Remove_Layer_Name: function(name){
         Remove_Layer_Name(name);
      },
      Goto: function(coordinate){
         Goto(coordinate);
      },
      Clear_Coordinate: function(){
         data.click_coord_long = null;
         data.click_coord_short = null;
      }
   }
});