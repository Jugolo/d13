<div class="swiper-slide">
	<div class="card">
		<div class="card-header">
			<div class="d13-heading">
				{{tvar_componentName}}
			</div><a class="close-popup" href="#"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
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
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_compResource}}.png" title="{{tvar_compResourceName}}"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_stored}}
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_compValue}}</span>
										</div>
									</div>
								</li>
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_duration}}
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_duration}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_compResource}}.png" title="{{tvar_compResourceName}}"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_storage}} {{tvar_ui_space}}
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_compStorage}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media">
										{{tvar_requirementsIcon}}
									</div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_requirements}}:
										</div>
										<div class="item-after">
											{{tvar_requirementsData}}
										</div>
									</div>
								</li>
								<li class="item-content">
									<div class="item-media">
										{{tvar_costIcon}}
									</div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_cost}}:
										</div>
										<div class="item-after">
											{{tvar_costData}}
										</div>
									</div>
								</li>
								
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<form action="?p=module&action=addComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}" class="pure-form" id="componentForm" method="post" name="componentForm">
				<select class="pure-input" onchange="change_maximum('input{{tvar_sliderID}}', {{tvar_compValue}}, 'componentForm', this.value)">
					<option value="?p=module&action=addComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}">{{tvar_ui_craft}}</option>
					<option value="?p=module&action=removeComponent&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&componentId={{tvar_cid}}">{{tvar_ui_remove}}</option>
				</select>
				<span class="badge" id="sliderRangeCraft{{tvar_sliderID}}">{{tvar_sliderValue}}</span><input id="input{{tvar_sliderID}}" max="{{tvar_sliderMax}}" min="{{tvar_sliderMin}}" name="quantity" onchange="showValue('sliderRangeCraft{{tvar_sliderID}}', this.value)" oninput="showValue('sliderRangeCraft{{tvar_sliderID}}', this.value)" onmousedown="mySwiper.lockSwipes()" onmouseup="mySwiper.unlockSwipes()" step="1" type="range" value="{{tvar_sliderValue}}"><input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" value="{{tvar_ui_set}}">
			</form>
		</div>
	</div>
</div>