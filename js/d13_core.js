/* -- D13 JS -------------------------------------------------------------------*/

function showValue(id, newValue)
{
	document.getElementById(id).innerHTML=newValue;
}

function set_maximum(id, value) {
    document.getElementById(id).value = value;
    document.getElementById(id).max = value;
}

function change_maximum(id, value, form, formvalue) {
    set_maximum(id, value);
    document.getElementById(form).action= formvalue;
}

function armyValue(id, newValue)
{

	document.getElementById(id).innerHTML=newValue;
	document.getElementById('attackerAmount_'+id).value=newValue;
	
	var fuelFactor		= document.getElementById('fuelFactor').value;
	
	var grandAmount		= 0;
	var totalAmount 	= 0;
	var totalDamage 	= 0;
	var totalSpeed 		= 0;
	var totalStealth 	= 0;
	var totalFuel 		= 0;
	
	var unitAmount 		= document.getElementsByName('attackerAmount[]');
	var unitDamage 		= document.getElementsByName('attackerDamage[]');
	var unitSpeed 		= document.getElementsByName('attackerSpeed[]');
	var unitStealth 	= document.getElementsByName('attackerStealth[]');
	var unitFuel 		= document.getElementsByName('attackerFuel[]');
	
	for(key=0; key < unitDamage.length; key++)  {
		totalAmount = parseInt(unitAmount[key].value);
    	totalDamage += parseInt(unitDamage[key].value) * totalAmount;
    	
	}
	
	for(key=0; key < unitSpeed.length; key++)  {
		totalAmount = parseInt(unitAmount[key].value);
    	totalSpeed += parseInt(unitSpeed[key].value) * totalAmount;
	}
	
	for(key=0; key < unitStealth.length; key++)  {
		totalAmount = parseInt(unitAmount[key].value);
    	totalStealth += parseInt(unitStealth[key].value) * totalAmount;
	}

	for(key=0; key < unitFuel.length; key++)  {
		totalAmount = parseInt(unitAmount[key].value);
    	totalFuel += parseInt(unitFuel[key].value) * totalAmount * fuelFactor;
    	grandAmount += totalAmount;
	}
	
	
	
	document.getElementById('totalAmount').innerHTML = grandAmount;
	document.getElementById('totalDamage').innerHTML = totalDamage;
	document.getElementById('totalSpeed').innerHTML = totalSpeed;
	document.getElementById('totalStealth').innerHTML = totalStealth;
	document.getElementById('totalFuel').innerHTML = totalFuel;

}

function moreLess(divContentID, aMoreLessID)
{
	
	var divCont = document.getElementById(divContentID);
	var aMoreLess = document.getElementById(aMoreLessID);

	if(divCont.style.overflow == 'hidden')
	{
		divCont.style.overflow = 'visible';
		divCont.style.height = 'auto';	
	}
	else
	{
		divCont.style.overflow = 'hidden';
		divCont.style.height = '0px';
	}
}

/* -- D13 JS -------------------------------------------------------------------*/

function timedJump(objectId, url)
{
	object=document.getElementById(objectId);
	var time=(object.innerHTML).split(":");
 	var done=0;
	if (time[2] > 0) time[2]--;
	else
	{
		time[2]=59;
		if (time[1] > 0) time[1]--;
		else
		{
			time[1]=59;
			if (time[0] > 0) time[0]--;
			else
   			{
    			clearTimeout(timerIds[objectId]);
   				window.location.href=url;
   				done=1;
   			}
		}
	}
	if (!done)
	{
  	if (String(time[1]).length==1) time[1]="0"+String(time[1]);
  	if (String(time[2]).length==1) time[2]="0"+String(time[2]);
		object.innerHTML=time[0]+":"+time[1]+":"+time[2];
		timerIds[objectId]=setTimeout("timedJump('"+objectId+"', '"+url+"')", 1000);
	}
}

function isset(variable)
{
 if ((typeof(variable)!="undefined")&&(variable!==null)) return true;
 else return false;
}
function indexOfSelectValue(object, value)
{
 var index=-1;
 for (var i=0, done=false; ((i<object.length)&&(!done)); i++)
  if (object.options[i].value==value)
  {
   index=i;
   done=true;
  }
 return index;
}



function jumpToSector()
{
 var x=document.getElementById("x").value, y=document.getElementById("y").value;
 fetch("/server/index.php?p=getGrid", "x="+x+"&y="+y);
}
function setSectorData(descriptionText, playerText, allianceText)
{
 var description=document.getElementById("description"), player=document.getElementById("player"), alliance=document.getElementById("alliance");
 description.innerHTML=descriptionText; player.innerHTML=playerText; alliance.innerHTML=allianceText;
}
function fetch(link, vars)
{
 var xmlHttp;
 try
 {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
 }
 catch (e)
 {
  // Internet Explorer
  try
  {
   xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
  catch (e)
  {
   try
   {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   catch (e)
   {
    alert("Your browser does not support AJAX.");
    return false;
   }
  }
 }
 contentData=document.getElementById("content");
 contentData.innerHTML="<img src=\"templates/loading.gif\">"+contentData.innerHTML;
 xmlHttp.onreadystatechange=function()
 {
  if(xmlHttp.readyState==4)
  {
   contentData.innerHTML="";
   data=xmlHttp.responseText;
   if (data.indexOf("<script type='text/javascript'>")>-1)
    for (start=data.indexOf("<script type='text/javascript'>"); start>-1; start=data.indexOf("<script type='text/javascript'>"))
    {
     end=data.indexOf("</script>");
     script=document.createElement("script");
     script.type="text/javascript";
     script.text=data.substring(start+31, end-1);
     contentData.innerHTML+=data.substring(0, start-1);
     contentData.appendChild(script);
     if (data.length>end+9) data=data.substring(end+9);
     else data="";
    }
   contentData.innerHTML+=data;
  }
 }
 if (vars)
 {
  xmlHttp.open("POST", link, true);
  xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
  xmlHttp.send(vars);
 }
 else
 {
  xmlHttp.open("GET", link, true);
  xmlHttp.send(null);
 }
}

var position=new Array(0, 0);
   function setSector(x, y)
   {
    var sector;
    sector=document.getElementById("sector_"+(position[0]-3)+"_"+(position[1]-3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]-3)+"_"+(position[1]+3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]+3)+"_"+(position[1]+3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]+3)+"_"+(position[1]-3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(x-3)+"_"+(y-3));
    if (isset(sector))
    {
     sector.style.borderLeft="1px solid white";
     sector.style.borderBottom="1px solid white";
    }
    sector=document.getElementById("sector_"+(x-3)+"_"+(y+3));
    if (isset(sector))
    {
     sector.style.borderLeft="1px solid white";
     sector.style.borderTop="1px solid white";
    }
    sector=document.getElementById("sector_"+(x+3)+"_"+(y+3));
    if (isset(sector))
    {
     sector.style.borderRight="1px solid white";
     sector.style.borderTop="1px solid white";
    }
    sector=document.getElementById("sector_"+(x+3)+"_"+(y-3));
    if (isset(sector))
    {
     sector.style.borderRight="1px solid white";
     sector.style.borderBottom="1px solid white";
    }
    position=new Array(x, y);
   }
