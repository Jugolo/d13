<div class="popup popup-build-{{tvar_id}}">

<div class="card">
	
	<div class="card-header">
			<div class="d13-heading">{{tvar_title}}</div>
	</div>
	
	<div class="card-content">
	<div class="card-content-inner">

		<div class="list-block">
			<ul>
					
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_duration}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleDuration}} {{tvar_ui_minutes}}</span></div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_max}} {{tvar_moduleInputName}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleLimit}}</span></div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-inner">
					{{tvar_costList}}
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-inner">
							
						<form class="pure-form" method="post" id="buildForm" action="{{tvar_moduleAction}}" id="buildForm_{{tvar_id}}">
							<input  class="pure-input" type="number" size="6" name="input" id="quantity{{tvar_id}}" min="1" max="{{tvar_moduleLimit}}" value="1">
							<button class="pure-input pure-button pure-{{tvar_global_color}}" type="button" {{tvar_disableData}} onClick="set_maximum('quantity{{tvar_id}}',{{tvar_moduleLimit}})">{{tvar_ui_max}}</button>
							<input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" {{tvar_disableData}} value="{{tvar_ui_build}}">
						</form>
						
					</div>
				</li>		

			</ul>
			
		</div>

	</div>
	</div>

</div>

</div>