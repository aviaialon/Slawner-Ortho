/**
 * MAP_DIRECTION_API constructor and main entry point
 *   
 * @author: Avi Aialon <aviaialon@gmail.com>
 * @dependencies: jQuery 1.7+
 */
var MAP_DIRECTION_API = function(objParams)
{
	// 45.493413, -73.685882
	this.map;
	this.hasPendingQueue	= false;
	this.onReadyCallback	= null;
	this.directionsService 	= new google.maps.DirectionsService();
	this.directionsDisplay 	= new google.maps.DirectionsRenderer({
	    draggable: true,
	    suppressMarkers: false
	});	
	this.setDestination(objParams);
	return (this);
}

/**
 * Sets a callback method to load when ready!
 *
 * @access 	public
 * @param	function fnOnReadyCallback - The callback method to use when ready
 * @return	void
 */
MAP_DIRECTION_API.prototype.onReady = function(fnOnReadyCallback)
{
	if (typeof fnOnReadyCallback == "function") {
		this.onReadyCallback = fnOnReadyCallback;	
		if (! this.hasPendingQueue) {
			this.callOnReady();	
		}
	}
	return (this);
}

/**
 * Calls the onReady callback method
 *
 * @access 	public
 * @param	function fnOnReadyCallback - The callback method to use when ready
 * @return	void
 */
MAP_DIRECTION_API.prototype.callOnReady = function(fnOnReadyCallback)
{
	if (typeof this.onReadyCallback == "function") {
		this.onReadyCallback(this);	
		this.onReadyCallback = null; // Clear it so it doesnt get re-called numerous times.
		this.hasPendingQueue = false;
	}
}

/**
 * Sets the end point address (lat/lng) for the direction service
 *
 * @access 	public
 * @param 	mixes mxDestination - The destination. either an address, or a lat/lng object [objParams.destination.lat / objParams.destination.lng]
 * @param	function fnSuccessCallback - The callback method to use when data is received
 * @return	void
 */
MAP_DIRECTION_API.prototype.setDestination = function(mxDestination, fnSuccessCallback)
{
	var me = this;
	var onDataReceiveCallback = (typeof fnSuccessCallback == "function" ? fnSuccessCallback : new function() {});
	
	if (
		(typeof mxDestination !== "undefined") &&
		(typeof mxDestination.destination !== "undefined") &&
		(typeof mxDestination.destination.lat !== "undefined") &&
		(typeof mxDestination.destination.lng !== "undefined") 
	) {
		this.mapEndPoint = new google.maps.LatLng(
			mxDestination.destination.lat,
			mxDestination.destination.lng
		);
		try { 
			onDataReceiveCallback(); 
			this.callOnReady();
		}
		catch (e) {}
			
	}
	else if (typeof mxDestination == "string") {
		this.hasPendingQueue = true;
		this.getAddressGeocode(mxDestination, function(latLngObject){
			me.setDestination({
				'destination': {
					'lat': latLngObject.lat,
					'lng': latLngObject.lng
				}	
			}, onDataReceiveCallback);
		}, function(strErrorStatus) {
			alert(strErrorStatus);
			me.hasPendingQueue = false;
		});
	}
	
	return (this);
}


/**
 * This method attaches a auto complete field input
 * see: https://developers.google.com/maps/documentation/javascript/places#place_details_results
 * @access 	public
 * @param 	string 		strFieldInput	- The field input Id
 * @param 	function	fnOnInputChangeCallback	- A method to call when the fields data has changed. if none are provided, the getDirectionsFromAddress() methoid is called
 * @return 	void
 */
