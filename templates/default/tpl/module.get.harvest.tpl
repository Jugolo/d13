<script type="text/javascript">
	var timerIds=new Array();
</script>

<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">

	<div class="card large-card card-shadow">

		<div class="card-header">
			<div class="d13-heading">{{tvar_name}} {{tvar_levelLabel}}</div>
			<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>

		<div class="card-content">
	<div class="card-content-inner">

		<div class="row">
	
			<div class="col-40">
				<div class="d13-module" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
				<img class="d13-module-inner" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_image}}">
				</div>
				<p class="d13-italic">{{tvar_description}}</p>
				{{tvar_linkData}}
			</div>
		
			<div class="col-60">
			
				<div class="list-block">
				<ul>
			
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_maxInput}}</div>
					<div class="item-after"><span class="badge">{{tvar_base_maxinput}} [+{{tvar_upgrade_maxinput}}]</span></div>
					</div>
				</li>
			
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_ratio}}</div>
					<div class="item-after"><span class="badge">{{tvar_base_Ratio}} [+{{tvar_upgrade_ratio}}]</span></div>
					</div>
				</li>
			
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleOutput0}}.png" title="{{tvar_moduleOutputName0}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_production}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleProduction}} {{tvar_ui_perHour}}</span></div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_moduleInputName}}</div>
				
					<div class="item-after">{{tvar_inputSlider}}</div>
				
					</div>
				</li>
						
				</ul>
				</div>
			</div>
		</div>

	</div>
	</div>

		<div class="card-footer">
			<div class="left">{{tvar_demolishLink}}</div><div class="right">{{tvar_inventoryLink}}</div>
		</div>

	</div>

</div>