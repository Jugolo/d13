<div class="swiper-slide-list">
	
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
			<div><span class="badge" id="sliderRangeTrain{{tvar_unitId}}">0</span><input type="range" name="attackerGroups[]" id="quantity{{tvar_unitId}}" min="0" max="{{tvar_unitAmount}}" value="0" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" onInput="showValue('sliderRange{{tvar_unitId}}', this.value)" onInput="showValue('sliderRangeTrain{{tvar_unitId}}', this.value)" onChange="showValue('sliderRangeTrain{{tvar_unitId}}', this.value)"></div>
			<input type="hidden" name="attackerGroupUnitIds[]" value="{{tvar_unitId}}">			
    	</div>
    	
	</div> 
	
    
</div>  