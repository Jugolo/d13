<div class="swiper-slide swiper-slide-list">
	
	<div class="card tiny-card">
	
    	<div class="card-header">
    		<div class="left">{{tvar_unitName}} ({{tvar_unitAmount}})</div>
    		<div class="right"><a href="#" class="open-popup" data-popup=".popup-unit-{{tvar_unitId}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/information.png"></a></div>
    	</div>
    	
    	<div class="card-content">
       		<div class="card-content-inner">
       			<img class="d13-unit" src="templates/{{tvar_global_template}}/images/units/{{tvar_nodeFaction}}/{{tvar_unitId}}.png" title="{{tvar_unitName}}">
    		</div>
    	</div>
    	
    	<div class="card-footer">
			<div><span class="badge" id="sliderRange{{tvar_unitId}}">0</span><input type="range" name="attackerGroups[]" id="quantity{{tvar_unitId}}" min="0" max="{{tvar_unitAmount}}" value="0" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" onInput="armyValue('sliderRange{{tvar_unitId}}', this.value)" onInput="armyValue('sliderRange{{tvar_unitId}}', this.value)" onChange="armyValue('sliderRange{{tvar_unitId}}', this.value, {{tvar_unitId}})"></div>
			<input type="hidden" name="attackerGroupUnitIds[]" value="{{tvar_unitId}}">
			
			<input type="hidden" id="attackerAmount_sliderRange{{tvar_unitId}}" name="attackerAmount[]" value="0">
			<input type="hidden" name="attackerDamage[]" value="{{tvar_unitDamage}}">
			<input type="hidden" name="attackerSpeed[]" value="{{tvar_unitSpeed}}">
			<input type="hidden" name="attackerStealth[]" value="{{tvar_unitStealth}}">
			<input type="hidden" name="attackerFuel[]" value="{{tvar_unitFuel}}">
			
    	</div>
    	
	</div> 
	
    
</div>  