MAP_DIRECTION_API.prototype.setAutoCompleteFieldInput = function(strFieldInput, fnOnInputChangeCallback)
{
	var me = this;
	var callBackMethod = fnOnInputChangeCallback;
	
	if (typeof document.getElementById(strFieldInput) !== "undefined")  {
		// Create the auto complete field if it isnt already initialised
		if (! this.getVariable('AutoCompleteFieldInputId')) {
			this.setVariable('AutoCompleteFieldInputId', strFieldInput);
			this.setVariable('AutoCompleteFieldInput', 
				new google.maps.places.Autocomplete(document.getElementById(this.getVariable('AutoCompleteFieldInputId')))
			);	
		}
		
		if (typeof this.map !== "undefined") {
			// Bind the field to the map object if it exists
			this.getVariable('AutoCompleteFieldInput').bindTo('bounds', this.map);
		}
		
		// Onchange event listener
		google.maps.event.addListener(this.getVariable('AutoCompleteFieldInput'), 'place_changed', function() {
			if (typeof callBackMethod == "undefined") {
				 me.getDirectionsFromAddress(
					me.getVariable('AutoCompleteFieldInput').getPlace().formatted_address,
					me.getVariable('directionResultContainer')
				);
			} else {
				callBackMethod(me.getVariable('AutoCompleteFieldInput').getPlace());
			}
		});
	}
}


/**
 * This method attaches a map panel for the directions
 *
 * @access 	public
 * @param 	string 		strMapContainerId		- The map container Id
 * @return 	void
 */
MAP_DIRECTION_API.prototype.setMapPanel = function(strMapContainerId)
{
	if (typeof document.getElementById(strMapContainerId) !== "undefined")
	{
		this.setVariable('mapPanelId', strMapContainerId);
		this.setVariable('mapOptions', {
			zoom: 		7,
			mapTypeId: 	google.maps.MapTypeId.ROADMAP,
			center: 	this.mapEndPoint
		});
		
		
		if (this.getVariable('directionsLoaded')) {
			this.map = new google.maps.Map(document.getElementById(this.getVariable('mapPanelId')), this.getVariable('mapOptions'));
			this.directionsDisplay.setMap(this.map);		
			this.setAutoCompleteFieldInput(this.getVariable('AutoCompleteFieldInputId'));
		}
	}
	
	return (this);
}

/**
 * This method sets the direction panel
 *
 * @access 	public
 * @param 	string 	strPanel	- The direction panel
 * @return 	void
 */
MAP_DIRECTION_API.prototype.setDirectionPanel = function(strPanel)
{
	this.setVariable('directionResultContainer', strPanel);
}

/**
 * Object Variable Getter
 *
 * @access 	public
 * @param 	string strVariableName - The variable name
 * @return	mixed
 */
MAP_DIRECTION_API.prototype.getVariable = function(strVariableName)
{
	var mxReturnData = false;
	if (typeof this[strVariableName] !== "undefined") {
		mxReturnData = this[strVariableName];
	}
	
	return (mxReturnData);
}


/**
 * Object Variable Setter
 *
 * @access 	public
 * @param 	string strVariableName - The variable name
 * @param 	mixed  mxVariableValue - The variable value
 * @return	void
 */
MAP_DIRECTION_API.prototype.setVariable = function(strVariableName, mxVariableValue)
{
	this[strVariableName] = (
		(typeof mxVariableValue !== "undefined") ? mxVariableValue : false
	);
}


/**
 * Gets the users current geo location
 *
 * @access 	public
 * @param 	function fnOnDataReceiveCallBack - The callback method to use when data is received
 * @return 	object 		- The user's current geo position
 */
