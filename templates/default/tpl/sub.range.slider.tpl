<div class="right {{tvar_sliderTooltip}}">
<form class="pure-form" method="post" action="{{tvar_formAction}}">
	
		<input type="image" class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/submit.png" type="submit" {{tvar_disableData}} value="{{tvar_ui_set}}">
		<input type="range" name="input" id="input{{tvar_sliderID}}" min="{{tvar_sliderMin}}" max="{{tvar_sliderMax}}" value="{{tvar_sliderValue}}" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" onInput="showValue('sliderRange{{tvar_sliderID}}', this.value)" ondblclick="showMax('input{{tvar_sliderID}}', 'sliderRange{{tvar_sliderID}}')" onChange="showValue('sliderRange{{tvar_sliderID}}', this.value)">
		<span class="badge" id="sliderRange{{tvar_sliderID}}">{{tvar_sliderValue}}</span>
		
</form>
</div>