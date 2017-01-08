<div class="swiper-slide">
	<div class="card large-card">
		<div class="card-header">
			<div class="d13-heading">
				{{tvar_name}} [{{tvar_Class}}] ({{tvar_unitValue}})
			</div><a class="close-popup" href="#"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>
		<div class="card-content">
			<div class="card-content-inner">
				<div class="row">
					<div class="col-25">
						<img src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/units/{{tvar_nodeFaction}}/{{tvar_image}}" width="80">
						<p class="d13-italic">{{tvar_description}}</p>
					</div>
					<div class="col-75">
						<div class="list-block">
							<ul>

								<li class="item-content">
									<div class="item-inner">
										<div class="item-after">
											{{tvar_ui_attack}}: {{tvar_attackType}}
										</div>
									</div>
									<div class="item-inner">
										<div class="item-after">
											{{tvar_ui_defense}}: {{tvar_armorType}}
										</div>
									</div>
								</li>

								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_duration}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_duration}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_upkeepResource}}.png" title="{{tvar_upkeepResource}}"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_upkeep}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_upkeep}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_hp.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_hp}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitHP}} {{tvar_unitHPPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_damage.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_damage}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitDamage}} {{tvar_unitDamagePlus}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_armor.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_armor}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitArmor}} {{tvar_unitArmorPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_speed.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_speed}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitSpeed}} {{tvar_unitSpeedPlus}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_critical.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_critical}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitCritical}} {{tvar_unitCriticalPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_capacity.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_capacity}}:
										</div>
										<div class="item-after">
											 <span class="badge">{{tvar_unitCapacity}} {{tvar_unitCapacityPlus}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_vision.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_vision}}:
										</div>
										<div class="item-after">
											 <span class="badge">{{tvar_unitVision}} {{tvar_unitVisionPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_capacity.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_stealth}}:
										</div>
										<div class="item-after">
											 <span class="badge">{{tvar_unitStealth}} {{tvar_unitStealthPlus}}</span>
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
			<form action="?p=module&action=addUnit&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&unitId={{tvar_uid}}" class="pure-form" id="unitForm_{{tvar_uid}}" method="post" name="unitForm_{{tvar_uid}}">
				<select class="pure-input" onchange="change_maximum('input{{tvar_sliderID}}', {{tvar_unitValue}}, 'trainForm', this.value)">
					<option value="index.php?p=module&action=addUnit&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&unitId={{tvar_uid}}">{{tvar_ui_train}}</option>
					<option value="index.php?p=module&action=removeUnit&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}&unitId={{tvar_uid}}">{{tvar_ui_remove}}</option>
					</select>
					<span class="badge" id="sliderRangeTrain{{tvar_sliderID}}">{{tvar_sliderValue}}</span><input id="input{{tvar_sliderID}}" max="{{tvar_sliderMax}}" min="{{tvar_sliderMin}}" name="quantity" onchange="showValue('sliderRangeTrain{{tvar_sliderID}}', this.value)" oninput="showValue('sliderRangeTrain{{tvar_sliderID}}', this.value)" onmousedown="mySwiper.lockSwipes()" onmouseup="mySwiper.unlockSwipes()" step="1" type="range" value="{{tvar_sliderValue}}"><input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" value="{{tvar_ui_set}}">
				</form>
		</div>
	</div>
</div>