MAP_DIRECTION_API.prototype.getGeoLocation = function(fnOnDataReceiveCallBack)
{
	var me = this;
	// Set the onDataReceive Callback method
	this.setVariable('callBackMethod', (typeof fnOnDataReceiveCallBack == "function") ? fnOnDataReceiveCallBack : function() {});
	this.hasPendingQueue = true;
	
	try {
		// Try to extract geo location from a HTML enabled browser
		// CallBackMethos passes a objPosition with data such as:
		// position.coords.latitude + ',' + position.coords.longitude;
		navigator.geolocation.getCurrentPosition(
			function(objPosition) { 
				var callBackMethod = me.getVariable('callBackMethod');
				// Here, were going to clone the return object to avoid the
				// "Cannot modify properties of a WrappedNative" error
				var objReturnGeoPosition = {};
				for (i in objPosition) {
					objReturnGeoPosition[i] = objPosition[i];
				}
				objReturnGeoPosition.type = 'E_CORDS_HTML5'
				callBackMethod(objReturnGeoPosition);
				me.hasPendingQueue = false;
			}, 
			function (objError) 
			{
				var returnObjectPosition = {
					'coords': {
						'latitude' : 	geoplugin_latitude(),
						'longitude' :	geoplugin_longitude()
					}
				};
				
				switch(objError.code)
				{
					case objError.PERMISSION_DENIED: 
						// Permission denied.
						returnObjectPosition.type = 'E_CORDS_PERMISSION_DENIED';
					break;
			
					case objError.POSITION_UNAVAILABLE: 
						// could not detect current position.
						returnObjectPosition.type = 'E_CORDS_COORDS_UNAVAILABLE';
					break;
			
					case objError.TIMEOUT: 
						// retrieving position timed out
						returnObjectPosition.type = 'E_CORDS_TIMEOUT';
					break;
			
					default: 
						// Unknown error... get a better browser,
						returnObjectPosition.type = 'E_CORDS_ERROR_UNKNOWN';
					break;
				}
				
				// Try with the internal geo positioning system
				//currentLatLong = geoplugin_latitude() + "," + geoplugin_longitude();
				me.hasPendingQueue = false;
				var returnFunctionCallback = me.getVariable('callBackMethod');
				returnFunctionCallback(returnObjectPosition);
			}
		);
		  
	} catch (Exception) {
		me.hasPendingQueue = false;
		var returnFunctionCallback = me.getVariable('callBackMethod');
		returnFunctionCallback({
			'type': 'E_RECOVERABLE_ERROR',
			'coords': {
				'latitude' : 	geoplugin_latitude(),
				'longitude' :	geoplugin_longitude()
			}
		});
	}
	
	return (this);
}

/**
 * Gets the text directions from an address
 *
 * @access 	public
 * @param 	string strEndPointAddress - The start point address. 
 * @param 	string strResultContainer - A element ID where the text directions are rendered, if nothing is passed, then only the directions are returned
 * @param 	string strTravelMode	  - The travel mode. Defaults to google.maps.DirectionsTravelMode.DRIVING
 * @return 	void
 */
MAP_DIRECTION_API.prototype.getDirectionsFromAddress = function(strEndPointAddress, strResultContainer, fnOnComplete, strTravelMode)
{
	var me = this;
	var fnOnCompleteHandler  = (typeof fnOnComplete == "function" ? fnOnComplete : function() {});
	var objDirectionRequest  = 
	{
        origin: 					strEndPointAddress,
        destination: 				this.mapEndPoint,
        optimizeWaypoints: 			true,
		provideRouteAlternatives: 	true,
        travelMode: 				strTravelMode || google.maps.DirectionsTravelMode.DRIVING
    };
    this.directionsService.route(objDirectionRequest, function (response, status) 
    {
        if (status == google.maps.DirectionsStatus.OK) 
        {
			me.setVariable('directionResultContainer', strResultContainer);
			me.setVariable('directionsLoaded', true);
			me.directionsDisplay.setPanel(document.getElementById(strResultContainer) || null);
            me.directionsDisplay.setDirections(response);
			if (me.getVariable('mapPanelId')) {
				me.setMapPanel(me.getVariable('mapPanelId'));
			}
		    me.setVariable('directions', me.directionsDisplay.directions);
			google.maps.event.addListener(me.directionsDisplay, 'directions_changed', function () {
		        me.calculateDistance(me.directionsDisplay.directions);
		    });
			
			document.getElementById(me.getVariable('mapPanelId')).className = 'borderWebkit';
			document.getElementById(strResultContainer).className = 'borderWebkit';
			document.getElementById(me.getVariable('totalDistancePanel')).innerHTML = me.calculateDistance(me.directionsDisplay.directions);
			document.getElementById(me.getVariable('moreInfoPanel')).style.display = 'block';
			
			me.callOnReady();
			fnOnCompleteHandler();
        }
		else if (status = google.maps.DirectionsStatus.NOT_FOUND) 
		{ 
            alert("Sorry.. Directions not found."); 
        } 
        else if (status = google.maps.DirectionsStatus.ZERO_RESULTS) 
        { 
            alert("ZERO_RESULTS"); 
        } 
        else if (status = google.maps.DirectionsStatus.MAX_WAYPOINTS_EXCEEDED) 
        { 
            alert("MAX_WAYPOINTS_EXCEEDED"); 
        } 
        else if (status = google.maps.DirectionsStatus.INVALID_REQUEST) 
        { 
            alert("INVALID_REQUEST"); 
        } 
        else if (status = google.maps.DirectionsStatus.OVER_QUERY_LIMIT) 
        { 
            alert("OVER_QUERY_LIMIT"); 
        } 
        else if (status = google.maps.DirectionsStatus.REQUEST_DENIED) 
        { 
            alert("REQUEST_DENIED"); 
        } 
        else 
        { 
            alert("UNKNOWN_ERROR"); 
        }
    });
    
    return (this);
}

