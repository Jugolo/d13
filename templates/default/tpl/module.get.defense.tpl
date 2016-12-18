<script type="text/javascript">
	var timerIds=new Array();
</script>

<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">

<div class="card card-shadow">

  <div class="card-header">
  	<div class="d13-heading">{{tvar_moduleName}}</div>
  	<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    
    	<div class="row">
    	
			<div class="col-40">
				<div class="d13-module" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
				<img class="d13-module-inner" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_mid}}.png">
				</div>
				<p class="d13-italic">{{tvar_moduleDescription}}</p>
			</div>
			
			<div class="col-60">
				
				<div class="list-block">
				<ul>
								
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title label">{{tvar_ui_maxInput}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleMaxInput}}</span></div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-inner">
					<div class="item-after">--combat stats go here--</div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
						<div class="item-title label">{{tvar_moduleInputName}}</div>
						
						<form class="pure-form" method="post" action="?p=module&action=set&nodeId={{tvar_nodeID}}&slotId={{tvar_slotID}}">
							<input class="pure-input" type="number" size="6" name="input" id="workers" min="0" max="{{tvar_moduleInputLimit}}" autocomplete="off" placeholder="{{tvar_moduleSlotInput}}" value="{{tvar_moduleSlotInput}}">
							<button class="pure-input pure-button pure-{{tvar_global_color}}" type="button" onClick="set_maximum('workers',{{tvar_moduleInputLimit}})">{{tvar_ui_max}}</button>
							<input class="pure-input pure-button pure-{{tvar_global_color}}" type="submit" value="{{tvar_ui_set}}">
						</form>
						
					</div>
				</li>
				
				

				<li class="item-content">
					<div class="item-inner">
					<div class="item-after">{{tvar_demolishLink}}</div>
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

 
