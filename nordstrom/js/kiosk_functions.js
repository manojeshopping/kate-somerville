// Kiosk API of Kiosk Pro Enterprise Developer (6.2.3442)
function isChrome()
{
	return navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
}

// Common JS-ObjC-Bridge API:
function ___kp_executeURL(url)
{
	var iframe = document.createElement("IFRAME");
	iframe.setAttribute("src", url);
	document.documentElement.appendChild(iframe);
	iframe.parentNode.removeChild(iframe);
	iframe = null;
}

// iFrame support:
var _kp_i_, _kp_frames_special_;
_kp_frames_special_ = document.getElementsByTagName("iframe");

// Kiosk Version API:
function kp_VersionAPI_requestFullVersion(callback)
{
	___kp_executeURL("kioskpro://kp_VersionAPI_requestFullVersion&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_VersionAPI_requestFullVersion'] = function(callback) { kp_VersionAPI_requestFullVersion(callback); };
}

function kp_VersionAPI_requestMainVersion(callback)
{
	___kp_executeURL("kioskpro://kp_VersionAPI_requestMainVersion&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_VersionAPI_requestMainVersion'] = function(callback) { kp_VersionAPI_requestMainVersion(callback); };
}

function kp_VersionAPI_requestBuildNumber(callback)
{
	___kp_executeURL("kioskpro://kp_VersionAPI_requestBuildNumber&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_VersionAPI_requestBuildNumber'] = function(callback) { kp_VersionAPI_requestBuildNumber(callback); };
}

function kp_VersionAPI_requestProductName(callback)
{
	___kp_executeURL("kioskpro://kp_VersionAPI_requestProductName&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_VersionAPI_requestProductName'] = function(callback) { kp_VersionAPI_requestProductName(callback); };
}

function kp_VersionAPI_requestProductNameWithFullVersion(callback)
{
	___kp_executeURL("kioskpro://kp_VersionAPI_requestProductNameWithFullVersion&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_VersionAPI_requestProductNameWithFullVersion'] = function(callback) { kp_VersionAPI_requestProductNameWithFullVersion(callback); };
}

// File API:
function writeToFile(fileName,data,callback)
{
	___kp_executeURL("kioskpro://writeToFile&" + encodeURIComponent(fileName) + "?" + encodeURIComponent(data) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['writeToFile'] = function(fileName,data,callback) { writeToFile(fileName,data,callback); };
}

function fileExists(filename,callback)
{
	___kp_executeURL("kioskpro://fileExists&" + encodeURIComponent(filename) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['fileExists'] = function(filename,callback) { fileExists(filename,callback); };
}

function deleteFile(filename,callback)
{
	___kp_executeURL("kioskpro://deleteFile&" + encodeURIComponent(filename) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['deleteFile'] = function(filename,callback) { deleteFile(filename,callback); };
}

// Photo & Video API:
function saveScreenToPng(filename,x,y,width,height,callback)
{
	___kp_executeURL("kioskpro://saveScreenToPng&" + encodeURIComponent(filename) + "?" + encodeURIComponent(x) + "?" + encodeURIComponent(y) + "?" + encodeURIComponent(width) + "?" + encodeURIComponent(height) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['saveScreenToPng'] = function(filename,x,y,width,height,callback) { saveScreenToPng(filename,x,y,width,height,callback); };
}

function kp_PhotoVideo_setCameraType(shouldUseFrontCamera,callback)
{
	___kp_executeURL("kioskpro://kp_PhotoVideo_setCameraType&" + encodeURIComponent(shouldUseFrontCamera) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_PhotoVideo_setCameraType'] = function(shouldUseFrontCamera,callback) { kp_PhotoVideo_setCameraType(shouldUseFrontCamera,callback); };
}

function kp_PhotoVideo_getCameraType(callback)
{
	___kp_executeURL("kioskpro://kp_PhotoVideo_getCameraType&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_PhotoVideo_getCameraType'] = function(callback) { kp_PhotoVideo_getCameraType(callback); };
}

function takePhotoToFile(filename,callback)
{
	___kp_executeURL("kioskpro://takePhotoToFile&" + encodeURIComponent(filename) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['takePhotoToFile'] = function(filename,callback) { takePhotoToFile(filename,callback); };
}

function takePhotoWithCountdownToFile(filename,callback,counter,message,showingTime)
{
	___kp_executeURL("kioskpro://takePhotoWithCountdownToFile&" + encodeURIComponent(filename) + "?" + encodeURIComponent(callback) + "?" + encodeURIComponent(counter) + "?" + encodeURIComponent(message) + "?" + encodeURIComponent(showingTime));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['takePhotoWithCountdownToFile'] = function(filename,callback,counter,message,showingTime) { takePhotoWithCountdownToFile(filename,callback,counter,message,showingTime); };
}

function takeVideoToFile(filename,callback)
{
	___kp_executeURL("kioskpro://takeVideoToFile&" + encodeURIComponent(filename) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['takeVideoToFile'] = function(filename,callback) { takeVideoToFile(filename,callback); };
}

// iMag2 Card Reader API:
function getReaderData(callback)
{
	___kp_executeURL("kioskpro://getReaderData&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['getReaderData'] = function(callback) { getReaderData(callback); };
}

function kp_iMagCardReader_requestSwipe(swipeInfo)
{
	___kp_executeURL("kioskpro://kp_iMagCardReader_requestSwipe&" + encodeURIComponent(swipeInfo));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iMagCardReader_requestSwipe'] = function(swipeInfo) { kp_iMagCardReader_requestSwipe(swipeInfo); };
}

function kp_iMagCardReader_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_iMagCardReader_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iMagCardReader_requestStateOfSupporting'] = function() { kp_iMagCardReader_requestStateOfSupporting(); };
}

function kp_iMagCardReader_requestStateOfConnection()
{
	___kp_executeURL("kioskpro://kp_iMagCardReader_requestStateOfConnection");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iMagCardReader_requestStateOfConnection'] = function() { kp_iMagCardReader_requestStateOfConnection(); };
}

// ZBar Scanner API:
function kp_ZBarScanner_startScan()
{
	___kp_executeURL("kioskpro://kp_ZBarScanner_startScan");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ZBarScanner_startScan'] = function() { kp_ZBarScanner_startScan(); };
}

function kp_ZBarScanner_cancelScan()
{
	___kp_executeURL("kioskpro://kp_ZBarScanner_cancelScan");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ZBarScanner_cancelScan'] = function() { kp_ZBarScanner_cancelScan(); };
}

function kp_ZBarScanner_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_ZBarScanner_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ZBarScanner_requestStateOfSupporting'] = function() { kp_ZBarScanner_requestStateOfSupporting(); };
}

// Bluetooth BarCode Scanner API:
function kp_BluetoothBarcodeScanner_requestAcceptingData(alert_title,alert_message,wait_in_seconds)
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_requestAcceptingData&" + encodeURIComponent(alert_title) + "?" + encodeURIComponent(alert_message) + "?" + encodeURIComponent(wait_in_seconds));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_requestAcceptingData'] = function(alert_title,alert_message,wait_in_seconds) { kp_BluetoothBarcodeScanner_requestAcceptingData(alert_title,alert_message,wait_in_seconds); };
}

function kp_BluetoothBarcodeScanner_requestSilentAcceptingData()
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_requestSilentAcceptingData");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_requestSilentAcceptingData'] = function() { kp_BluetoothBarcodeScanner_requestSilentAcceptingData(); };
}

function kp_BluetoothBarcodeScanner_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_requestStateOfSupporting'] = function() { kp_BluetoothBarcodeScanner_requestStateOfSupporting(); };
}

function kp_BluetoothBarcodeScanner_requestStateOfConnection()
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_requestStateOfConnection");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_requestStateOfConnection'] = function() { kp_BluetoothBarcodeScanner_requestStateOfConnection(); };
}

function kp_BluetoothBarcodeScanner_connect()
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_connect");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_connect'] = function() { kp_BluetoothBarcodeScanner_connect(); };
}

function kp_BluetoothBarcodeScanner_disconnect()
{
	___kp_executeURL("kioskpro://kp_BluetoothBarcodeScanner_disconnect");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_BluetoothBarcodeScanner_disconnect'] = function() { kp_BluetoothBarcodeScanner_disconnect(); };
}

// Common API:
function kp_requestKioskId(callback)
{
	___kp_executeURL("kioskpro://kp_requestKioskId&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_requestKioskId'] = function(callback) { kp_requestKioskId(callback); };
}

// Common Printer API:
function print()
{
	___kp_executeURL("kioskpro://print");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['print'] = function() { print(); };
}

// AirPrinter API:
function kp_AirPrinter_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_AirPrinter_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AirPrinter_requestStateOfSupporting'] = function() { kp_AirPrinter_requestStateOfSupporting(); };
}

function kp_AirPrinter_print()
{
	___kp_executeURL("kioskpro://kp_AirPrinter_print");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AirPrinter_print'] = function() { kp_AirPrinter_print(); };
}

function kp_AirPrinter_printPdf(filename)
{
	___kp_executeURL("kioskpro://kp_AirPrinter_printPdf&" + encodeURIComponent(filename));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AirPrinter_printPdf'] = function(filename) { kp_AirPrinter_printPdf(filename); };
}

// StarPrinter API:
function kp_StarPrinter_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_StarPrinter_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_requestStateOfSupporting'] = function() { kp_StarPrinter_requestStateOfSupporting(); };
}

function kp_StarPrinter_requestStatusOfPrinter()
{
	___kp_executeURL("kioskpro://kp_StarPrinter_requestStatusOfPrinter");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_requestStatusOfPrinter'] = function() { kp_StarPrinter_requestStatusOfPrinter(); };
}

function kp_StarPrinter_printText(text,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printText&" + encodeURIComponent(text) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printText'] = function(text,cut) { kp_StarPrinter_printText(text,cut); };
}

function kp_StarPrinter_printHtml(elementId,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printHtml&" + encodeURIComponent(elementId) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printHtml'] = function(elementId,cut) { kp_StarPrinter_printHtml(elementId,cut); };
}

function kp_StarPrinter_printCode39(text,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printCode39&" + encodeURIComponent(text) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printCode39'] = function(text,cut) { kp_StarPrinter_printCode39(text,cut); };
}

function kp_StarPrinter_printCode93(text,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printCode93&" + encodeURIComponent(text) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printCode93'] = function(text,cut) { kp_StarPrinter_printCode93(text,cut); };
}

function kp_StarPrinter_printCode128(text,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printCode128&" + encodeURIComponent(text) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printCode128'] = function(text,cut) { kp_StarPrinter_printCode128(text,cut); };
}

function kp_StarPrinter_printQRCode(text,cut)
{
	___kp_executeURL("kioskpro://kp_StarPrinter_printQRCode&" + encodeURIComponent(text) + "?" + encodeURIComponent(cut));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_StarPrinter_printQRCode'] = function(text,cut) { kp_StarPrinter_printQRCode(text,cut); };
}

// Memory & Privacy API:
function kp_Browser_clearCookies()
{
	___kp_executeURL("kioskpro://kp_Browser_clearCookies");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_Browser_clearCookies'] = function() { kp_Browser_clearCookies(); };
}

function kp_Browser_clearCache()
{
	___kp_executeURL("kioskpro://kp_Browser_clearCache");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_Browser_clearCache'] = function() { kp_Browser_clearCache(); };
}

// External Screen API:
function kp_ExternalScreen_requestStateOfConnection()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_requestStateOfConnection");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_requestStateOfConnection'] = function() { kp_ExternalScreen_requestStateOfConnection(); };
}

function kp_ExternalScreen_requestProperties(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_requestProperties&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_requestProperties'] = function(callback) { kp_ExternalScreen_requestProperties(callback); };
}

function kp_ExternalScreen_setScreenMode(width,height,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_setScreenMode&" + encodeURIComponent(width) + "?" + encodeURIComponent(height) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_setScreenMode'] = function(width,height,callback) { kp_ExternalScreen_setScreenMode(width,height,callback); };
}

function kp_ExternalScreen_setOverscanCompensationMode(mode,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_setOverscanCompensationMode&" + encodeURIComponent(mode) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_setOverscanCompensationMode'] = function(mode,callback) { kp_ExternalScreen_setOverscanCompensationMode(mode,callback); };
}

function kp_ExternalScreen_connectToScreen()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_connectToScreen");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_connectToScreen'] = function() { kp_ExternalScreen_connectToScreen(); };
}

function kp_ExternalScreen_disconnectFromScreen()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_disconnectFromScreen");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_disconnectFromScreen'] = function() { kp_ExternalScreen_disconnectFromScreen(); };
}

function kp_ExternalScreen_openDocument(filePath,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_openDocument&" + encodeURIComponent(filePath) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_openDocument'] = function(filePath,callback) { kp_ExternalScreen_openDocument(filePath,callback); };
}

function kp_ExternalScreen_setBrowserBgColor(bgColor,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_setBrowserBgColor&" + encodeURIComponent(bgColor) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_setBrowserBgColor'] = function(bgColor,callback) { kp_ExternalScreen_setBrowserBgColor(bgColor,callback); };
}

function kp_ExternalScreen_getBrowserBgColor(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_getBrowserBgColor&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_getBrowserBgColor'] = function(callback) { kp_ExternalScreen_getBrowserBgColor(callback); };
}

function kp_ExternalScreen_doJScript(script)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_doJScript&" + encodeURIComponent(script));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_doJScript'] = function(script) { kp_ExternalScreen_doJScript(script); };
}

function kp_ExternalScreen_setPlayVideoParams(fadeDuration,fadeBgColor,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_setPlayVideoParams&" + encodeURIComponent(fadeDuration) + "?" + encodeURIComponent(fadeBgColor) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_setPlayVideoParams'] = function(fadeDuration,fadeBgColor,callback) { kp_ExternalScreen_setPlayVideoParams(fadeDuration,fadeBgColor,callback); };
}

function kp_ExternalScreen_getPlayVideoParams(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_getPlayVideoParams&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_getPlayVideoParams'] = function(callback) { kp_ExternalScreen_getPlayVideoParams(callback); };
}

function kp_ExternalScreen_playVideo(filePath,loop,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_playVideo&" + encodeURIComponent(filePath) + "?" + encodeURIComponent(loop) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_playVideo'] = function(filePath,loop,callback) { kp_ExternalScreen_playVideo(filePath,loop,callback); };
}

function kp_ExternalScreen_getCurrentVideoPlaybackState(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_getCurrentVideoPlaybackState&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_getCurrentVideoPlaybackState'] = function(callback) { kp_ExternalScreen_getCurrentVideoPlaybackState(callback); };
}

function kp_ExternalScreen_stopVideo()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_stopVideo");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_stopVideo'] = function() { kp_ExternalScreen_stopVideo(); };
}

function kp_ExternalScreen_pauseVideo()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_pauseVideo");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_pauseVideo'] = function() { kp_ExternalScreen_pauseVideo(); };
}

