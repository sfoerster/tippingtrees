/*
  Paranoid On-Screen Keyboard v2.0 (c) 2011 Lazar Laszlo
  http://lazarsoft.info
*/


PKb = {};

PKb.keys = ["`1234567890-=",
			"qwertyuiop[]\\",
			"asdfghjkl;'",
			"Uzxcvbnm,./",
			"S"
			];
PKb.KEYS=[	"~!@#$%^&*()_+",
			"QWERTYuIOP{}|",
			"AsDFGHJKL:\"",
			"UZXCVBNM<>?",
			"S"
			];
PKb.lCase = true;
PKb.inTime = 0;
PKb.highlight = true;
PKb.paranoid = false;
PKb.lastCell = null;
PKb.input = null;
PKb.onlyclick = true;
PKb.X = 50;
PKb.Y = 50;

PKb.deleteAtCursor = function(myField) {
	if (document.selection) {
		//myField.focus();
		//sel = document.selection.createRange();
		//sel.text = myField.value;
		myField.value = myField.value.substr(0,myField.value.length-1);
	}else
	if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substr(0, startPos-1)
		+ myField.value.substr(startPos);
		myField.selectionStart = startPos-1;
		myField.selectionEnd = endPos-1;
	} 
}

PKb.insertAtCursor = function(myField, myValue) {
	if(myField==null)
		return;
	//IE support
	if (document.selection) {
		//myField.focus();
		//sel = document.selection.createRange();
		//sel.text = myValue;
		myField.value += myValue
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
		} else {
			myField.value += myValue;
		}
}

PKb.hide = function()
{
	if(PKb.lastCell!=null)
	{
		PKb.lastCell.style.color="White";
		PKb.lastCell = null;
	}
}

PKb.shuffle = function(keys)
{
	for(var j=0;j<4;j++)
	{
		var rnd="";
		var k = keys[j];
		var l = k.length;
		for(var i=0;i<l;i++)
		{
			var r = Math.floor(Math.random()*(l-i));
			rnd+=k.substr(r,1);
			k=k.substr(0,r)+k.substr(r+1);
		}
		keys[j] = rnd;
	}
}

PKb.overFunc = function(evt)
{
	var obj=null;
	if(typeof(evt.target) == "undefined")
		obj = evt.srcElement;
	else
		obj = evt.target;
	if(PKb.highlight)
		obj.style.background='gray';
	obj.style.color="Black";
	if(PKb.inTime==0)
	{
		var d=new Date();
		PKb.inTime=d.getTime();
	}
	if(PKb.paranoid)
	{
		if(PKb.lastCell!=null)
			PKb.lastCell.style.color="White";
		PKb.lastCell=obj;
		setTimeout(PKb.hide,500);
	}
};

PKb.press = function(key)
{
	switch(key)
	{
		case "SPACE":
			key=" ";
			break;
		case "CL":
			key=null;
			PKb.shift();
			break;
		default:
			break;
	}
	if(key!=null && PKb.input!=null)
		PKb.insertAtCursor(PKb.input, key);
}

PKb.outFunc = function(evt)
{
	var obj=null;
	if(typeof(evt.target) == "undefined")
		obj = evt.srcElement;
	else
		obj = evt.target;
	obj.style.background='white';
	var d=new Date();
	if((PKb.inTime!=0)&&((d.getTime()-PKb.inTime)>500)&&(!PKb.onlyclick))
	{
		var key=obj.innerHTML;
		PKb.press(key);
	}
	PKb.inTime=0;
};

PKb.click = function(evt)
{
	var obj=null;
	if(typeof(evt.target) == "undefined")
		obj = evt.srcElement;
	else
		obj = evt.target;
	obj.style.background='white';
	PKb.inTime=0;
	var key=obj.innerHTML;
	PKb.press(key)
}
PKb.remove = function(e)
{
	var div = document.getElementById("PKBcontainer");
	if(div)
		div.parentNode.removeChild(div);
};
PKb.reinit = function(sh)
{
	var div = document.getElementById("PKBcontainerKB");
	div.removeChild(document.getElementById("PKBkeyboard"));
	if(!PKb.paranoid)
	{
		PKb.keys=PKb.okeys.slice();
		PKb.KEYS=PKb.oKEYS.slice();
	}
	if(sh || PKb.paranoid)
	{
		PKb.shuffle(PKb.KEYS);
		PKb.shuffle(PKb.keys);
	}
	if(!PKb.lCase)
	{
		PKb.initKeyboard(PKb.KEYS);
	}
	else
		PKb.initKeyboard(PKb.keys);
}

PKb.shift = function()
{
	var div = document.getElementById("PKBcontainerKB");
	div.removeChild(document.getElementById("PKBkeyboard"));
	PKb.lCase=!PKb.lCase;
	if(!PKb.lCase)
	{
		PKb.initKeyboard(PKb.KEYS);
	}
	else
		PKb.initKeyboard(PKb.keys);
}