/**
 * Calculates the total travel distance between the start point and end point
 *
 * @access 	public
 * @param 	object objDirection - The direction object returned by google 
 * @return 	string 				- Returns the distance in KM
 */
MAP_DIRECTION_API.prototype.calculateDistance = function(objDirection)
{
	// This method computes the total distance
	var total 		= 0;
	var myroute 	= objDirection.routes[0];
	for (i = 0; i < myroute.legs.length; i++) 
	{
		total += myroute.legs[i].distance.value;
	}
	total = total / 1000.
	return (total + " km");
}

/**
 * Converts a lat/lng points into an address
 * see: https://developers.google.com/maps/documentation/javascript/geocoding
 * 
 * @access 	public
 * @param 	float 		fltLat				- The address latitude
 * @param 	float 		fltLng				- The address longitude
 * @param 	function 	fnSuccessCallbackMethod	- A callback once complete on success
 * @param 	function 	fnErrorCallbackMethod	- A callback once complete on Error
 * @return 	object 		MAP_DIRECTION_API
 */
MAP_DIRECTION_API.prototype.getAddressReverseGeocode = function(fltLat, fltLng, fnCallbackMethod, fnErrorCallbackMethod)
{	
	var me	 					= this;
	this.hasPendingQueue 		= true;
	var objGeoCoder 			= new google.maps.Geocoder();
	var successCallbackHandler 	= (typeof fnCallbackMethod == "function" ? fnCallbackMethod : new function() {});
	var errorCallbackHandler 	= (typeof fnCallbackMethod == "function" ? fnErrorCallbackMethod : new function() {});
	
	var lat = parseFloat(fltLat);
    var lng = parseFloat(fltLng);
    var latlng = new google.maps.LatLng(lat, lng);
	
	objGeoCoder.geocode({'latLng': latlng}, function(arrResults, status) {
		if (status == google.maps.GeocoderStatus.OK && arrResults[0]) {
			successCallbackHandler(arrResults[0].formatted_address);
		} else {
			errorCallbackHandler(status);
		}
		me.hasPendingQueue = false;
	});
	
	return (this);
}

/**
 * Converts a string address to lat/lng object via google geocode
 * see: https://developers.google.com/maps/documentation/javascript/geocoding
 * see: http://stackoverflow.com/questions/6284457/will-the-function-wait-for-the-asynchronous-functions-completion-before-returnin
 * @access 	public
 * @param 	string 		strAddress				- The address to geocode 
 * @param 	function 	fnSuccessCallbackMethod	- A callback once complete on success
 * @param 	function 	fnErrorCallbackMethod	- A callback once complete on Error
 * @return 	object 		MAP_DIRECTION_API
 */
MAP_DIRECTION_API.prototype.getAddressGeocode = function(strAddress, fnCallbackMethod, fnErrorCallbackMethod)
{
	var me	 					= this;
	this.hasPendingQueue 		= true;
	var objGeoCoder 			= new google.maps.Geocoder();
	var successCallbackHandler 	= (typeof fnCallbackMethod == "function" ? fnCallbackMethod : new function() {});
	var errorCallbackHandler 	= (typeof fnCallbackMethod == "function" ? fnErrorCallbackMethod : new function() {});
	objGeoCoder.geocode({'address': strAddress}, function(arrResults, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			successCallbackHandler({
				'lat': arrResults[0].geometry.location.lat(),
				'lng': arrResults[0].geometry.location.lng()
			});
		} else {
			errorCallbackHandler(status);
		}
		
		me.hasPendingQueue = false;
	});
	
	return (this);
}