app.factory('PDF', function ($http, $q) {


	pdfjsLib.GlobalWorkerOptions.workerSrc ='assets/pdfjs/pdf.worker.js';

	var isPdfLoading = true;
	var Path = '';
	var ContentID = '';
	var currPage = 1; 
	var scale = 1.5;
	var numPages = 0;
	var thePDF = null;
	var Title = '';
	// var isMenu =  is.not.firefox() ? true: false;
	var isMenu =  false;
	var isPrintDisable = false;


	function handlePages(page) {

		var canvasExist = false;

		if( document.getElementById("pdf_" + currPage) ){
			var canvas = document.getElementById("pdf_" + currPage);
			canvasExist = true;
		}
		else{
			var canvas = document.createElement( "canvas" );
			canvas.setAttribute("id", "pdf_" + currPage);
		}

		var viewport = page.getViewport({ scale: scale});

		var context = canvas.getContext('2d');
		canvas.height = viewport.height;
		canvas.width = viewport.width;

		page.render({canvasContext: context, viewport: viewport});

		if( ! canvasExist ){

			var div = document.createElement( "div" );
			div.setAttribute('class','page');
			div.appendChild(canvas);
			document.getElementById( ContentID ).appendChild(div);
		}

		currPage++;
		if ( thePDF !== null && currPage <= numPages ){
			thePDF.getPage( currPage ).then( handlePages );
		}

		if( currPage > numPages )
			isPdfLoading = false;
	}	

	function User_Frame(){

		var iframe = document.createElement("iframe")
		iframe.setAttribute('src', Path);
		document.getElementById( ContentID ).appendChild(iframe);

		isPdfLoading = false;
	}

	return{

		Init: function(CONTENTID,PATH){

			isPdfLoading = true;

			var deferred = $q.defer();

			ContentID = CONTENTID;
			Path = PATH;

			if(  isMenu ){

				pdfjsLib.getDocument(Path).then(function(pdf) {

					thePDF = pdf;

					if( thePDF != null ){

						currPage = 1; 
						numPages = pdf.numPages;
						// scale 	 = 1.5;

						pdf.getPage( currPage ).then(handlePages);

											
						deferred.resolve(true);
					}
					else{
						deferred.reject(false);
					}

				});
			}
			else{
				User_Frame();
				deferred.resolve(true);
			}

			return deferred.promise;
		},	

		ScaleInc: function(){
			isPdfLoading = true;

			scale += 0.25;
			currPage = 1;
			
			thePDF.getPage(currPage).then(handlePages);
			isPdfLoading = false;
		},

		ScaleDec: function(){

			if( scale > 0.5 ){

				isPdfLoading = true;

				scale -= 0.25;
				currPage = 1;

				thePDF.getPage(currPage).then(handlePages);
				isPdfLoading = false;
			}
		},

		Print: function(){

			printJS(Path);

		},
		Get_Menu_Status: function(){
			return isMenu;
		},
		Get_Load_Status: function(){
			return isPdfLoading;
		}

	}

});