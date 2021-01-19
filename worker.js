importScripts('js/localForage.js');

function isOnline(no,yes){
	var xhr = XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHttp');
	xhr.onload = function(){
		if (xhr.status >= 200 && xhr.status < 400) {
			if(yes instanceof Function){
				yes();
			}
		} else {
			if(no instanceof Function){
				no();
			}
		}
	}
	xhr.onerror = function(){
		if(no instanceof Function){
			no();
		}
	}
	xhr.open("GET","test2.php",true);
	xhr.send();
}




function timedCount() {
	isOnline(
		function(){
			postMessage(0)
		},
		function(){
			postMessage(1)
		}
	);
/*
	var request = new XMLHttpRequest();
	request.open('GET', 'ajax/ajax_pos.php?functionName=getCategories', true);

	request.onload = function() {
		if (request.status >= 200 && request.status < 400) {

			postMessage(request.responseText)

		} else {


		}
	};

	request.onerror = function() {
		// There was a connection error of some sort
	};

	request.send();
*/
	setTimeout("timedCount()",5000);
}

timedCount();