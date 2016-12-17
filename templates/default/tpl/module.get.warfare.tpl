<script type="text/javascript">
	var timerIds=new Array();
</script>

<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">

<div class="card card-shadow">

  <div class="card-header">
  	{{tvar_moduleName}}
  	<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    
    	<div class="row">
    	
			<div class="col-40">
				<div class="d13-module" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
				<img class="d13-module-inner" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_mid}}.png">
				</div>
			</div>
			
			<div class="col-60">
				
				<div class="list-block">
				<ul>
				
				<li class="item-content">
					<div class="item-inner">
					<div class="item-after"><p class="d13-italic">{{tvar_moduleDescription}}</p></div>
					</div>
				</li>
				
				{{tvar_moduleItemContent}}
				
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

 
