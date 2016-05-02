function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";

    document.cookie = name+"="+value+expires+"; path=/; domain=."+window.location.host;

}

function getFilenameID() {
    var iPadID = "";
    try {
        iPadID = kioskpro_id.toString();
	 createCookie('store_ipad_id', iPadID, 365);
    }
    catch(e)
    {
        ///alert('Unique iPad ID not entered in Kiosk Pro settings.');
        return "";
    }
    return iPadID;
}


function getCurrentIpadDate(){
	var date = new Date();
	var datetime = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();

    	    date.setTime(date.getTime()+(365*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	var name = "current_ipad_date";
	document.cookie = name+"="+datetime+expires+"; path=/; domain=."+window.location.host;

	return datetime; 
}

getFilenameID();
getCurrentIpadDate();
//document.write(getCurrentIpadDate());
