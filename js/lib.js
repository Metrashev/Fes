function getParentFormElement(elem) {
  form_obj = elem;
  while (form_obj.tagName!='FORM') {
    form_obj = form_obj.parentNode;
    if (!form_obj) {
      alert('Form not found! Please put the list control in a form!'); return 0;
    }
  }
  return form_obj;
}

function getForm(elem) {
	return getParentFormElement(elem);
}

function ADivButton(d){
  var col = d.getElementsByTagName('A');
  if(col.length>0){
    col[0].click();
  }
}


function fixIELabel(){
	var oLabs = document.getElementsByTagName('label');
	for(var i=0; i<oLabs.length; i++){
		var oLab = oLabs[i];
		
		if(!oLab.htmlFor || oLab.onclick) continue;
		var oSel = document.getElementById(oLab.htmlFor);
		if(!oSel) continue;
		if(oSel.tagName=='SELECT'){
			oLab.htmlFor = null;
			oLab.onclick = function(){
				oSel.focus();
			}
		}
		
	}

}


/* COOKIES   */

function SetCookie(sName, sValue)
{
  date = new Date();
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
}

function DelCookie(sName)
{
  var sValue='block';
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

function GetCookie(sName)
{
  var aCookie = document.cookie.split("; ");
  for (var i=0; i < aCookie.length; i++)
  {
    var aCrumb = aCookie[i].split("=");
    if (sName == aCrumb[0]) 
      return unescape(aCrumb[1]);
  }
  return null;
}

/* END COOKIES  */

function getBoundingBox(el) {
	var box = {top:0, left:0, right:0, bottom:0, height:0, width:0}

	if (document.getBoxObjectFor) {
		var r = document.getBoxObjectFor(el);
		box = {top:r.y - document.body.scrollTop, left:r.x - document.body.scrollLeft, right:r.x - document.body.scrollLeft + r.width, bottom:r.y - document.body.scrollTop + r.height, height:r.height, width:r.width};
	} else if (el.getBoundingClientRect) {
		var r = el.getBoundingClientRect();
		box = {top:r.top, left:r.left, right:r.right, bottom:r.bottom, height:r.bottom-r.top, width:r.right-r.left};
	}

	return box;
}

function getPrintLink(){
	var url = window.location.protocol +'//'+ window.location.hostname +  window.location.pathname;
	
	if(window.location.search){
		url += window.location.search + '&print=on';
	} else {
		url += '?print=on';
	}
	url += window.location.hash;
	return url;
}


/*  za kartata na sofiq */
function zoomInSofiaMap(lng) {
	var img = document.getElementById('map_sofia');
	img.src = '/i/map_office_'+lng+'.png';
	
}

function zoomOutSofiaMap(lng) {
	var img = document.getElementById('map_sofia');
	img.src = '/i/map_sofia_	'+lng+'.png';
	
}