function kp_ExternalScreen_resumeVideo()
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_resumeVideo");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_resumeVideo'] = function() { kp_ExternalScreen_resumeVideo(); };
}

function kp_ExternalScreen_changeCurrentTimeOfVideo(time)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_changeCurrentTimeOfVideo&" + encodeURIComponent(time));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_changeCurrentTimeOfVideo'] = function(time) { kp_ExternalScreen_changeCurrentTimeOfVideo(time); };
}

function kp_ExternalScreen_requestNumberOfPdfPages(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_requestNumberOfPdfPages&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_requestNumberOfPdfPages'] = function(callback) { kp_ExternalScreen_requestNumberOfPdfPages(callback); };
}

function kp_ExternalScreen_showPdfPage(pageNumber,callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_showPdfPage&" + encodeURIComponent(pageNumber) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_showPdfPage'] = function(pageNumber,callback) { kp_ExternalScreen_showPdfPage(pageNumber,callback); };
}

function kp_ExternalScreen_requestNumberOfCurrentPdfPage(callback)
{
	___kp_executeURL("kioskpro://kp_ExternalScreen_requestNumberOfCurrentPdfPage&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_ExternalScreen_requestNumberOfCurrentPdfPage'] = function(callback) { kp_ExternalScreen_requestNumberOfCurrentPdfPage(callback); };
}

// Audio Player API:
function kp_AudioPlayer_play(filePath,atTime,withVolume,repeat)
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_play&" + encodeURIComponent(filePath) + "?" + encodeURIComponent(atTime) + "?" + encodeURIComponent(withVolume) + "?" + encodeURIComponent(repeat));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_play'] = function(filePath,atTime,withVolume,repeat) { kp_AudioPlayer_play(filePath,atTime,withVolume,repeat); };
}

