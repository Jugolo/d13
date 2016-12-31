<form method="post" action="{{tvar_formAction}}">

	<div class="item-input">
		<div class="">
			<span class="badge" id="sliderRange{{tvar_sliderID}}">{{tvar_sliderValue}}</span><input type="range" name="input" id="input{{tvar_sliderID}}" min="{{tvar_sliderMin}}" max="{{tvar_sliderMax}}" value="{{tvar_sliderValue}}" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" onInput="showValue('sliderRange{{tvar_sliderID}}', this.value)" onChange="showValue('sliderRange{{tvar_sliderID}}', this.value)"><input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" value="{{tvar_ui_set}}">
		</div>
	</div>         

</form>