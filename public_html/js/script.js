//var default_content="";
var default_content = {};

$(document).ready(function(){

	if (typeof window.sesskey == 'undefined') { // not logged in
		genRSAkey(function() { // demo
	
			
		});
	}
	
	// handle page directs and defaults
	hash=window.location.hash;
	if(hash=="") {
		jsloadPage('#register');
	} /*else {
		hash = '#' + encodeURIComponent(hash.substring(1));
		window.location.hash = hash;
	} */
	
	checkURL();
	// $('ul li a').click(function (e){

	// 		checkURL(this.hash);

	// });
	
	//filling in the default content
	//default_content = $('#pageContent').html();
	
	
	setInterval("checkURL()",500);
	
});

var lasturl="";

function checkURL(hash)
{
	if(!hash) hash=window.location.hash;
	
	if(hash != lasturl)
	{
		lasturl=hash;
		
		// FIX - if we've used the history buttons to return to the homepage,
		// fill the pageContent with the default_content
		
		if(hash=="") {
			
			//$('#pageContent').html(default_content);
			jsloadPage('#home');
			
		} else {
			
			jsloadPage(hash);
		}
	}
}

function closeChildren(parentid) {
    // parentdiv = "msg60" ex
    //alert(parentid);
    var iter = document.getElementById(parentid).childNodes;
    
    for (var n=0; n < iter.length; n++)
    {
        // iter[n].style.visibility="hidden";
        iter[n].className = "hidden";
    }

}

function offNavSiblings(sibid) {
    // parentdiv = "msg60" ex
    //alert(parentid);
    
    try {
	var parentidNode = document.getElementById(sibid).parentNode;
	var grandparentNode = parentidNode.parentNode;
	//console.log('chat parend id: '+parentid.getAttribute('id'));
	//var parentid = parentidNode.getAttribute('id');
	//var iter = document.getElementById(parentid).childNodes;
	var iter = grandparentNode.childNodes;
	
	//console.log('grandparent: '+grandparentNode);
	//console.log('grandparent innerHTML: '+grandparentNode.innerHTML);
	//console.log('iter: '+iter[0].tagName);
	
	var li = grandparentNode.getElementsByTagName("li");
	for(var n=0; n<li.length; n++) {
		//results[results.length] = li[i];
		//console.log('li: '+li[n])
		
		var nnode = li[n].childNodes;
		nnode[0].className = "navoff";
		//console.log('nnode[0]: '+nnode[0])
	}
	

    } catch(err) {
	//console.log('error offNavSiblings: '+sibid);
	//console.log('error: '+err);
    }
    


}

function setSubNavOn(urlsecond,urlthird) {
	if (urlthird == '' || urlsecond == 'peoplePublic') { // public profiles use NavLink format but have a urlthird
	//if (document.contains(urlsecond+"NavLink")) {
		try {
			document.getElementById(urlsecond+"NavLink").className = "navon";
		} catch(err) {
			
		}
	} else {
		try {
			document.getElementById(urlsecond+urlthird).className = "navon";
		} catch(err) {
			
		}
	}
	
}

function initPage(urlfirst,urlsecond,urlthird)
{

	switch(urlfirst)
	{
		case 'demo':
			//genRSAkey(); // don't load every time clicked
			file_worker_load();
		  break;
		default:

	}
	
	if (urlfirst == 'pmessage') { // && urlsecond == 'pmessageView') {
		
		switch(urlsecond)
		{
			case 'pmessageView':
				ttPMsgView('pmessageView',window.location.hash);
			break;
			case 'pmessageSent':
				displayPMsgSent('pmessageSent');
			break;
			case 'pmessageRead':
				displayPMsgRead('pmessageRead');
			break;
			default:
				
		}
		
	} else if (urlfirst == 'people') {
		
		switch(urlsecond)
		{
			case 'peoplePublic':
				ttPeoplePublic('peoplePublic',urlthird);
			break;
			case 'peopleContacts':
				displayVouches('peopleVouches');
			break;
			default:
		}
	} else if (urlfirst == 'register') {
		
		switch(urlsecond)
		{
			case 'registerFromInvitation':
				ttRegisterFromInvitation('registerFromInvitation',window.location.hash);
			break;
			case 'resetAccount':
				ttResetAccount('resetAccount',window.location.hash);
			break;
			default:
		}
	} else if (urlfirst == 'notification') {
		
		switch(urlsecond)
		{
			default:
				displayNotifications('notificationGeneral');
		}
	} else if (urlfirst == 'group') {
		
		switch(urlsecond)
		{
			case 'groupView':
				if (urlthird != '') {
					showChat(urlthird,true);
				}
			break;
			default:
		}
	}
	
}

