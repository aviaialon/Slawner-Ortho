/**
 * This class represent an url.
 * Compatible : IE, Firefox, Safari, Opera
 * @package LEGACY::NET
 * @author Inoveo technologie inc.
 */
if (LEGACY == undefined) var LEGACY = {};
if (LEGACY.NET == undefined) LEGACY.NET = {};
(LEGACY.NET.URL = function (asUrl) {
	
	this.sOriginalUrl;						// http://www.domain.com/path/to/file.php?id=44
	
	this.sProtocol;							// HTTP, FTP, ...
	this.sUserName;	
	this.sPassword;
	this.sHost;								// www.domaine.com
	this.iPort;								// HTTP = 80, FTP = 21
	this.sPath;								// /path/to/file.php
	this.aAttribute = new Object();	// id=44
	this.strFrangment = ''; 

	LEGACY.NET.URL.prototype.parseUrl = function parseUrl(asUrl) {
		var aUrl = []; 

		this.sOriginalUrl = asUrl;

		re = new RegExp("^(([a-zA-Z]+)://((([a-zA-Z.]+):)?([a-zA-Z.]+)@)?([a-zA-Z0-9.-]+)(:([0-9]*))?)?([^?#]*)[?]?([^#$]*)?(#([a-zA-Z0-9=&]*))?$");
		aUrl = re.exec(asUrl);

		this.sProtocol = (aUrl[2] ? aUrl[2] : undefined);
		this.sUserName = (aUrl[5] ? aUrl[5] : undefined);
		this.sPassword = (aUrl[6] ? aUrl[6] : undefined);
		if (this.sPassword && !this.sUserName) {
			this.sUserName = this.sPassword;
			this.sPassword = undefined;
		}

		this.sHost = (aUrl[7] ? aUrl[7] : undefined);
		this.iPort = (aUrl[9] ? parseInt(aUrl[9]) : undefined);
		this.sPath = (aUrl[10] ? aUrl[10] : undefined); 

		var sAttr = aUrl[11];
		var aAttr = new Array();

		if (sAttr != undefined) { aAttr = sAttr.split('&'); }
		
		var sReg = /\+/g;
		for(i in aAttr) if (aAttr[i]) {
			aElem = aAttr[i].split('=');
			this.aAttribute[unescape(aElem[0]).replace(sReg, ' ')] = (unescape(aElem[1]).replace(sReg, ' '));
		}

		this.strFragment = aUrl[13];

	}
	
	LEGACY.NET.URL.prototype.setProtocol = function setProtocol(asProtocol) { 
		this.sProtocol = asProtocol; 
	}

	LEGACY.NET.URL.prototype.getProtocol = function getProtocol() {
		return this.sProtocol; 
	}

	LEGACY.NET.URL.prototype.setUserName = function setUserName(asUserName) {
		this.sUserName = asUserName;	
	}

	LEGACY.NET.URL.prototype.getUserName = function getUserName() { 
		return this.sUserName; 
	}

	LEGACY.NET.URL.prototype.setPassword = function setPassword(asPassword) {
		this.sPassword = asPassword;	
	}

	LEGACY.NET.URL.prototype.getPassword = function getPassword() { 
		return this.sPassword; 
	}

	LEGACY.NET.URL.prototype.setHost = function setHost(asHost) { 
		this.sHost = asHost; 
	}

	LEGACY.NET.URL.prototype.getHost = function getHost() {
		return this.sHost; 
	}

	LEGACY.NET.URL.prototype.setPort = function setPort(aiPort) {
		this.iPort = aiPort; 
	}

	LEGACY.NET.URL.prototype.getPort = function getPort(bProtocolBase) { 
		if (this.iPort) { return this.iPort; }
		var iReturnPort = undefined;
		if (bProtocolBase == true) {
			switch(this.sProtocol) {
			case 'http':
				iReturnPort = 80;
			break;
			case 'https':
				iReturnPort = 443;
			break;
			case 'ftp':
				iReturnPort = 21;
			break;
			}
		}
		return iReturnPort; 
	}

	LEGACY.NET.URL.prototype.setPath 	= function setPath(asPath) { this.sPath = asPath; }
	LEGACY.NET.URL.prototype.appendPath = function setPath(asPath) { 
		if (typeof this.sPath !== "undefined") {
			this.sPath += (asPath.charAt(0) == '/' ? asPath : '/' + asPath);
		}
		else
		{
			this.sPath = (asPath.charAt(0) == '/' ? asPath : '/' + asPath);
		}
	}
	
	LEGACY.NET.URL.prototype.setRelativePath = function setRelativePath(asPath) {
		this.sPath = this.sPath.substring(0, this.sPath.lastIndexOf('/'));
		while (asPath.length >= 3 && asPath.substr(0, 3) == '../') {
			this.sPath = this.sPath.substring(0, this.sPath.lastIndexOf('/'));
			asPath = asPath.substring(3, asPath.length);
		}
		this.sPath = this.sPath + '/' + asPath;
	}

	LEGACY.NET.URL.prototype.getPath = function getPath() {	return this.sPath; }

	LEGACY.NET.URL.prototype.setAttribute = function setAttribute(sName, sValue) { 
		this.aAttribute[sName] = sValue; 
	}

	LEGACY.NET.URL.prototype.getAttribute = function getAttribute(sName) { 
		return this.aAttribute[sName]; 
	}
	
	LEGACY.NET.URL.prototype.getAllAttribute = function getAllAttribute() {
		var oRet = new Object();
		var bAttributes = false;
		for(i in this.aAttribute) {
			if (i && this.aAttribute[i]) {
				bAttributes = true;
				oRet[i] = this.aAttribute[i];
			}
		}

		return (bAttributes ? oRet : undefined);
	}

	LEGACY.NET.URL.prototype.removeAttribute = function removeAttribute(strAttribute) {
		if(this.aAttribute[strAttribute]) {
			var arrReplace = new Object();
			for(i in this.aAttribute) {
				if(i != strAttribute) {
					arrReplace[i] = this.aAttribute[i];
				}
			}
			this.aAttribute = arrReplace;
		}
	}

	LEGACY.NET.URL.prototype.setFragment = function setFragment(strFragment) {
		this.strFragment = strFragment;
	}

	LEGACY.NET.URL.prototype.getFragment = function getFragment() {
		return this.strFragment;
	}
	
	LEGACY.NET.URL.prototype.removeFragment = function removeFragment() {
		this.strFragment = '';
	}

	LEGACY.NET.URL.prototype.toString = function toString() {
		
		var sUrl = '';
		var sAttribute = '';

		sUrl += (this.sProtocol ? this.sProtocol + '://' : '');
		sUrl += (this.sUserName ? this.sUserName : '');
		sUrl += (this.sPassword ? (this.sUserName ? ':' : '') + this.sPassword : '');
		sUrl += (this.sUserName || this.sPassword ? '@' : '') ;
		sUrl += (this.sHost ? this.sHost : '');
		sUrl += (this.iPort ? ':' + this.iPort : '');
		sUrl += (this.sPath ? this.sPath : '');

		for(i in this.aAttribute) {
			sAttribute += (sAttribute ? '&' : '');
			sAttribute += escape(i) + '=' + escape(this.aAttribute[i]);
		}
		sUrl += (sAttribute ? '?' + sAttribute : '');
		sUrl += (this.strFragment != '' && typeof this.strFragment != 'undefined' ? '#' + this.strFragment : ''); 

		return sUrl; 
	}

	if (LEGACY.NET.URL.className == undefined) {
	
		/**
		 * Retrieve an url from current document.
		 * @return object LEGACY.NET.URL
		 */
		LEGACY.NET.URL.getCurrent = function getCurrent() {
			var oUrl = new LEGACY.NET.URL();
			oUrl.parseUrl(document.location.href);
			return oUrl;
		}
		
		LEGACY.NET.URL.className = 'LEGACY.NET.URL';
	}

	this.jsClass = LEGACY.NET.URL;

	if (asUrl != undefined) {
		this.parseUrl(asUrl);
	}
})();