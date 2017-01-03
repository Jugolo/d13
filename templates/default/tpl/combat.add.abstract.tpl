<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">
	<div class="card card-shadow">

  		<div class="card-header">
  			<div class="left">{{tvar_ui_combat}}</div>
  			<div class="right"><a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>
  		</div>
  
  		<div class="card-content">
    		<div class="card-content-inner">
    
			<form class="pure-form" method="post" id="combatForm" action="?p=combat&action=add&nodeId={{tvar_nodeID}}" id="combatForm">
				
				<div class="swiper-container">
					<div class="swiper-wrapper">
						 {{tvar_unitsHTML}}
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-pagination"></div>
					<div class="swiper-scrollbar"></div>
				</div>
				
				<input type="hidden" name="type" value="{{tvar_type}}">
				{{tvar_ui_target}}: <select name="id">{{tvar_nodeList}}</select> {{tvar_ui_cost}}: {{tvar_costData}} <input class="pure-button" type="submit" value="{{tvar_ui_send}}">
			
			</form>

	 </div>
	</div>
		
		<div class="card-footer">
  			<div class="left">{{tvar_ui_army}} {{tvar_ui_size}}: <span id="size" class="badge">0/0</span></div>
  			
  			
  		</div>

	</div>
</div>