PKb.initKeyboard = function(keyTable)
{
	var div = document.getElementById("PKBcontainerKB");
    var table = document.createElement("table");
    table.border = 1;
    table.borderColor = "Black";
    table.style.height = "100%";
    table.style.width = "100%";
    table.cellSpacing = 3;
	table.align="center";
	table.id="PKBkeyboard";
    var tmpRow = null;
    var tmpCell = null;
	
    for(var i=0;i<keyTable.length;i++)
    {
        tmpRow = table.insertRow(-1);
		var keyLine = keyTable[i];
        for(var j=0;j<keyLine.length;j++)
        {
            tmpCell = tmpRow.insertCell(-1);
			if ( tmpCell.addEventListener ) {
				tmpCell.addEventListener( "mouseover", PKb.overFunc, false );
			} else if ( tmpCell.attachEvent ) {
				tmpCell.attachEvent( "onmouseover", PKb.overFunc );
			} 
			if ( tmpCell.addEventListener ) {
				tmpCell.addEventListener( "mouseout", PKb.outFunc, false );
			} else if ( tmpCell.attachEvent ) {
				tmpCell.attachEvent( "onmouseout", PKb.outFunc );
			} 
			if ( tmpCell.addEventListener ) {
				tmpCell.addEventListener( "click", PKb.click, false );
			} else if ( tmpCell.attachEvent ) {
				tmpCell.attachEvent( "onclick", PKb.click );
			} 
			tmpCell.setAttribute('align', 'center');
            tmpCell.bgColor="White";
			if(PKb.paranoid)
				tmpCell.style.color="White";
			else
				tmpCell.style.color="Black";
			var key=keyLine.substr(j,1);
			
			if(key=='S')
			{
				key="SPACE";
				tmpCell.colSpan=14;
			}
			if(key=='U')
			{
				key="CL";
				tmpCell.colSpan=2;
			}
			if(!PKb.lCase && key=="u")
				key="U";
			if(!PKb.lCase && key=="s")
				key="S";
            tmpCell.innerHTML=key;
        }
    }
	div.appendChild(table);
}


PKb.create = function(parent)
{
	if(document.getElementById("PKBcontainer"))
		return;
	
	var container = document.createElement("div");
	container.id = "PKBcontainer";
	container.style.width="504px";
	container.style.height="173px";
	container.style.background="white";
	container.style.border="solid 2px black";
	container.style.styleFloat="left";
	container.style.cssFloat="left";

	var containerKb = document.createElement("div");
	containerKb.id = "PKBcontainerKB";
	containerKb.style.width="425px";
	containerKb.style.height="170px";
	containerKb.style.background="#f0f0f0";
	containerKb.style.border="solid 2px black";
	containerKb.style.styleFloat="left";
	containerKb.style.cssFloat="left";

	var containerCtrl = document.createElement("div");
	containerCtrl.id = "PKBcontainerCtrl";
	containerCtrl.style.width="70px";
	containerCtrl.style.height="150px";
	containerCtrl.style.styleFloat="right";
	containerCtrl.style.cssFloat="right";

	var html = '\
	<input type="button" value="<--" style="width:65px;height:20px;margin-top:4px;background:#f0f0f0" onclick="PKb.deleteAtCursor(PKb.input);" /> \
	<input type="button" value="Clear" style="width:65px;height:20px;margin-top:4px;background:#f0f0f0" onclick="if(PKb.input!=null)PKb.input.value=\'\';" /> \
	<input type="button" value="Shuffle" style="width:65px;height:20px;margin-top:4px;background:#f0f0f0" onclick="PKb.reinit(true);" /> \
	<input type="button" value="Paranoid" style="width:65px;height:20px;margin-top:4px;background:#f0f0f0" onclick="PKb.paranoid=!PKb.paranoid;PKb.highlight=!PKb.highlight;PKb.reinit(false);" /> \
	<input type="button" value="Mode" style="width:65px;height:20px;margin-top:4px;background:#f0f0f0" onclick="PKb.onlyclick=!PKb.onlyclick;if(PKb.onlyclick)this.value=\'Click\';else this.value=\'Hover\';" /> \
	';
	
	containerCtrl.innerHTML=html;
	
	container.appendChild(containerKb);
	container.appendChild(containerCtrl);
	parent.appendChild(container);

	PKb.okeys=PKb.keys.slice();
	PKb.oKEYS=PKb.KEYS.slice();
	if(PKb.paranoid)
	{
		PKb.shuffle(PKb.keys);
		PKb.shuffle(PKb.KEYS);
	}
		
	PKb.initKeyboard(PKb.keys);
}

