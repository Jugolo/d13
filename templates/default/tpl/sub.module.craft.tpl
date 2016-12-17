<div class="swiper-slide">
	
	<div class="card">
		<div class="card-header">
			{{tvar_componentName}}
			<a class="close-popup" href="#"><img class="resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>
  
	<div class="card-content">
    <div class="card-content-inner">
	
	<div class="row">
    	
			<div class="col-25">
				<img src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/components/{{tvar_nodeFaction}}/{{tvar_cid}}.png" width="80">
				<p class="d13-italic">{{tvar_componentDescription}}</p>
			</div>
			
			<div class="col-75">
				
				<div class="list-block">
				
					<ul>
						
						<li class="item-content">
							<div class="item-media"><i class="f7-icons size-16">bookmark</i></div>
							<div class="item-inner">
							<div class="item-title">{{tvar_ui_storage}}</div>
							<div class="item-after"><span class="badge">{{tvar_compValue}}</span></div>
							</div>
						</li>
						
						<li class="item-content">
							<div class="item-media"><i class="f7-icons size-16">time</i></div>
							<div class="item-inner">
							<div class="item-title">{{tvar_ui_duration}}</div>
							<div class="item-after"><span class="badge">{{tvar_duration}}</span></div>
							</div>
						</li>
						
						<li class="item-content">
							<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_compResource}}.png" title="{{tvar_compResourceName}}"></div>
							<div class="item-inner">
							<div class="item-title">{{tvar_ui_storage}} {{tvar_ui_space}}</div>
							<div class="item-after"><span class="badge">{{tvar_compStorage}}</span></div>
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
							<div class="item-media">{{tvar_costIcon}}</div>
							<div class="item-inner">
							<div class="item-title">{{tvar_ui_cost}}:</div>
							<div class="item-after">{{tvar_costData}}</div>
							</div>
						</li>
						
						<li class="item-content">
							<div class="item-inner">
								
								<form class="pure-form" method="post" action="?p=module&action=addComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}">
									
									<select class="pure-input">
										<option value="?p=module&action=addComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}">{{tvar_ui_craft}}</option>
										<option value="?p=module&action=removeComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}">{{tvar_ui_remove}}</option>
									</select>
								
									<input class="pure-input" type="number" size="6" name="quantity" id="quantity{{tvar_cid}}" min="0" max="{{tvar_compLimit}}" value="0">
									<button class="pure-input pure-button pure-{{tvar_global_color}}" type="button" {{tvar_disableData}} onClick="set_maximum('quantity{{tvar_cid}}',{{tvar_compLimit}})">{{tvar_ui_max}}</button>
									<input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" {{tvar_disableData}} value="{{tvar_ui_craft}}">
										
								</form>
								
						</li>
					</ul>
					
				</div>
			</div>
	</div>
	
	
	</div>
	</div>

	</div>
	
</div>