function jsloadPage(url)
{
	url=url.replace('#','');
	url=url.split("-");
	
	
	// first level
	var urlfirst = '';
	if (url.length > 0) {
		urlfirst = url[0];
		offNavSiblings(urlfirst+"NavLink");
	}
	
	closeChildren('pageContent');
	
	if ((urlfirst != '') && ($('#'+urlfirst).length > 0)) {
		// url exists
	} else {

		urlfirst = 'home'; //'home';
		window.location.hash = "#";
	}
	
	if (!default_content[urlfirst]) {
		default_content[urlfirst] = '';
	}
	
	document.getElementById(urlfirst).className = "visible";
	try {
		document.getElementById(urlfirst+"NavLink").className = "navon";
	} catch(e) {
		//console.log(e);
		//console.log(urlfirst);
	}
	
	
	// second level
	var urlsecond = '';
	if (url.length > 1) {
		urlsecond = url[1];
		offNavSiblings(urlsecond+"NavLink");
	}
	
	// third level
	var urlthird = '';
	if (url.length > 2) {
		urlthird = url[2];
		offNavSiblings(urlsecond+urlthird);
	}
	
	initPage(urlfirst,urlsecond,urlthird);
	
	closeChildren(urlfirst+'Content');
	
	if ((urlsecond == '') || ($('#'+urlsecond).length == 0)) { // no prechosen second option
		// open defaults
		
		if (default_content[urlfirst] == '') {
			
			var urlsecondset = true;
			
			switch(urlfirst)
			{
				case 'home':
					default_content[urlfirst] = 'homeDefault';
				break;
				case 'demo':
					default_content[urlfirst] = 'demoFiles';
				break;
				case 'about':
					default_content[urlfirst] = 'aboutFirst';
				break;
				case 'branch':
					default_content[urlfirst] = 'branchRoadmap';
				break;
				case 'register':
					default_content[urlfirst] = 'registerForm';	
				break;
				case 'pmessage':
					default_content[urlfirst] = 'pmessageRead';
				break;
				case 'group':
					default_content[urlfirst] = 'groupView';
				break;
				case 'people':
					default_content[urlfirst] = 'peopleContacts';
				break;
				case 'notification':
					default_content[urlfirst] = 'notificationGeneral';
				break;
				default:
					urlsecondset = false;
					
			}
			
			if (urlsecondset) {
				urlsecond = default_content[urlfirst];
				document.getElementById(urlsecond).className = "visible";
				setSubNavOn(urlsecond,urlthird);
			}

		} else { // default_content[urlfirst] is not null
			
			
			urlsecond = default_content[urlfirst];
			//console.log('urlsecond: '+urlsecond);
			document.getElementById(urlsecond).className = "visible";
			setSubNavOn(urlsecond,urlthird);
		}
		
		
		
	} else {
		
		default_content[urlfirst] = urlsecond;
		document.getElementById(urlsecond).className = "visible";
		setSubNavOn(urlsecond,urlthird);
	}
}

function ajaxloadPage(url)
{
	//url=url.replace('#page','');
	
	
	$('#loading').css('visibility','visible');
	
	$.ajax({
		type: "POST",
		url: "load_page.php",
		data: 'page='+url,
		dataType: "html",
		success: function(msg){
			
			if(parseInt(msg)!=0)
			{
				$('#pageContent').html(msg);
				$('#loading').css('visibility','hidden');
			}
		}
		
	});

}