function kp_AudioPlayer_stop()
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_stop");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_stop'] = function() { kp_AudioPlayer_stop(); };
}

function kp_AudioPlayer_pause()
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_pause");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_pause'] = function() { kp_AudioPlayer_pause(); };
}

function kp_AudioPlayer_resume()
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_resume");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_resume'] = function() { kp_AudioPlayer_resume(); };
}

function kp_AudioPlayer_changeVolume(volume)
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_changeVolume&" + encodeURIComponent(volume));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_changeVolume'] = function(volume) { kp_AudioPlayer_changeVolume(volume); };
}

function kp_AudioPlayer_changeCurrentTime(currentTime)
{
	___kp_executeURL("kioskpro://kp_AudioPlayer_changeCurrentTime&" + encodeURIComponent(currentTime));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_AudioPlayer_changeCurrentTime'] = function(currentTime) { kp_AudioPlayer_changeCurrentTime(currentTime); };
}

// Idle Timer API:
function kp_IdleTimer_fire()
{
	___kp_executeURL("kioskpro://kp_IdleTimer_fire");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_IdleTimer_fire'] = function() { kp_IdleTimer_fire(); };
}

// Dropbox API:
function kp_DBXSyncManager_sync()
{
	___kp_executeURL("kioskpro://kp_DBXSyncManager_sync");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_DBXSyncManager_sync'] = function() { kp_DBXSyncManager_sync(); };
}

