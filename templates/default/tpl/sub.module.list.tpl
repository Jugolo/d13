<div class="swiper-slide">

	
	<div class="card">

	  <div class="card-header">
		<div class="d13-heading">{{tvar_moduleName}}</div>
		<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	  </div>
  
	  <div class="card-content">
		<div class="card-content-inner">
	
			<div class="row">
			
				<div class="col-40">
					<div class="d13-module" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
					<img class="d13-module-inner" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_moduleImage}}">
					</div>
					<p class="d13-italic">{{tvar_moduleDescription}}</p>
					{{tvar_linkData}}
				</div>

				<div class="col-60">
				
					<div class="list-block">
					<ul>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_input}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleInputName}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_ratio}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleRatio}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_maxInput}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleMaxInput}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_maxInstances}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleMaxInstances}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_add}} {{tvar_ui_duration}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleDuration}} {{tvar_ui_minutes}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_salvage}} {{tvar_ui_cost}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleSalvage}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_removeDuration}}</div>
						<div class="item-after"><span class="badge">{{tvar_moduleRemoveDuration}} {{tvar_ui_minutes}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media">{{tvar_costIcon}}</div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_cost}}</div>
						<div class="item-after">{{tvar_costData}}</div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media">{{tvar_requirementsIcon}}</div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_requirements}}:</div>
						<div class="item-after">{{tvar_requirementsData}}</div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_output}}:</div>
						<div class="item-after">{{tvar_outputData}}</div>
						</div>
					</li>

					</ul>
					</div>
				</div>
			</div>
	
		</div>
	  </div>
  
	</div>

</div>