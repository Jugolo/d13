<div id="divContainer" class="{{tvar_winClass}}">
	<div class="cell">{{tvar_winTitle}}</div><div class="cell"><a href="#"  id="anchorMoreLess{{tvar_winId}}" onClick="moreLess('divContent{{tvar_winId}}','anchorMoreLess{{tvar_winId}}'); return false;"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/scrollVertical.png" title=""></a></div>
	<div id="divContent{{tvar_winId}}" style="height:0px; width:100%; overflow:hidden;">
	{{tvar_winContent}}
	</div>
</div>