function kp_DBXSyncManager_stopObservingChangesOfType(typeOfChanges)
{
	___kp_executeURL("kioskpro://kp_DBXSyncManager_stopObservingChangesOfType&" + encodeURIComponent(typeOfChanges));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_DBXSyncManager_stopObservingChangesOfType'] = function(typeOfChanges) { kp_DBXSyncManager_stopObservingChangesOfType(typeOfChanges); };
}

function kp_DBXSyncManager_startObservingChangesOfType(typeOfChanges)
{
	___kp_executeURL("kioskpro://kp_DBXSyncManager_startObservingChangesOfType&" + encodeURIComponent(typeOfChanges));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_DBXSyncManager_startObservingChangesOfType'] = function(typeOfChanges) { kp_DBXSyncManager_startObservingChangesOfType(typeOfChanges); };
}

function kp_DBXSyncManager_getTypeOfObservingChanges(callback)
{
	___kp_executeURL("kioskpro://kp_DBXSyncManager_getTypeOfObservingChanges&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_DBXSyncManager_getTypeOfObservingChanges'] = function(callback) { kp_DBXSyncManager_getTypeOfObservingChanges(callback); };
}

// iDynamo Card Reader API:
function kp_iDynamoCardReader_requestDeviceType()
{
	___kp_executeURL("kioskpro://kp_iDynamoCardReader_requestDeviceType");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iDynamoCardReader_requestDeviceType'] = function() { kp_iDynamoCardReader_requestDeviceType(); };
}

function kp_iDynamoCardReader_requestStateOfConnection()
{
	___kp_executeURL("kioskpro://kp_iDynamoCardReader_requestStateOfConnection");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iDynamoCardReader_requestStateOfConnection'] = function() { kp_iDynamoCardReader_requestStateOfConnection(); };
}

function kp_iDynamoCardReader_requestSwipe(swipeInfo)
{
	___kp_executeURL("kioskpro://kp_iDynamoCardReader_requestSwipe&" + encodeURIComponent(swipeInfo));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iDynamoCardReader_requestSwipe'] = function(swipeInfo) { kp_iDynamoCardReader_requestSwipe(swipeInfo); };
}

function kp_iDynamoCardReader_cancelSwipe()
{
	___kp_executeURL("kioskpro://kp_iDynamoCardReader_cancelSwipe");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iDynamoCardReader_cancelSwipe'] = function() { kp_iDynamoCardReader_cancelSwipe(); };
}

function kp_iDynamoCardReader_mps_doCreditSaleOperation(amount,invoiceNumber)
{
	___kp_executeURL("kioskpro://kp_iDynamoCardReader_mps_doCreditSaleOperation&" + encodeURIComponent(amount) + "?" + encodeURIComponent(invoiceNumber));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_iDynamoCardReader_mps_doCreditSaleOperation'] = function(amount,invoiceNumber) { kp_iDynamoCardReader_mps_doCreditSaleOperation(amount,invoiceNumber); };
}

// UniMag2 Card Reader API:
function kp_UniMag2CardReader_requestStateOfSupporting()
{
	___kp_executeURL("kioskpro://kp_UniMag2CardReader_requestStateOfSupporting");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_UniMag2CardReader_requestStateOfSupporting'] = function() { kp_UniMag2CardReader_requestStateOfSupporting(); };
}

function kp_UniMag2CardReader_requestStateOfConnection()
{
	___kp_executeURL("kioskpro://kp_UniMag2CardReader_requestStateOfConnection");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_UniMag2CardReader_requestStateOfConnection'] = function() { kp_UniMag2CardReader_requestStateOfConnection(); };
}

function kp_UniMag2CardReader_requestSwipe(swipeInfo)
{
	___kp_executeURL("kioskpro://kp_UniMag2CardReader_requestSwipe&" + encodeURIComponent(swipeInfo));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_UniMag2CardReader_requestSwipe'] = function(swipeInfo) { kp_UniMag2CardReader_requestSwipe(swipeInfo); };
}

function kp_UniMag2CardReader_cancelSwipe()
{
	___kp_executeURL("kioskpro://kp_UniMag2CardReader_cancelSwipe");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_UniMag2CardReader_cancelSwipe'] = function() { kp_UniMag2CardReader_cancelSwipe(); };
}

function kp_UniMag2CardReader_mps_doCreditSaleOperation(amount,invoiceNumber)
{
	___kp_executeURL("kioskpro://kp_UniMag2CardReader_mps_doCreditSaleOperation&" + encodeURIComponent(amount) + "?" + encodeURIComponent(invoiceNumber));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_UniMag2CardReader_mps_doCreditSaleOperation'] = function(amount,invoiceNumber) { kp_UniMag2CardReader_mps_doCreditSaleOperation(amount,invoiceNumber); };
}

// MPS API:
function kp_MercuryPaySystem_generateFullReportToFile(fileName,callback)
{
	___kp_executeURL("kioskpro://kp_MercuryPaySystem_generateFullReportToFile&" + encodeURIComponent(fileName) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_MercuryPaySystem_generateFullReportToFile'] = function(fileName,callback) { kp_MercuryPaySystem_generateFullReportToFile(fileName,callback); };
}

function kp_MercuryPaySystem_getSettings()
{
	___kp_executeURL("kioskpro://kp_MercuryPaySystem_getSettings");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_MercuryPaySystem_getSettings'] = function() { kp_MercuryPaySystem_getSettings(); };
}

function kp_MercuryPaySystem_requestLastRegisteredOperation()
{
	___kp_executeURL("kioskpro://kp_MercuryPaySystem_requestLastRegisteredOperation");
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_MercuryPaySystem_requestLastRegisteredOperation'] = function() { kp_MercuryPaySystem_requestLastRegisteredOperation(); };
}

// Custom America Printer API:
function kp_CustomAmericaPrinterAPI_getPageWidth(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getPageWidth&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getPageWidth'] = function(callback) { kp_CustomAmericaPrinterAPI_getPageWidth(callback); };
}

function kp_CustomAmericaPrinterAPI_getFontCharWidth(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontCharWidth&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontCharWidth'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontCharWidth(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontCharWidth(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontCharWidth&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontCharWidth'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontCharWidth(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontCharHeight(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontCharHeight&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontCharHeight'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontCharHeight(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontCharHeight(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontCharHeight&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontCharHeight'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontCharHeight(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontEmphasizedProperty(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontEmphasizedProperty&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontEmphasizedProperty'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontEmphasizedProperty(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontEmphasizedProperty(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontEmphasizedProperty&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontEmphasizedProperty'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontEmphasizedProperty(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontItalicProperty(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontItalicProperty&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontItalicProperty'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontItalicProperty(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontItalicProperty(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontItalicProperty&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontItalicProperty'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontItalicProperty(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontUnderlineProperty(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontUnderlineProperty&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontUnderlineProperty'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontUnderlineProperty(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontUnderlineProperty(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontUnderlineProperty&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontUnderlineProperty'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontUnderlineProperty(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontJustificationProperty(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontJustificationProperty&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontJustificationProperty'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontJustificationProperty(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontJustificationProperty(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontJustificationProperty&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontJustificationProperty'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontJustificationProperty(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getCharFontType(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getCharFontType&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getCharFontType'] = function(callback) { kp_CustomAmericaPrinterAPI_getCharFontType(callback); };
}

function kp_CustomAmericaPrinterAPI_setCharFontType(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setCharFontType&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setCharFontType'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setCharFontType(value,callback); };
}

function kp_CustomAmericaPrinterAPI_getFontInternationalCharSetType(callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_getFontInternationalCharSetType&" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_getFontInternationalCharSetType'] = function(callback) { kp_CustomAmericaPrinterAPI_getFontInternationalCharSetType(callback); };
}

function kp_CustomAmericaPrinterAPI_setFontInternationalCharSetType(value,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_setFontInternationalCharSetType&" + encodeURIComponent(value) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_setFontInternationalCharSetType'] = function(value,callback) { kp_CustomAmericaPrinterAPI_setFontInternationalCharSetType(value,callback); };
}

function kp_CustomAmericaPrinterAPI_printText(text,pixel_la,pixel_w,feed,wordWrap,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_printText&" + encodeURIComponent(text) + "?" + encodeURIComponent(pixel_la) + "?" + encodeURIComponent(pixel_w) + "?" + encodeURIComponent(feed) + "?" + encodeURIComponent(wordWrap) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_printText'] = function(text,pixel_la,pixel_w,feed,wordWrap,callback) { kp_CustomAmericaPrinterAPI_printText(text,pixel_la,pixel_w,feed,wordWrap,callback); };
}

function kp_CustomAmericaPrinterAPI_printHTMLElement(elementId,wordWrap,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_printHTMLElement&" + encodeURIComponent(elementId) + "?" + encodeURIComponent(wordWrap) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_printHTMLElement'] = function(elementId,wordWrap,callback) { kp_CustomAmericaPrinterAPI_printHTMLElement(elementId,wordWrap,callback); };
}

function kp_CustomAmericaPrinterAPI_print2DBarCode(text,type,justification,width,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_print2DBarCode&" + encodeURIComponent(text) + "?" + encodeURIComponent(type) + "?" + encodeURIComponent(justification) + "?" + encodeURIComponent(width) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_print2DBarCode'] = function(text,type,justification,width,callback) { kp_CustomAmericaPrinterAPI_print2DBarCode(text,type,justification,width,callback); };
}

function kp_CustomAmericaPrinterAPI_printBarCode(text,type,hriType,justification,width,height,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_printBarCode&" + encodeURIComponent(text) + "?" + encodeURIComponent(type) + "?" + encodeURIComponent(hriType) + "?" + encodeURIComponent(justification) + "?" + encodeURIComponent(width) + "?" + encodeURIComponent(height) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_printBarCode'] = function(text,type,hriType,justification,width,height,callback) { kp_CustomAmericaPrinterAPI_printBarCode(text,type,hriType,justification,width,height,callback); };
}

function kp_CustomAmericaPrinterAPI_printImage(path,leftAlign,scaleOption,width,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_printImage&" + encodeURIComponent(path) + "?" + encodeURIComponent(leftAlign) + "?" + encodeURIComponent(scaleOption) + "?" + encodeURIComponent(width) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_printImage'] = function(path,leftAlign,scaleOption,width,callback) { kp_CustomAmericaPrinterAPI_printImage(path,leftAlign,scaleOption,width,callback); };
}

function kp_CustomAmericaPrinterAPI_feed(numberOfLFToSend,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_feed&" + encodeURIComponent(numberOfLFToSend) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_feed'] = function(numberOfLFToSend,callback) { kp_CustomAmericaPrinterAPI_feed(numberOfLFToSend,callback); };
}

function kp_CustomAmericaPrinterAPI_cut(cutType,callback)
{
	___kp_executeURL("kioskpro://kp_CustomAmericaPrinterAPI_cut&" + encodeURIComponent(cutType) + "?" + encodeURIComponent(callback));
}

for (_kp_i_ = 0; _kp_i_ < _kp_frames_special_.length; ++_kp_i_)
{
	_kp_frames_special_[_kp_i_].contentWindow['kp_CustomAmericaPrinterAPI_cut'] = function(cutType,callback) { kp_CustomAmericaPrinterAPI_cut(cutType,callback); };
}

