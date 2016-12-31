<div class="swiper-slide">
	
	
	<div class="card">
	
    	<div class="card-header">
    	<div class="left">{{tvar_unitName}} ({{tvar_unitAmount}})</div>
    	<div class="right"><a href="#" class="open-popup" data-popup=".popup-info"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/information.png"></a>
    	</div>
    	</div>
    	
    	<div class="card-content">
       		<div class="card-content-inner"><img src="templates/{{tvar_global_template}}/images/units/{{tvar_nodeFaction}}/{{tvar_unitId}}.png" title="{{tvar_unitName}}" width="80"></div>
    	</div>
    	
    	<div class="card-footer">
    	    <input class="pure-input" type="number" size="6" name="attackerGroups[]" id="quantity{{tvar_unitId}}" min="0" max="{{tvar_unitAmount}}" value="0">
			<input type="hidden" name="attackerGroupUnitIds[]" value="{{tvar_unitId}}">
			<button class="pure-input pure-button pure-{{tvar_global_color}}" type="button" {{tvar_disableData}} onClick="set_maximum('quantity{{tvar_unitId}}',{{tvar_unitAmount}})">{{tvar_ui_max}}</button>
    	</div>
    	
	</div> 
	
    
</div>  