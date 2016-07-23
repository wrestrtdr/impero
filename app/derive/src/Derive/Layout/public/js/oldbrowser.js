var oldBrowserWarning = new Object();
oldBrowserWarning.getInternetExplorerVersion = function() {
  var rv = -1; // Return value assumes failure.
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}
oldBrowserWarning.isOld = function()
{
  var ver = oldBrowserWarning.getInternetExplorerVersion();

  if ( ver > -1 )
  {
    if ( ver <= 8.0 ) {
    	return true;
    }
  }

  return false;
}


$(document).ready(function(){
  if (oldBrowserWarning.isOld()) {
    cookieHtml = '<div id="oldbrowser" style="position: absolute; top: 0px; z-index: 9998; width: 100%; height: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.6);">' +
    '<span style="text-align: left; color: #fff; line-height: 20px; margin: 8px; display: block;">Uporabljate zastarelo različico brskalnika. Za boljšo uporabniško izkušnjo priporočamo, da posodobite brskalnik ali pa prenesete enega izmed sledečih: ' +
      '<a style="margin: 0px 15px; float: right; color: white; font-weight: bold; line-height: 20px;" href="http://www.opera.com/">Opera</a>' +
      '<a style="margin: 0px 15px; float: right; color: white; font-weight: bold; line-height: 20px;" href="http://www.mozilla.org/sl/firefox/new/">Mozilla Firefox</a>' +
      '<a style="margin: 0px 15px; float: right; color: white; font-weight: bold; line-height: 20px;" href="http://www.google.com/intl/sl/chrome/browser/">Google Chrome</a>' +
    '</span>' +
    '</div>';
    $(".head-main .container").first().append(cookieHtml); // zekomConfirm, zekomReject, zekomMore
  }
});