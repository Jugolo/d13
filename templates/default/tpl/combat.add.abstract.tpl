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
				<select name="id">{{tvar_nodeList}}</select> {{tvar_ui_cost}}: {{tvar_costData}} <input class="pure-button" type="submit" value="{{tvar_ui_send}}">
			
			</form>

	 </div>
	</div>
		
		<div class="card-footer">
  			<div class="left">{{tvar_ui_army}} {{tvar_ui_size}}: <span id="size" class="badge">0/0</span></div>
  			<div class="left">{{tvar_ui_battle}} {{tvar_ui_power}}: <span id="power" class="badge">0</span></div>
  			
  			
  		</div>
		
		<script type="text/javascript">
		/*
				function addGroup()
				{
				 var group=new Group(document.getElementById("attackerUnit").value, 0);
				 attacker.push(group);
				 setUnits();
				}
				function removeGroup(index)
				{
				 attacker.splice(index, 1);
				 setUnits();
				}
				function setUnits()
				{
				 var holder="";
				 if (attacker.length==0) holder="[ ... ]";
				 for (var i=0; i<attacker.length; i++)
					holder+="<div class=\'cell\' style=\'text-align: center;\'><div class=\'row\'><a class=\'link\' href=\'javascript: removeGroup("+i+")\'>x</a></div><div class=\'row\'><img src=\'templates/{{tvar_unitImagePath}}/"+attacker[i].type+".png\' style=\'width: 38px;\'></div><div class=\'row\'><input type=\'hidden\' name=\'attackerGroupUnitIds[]\' value=\'"+attacker[i].type+"\'><input class=\'textbox numeric\' type=\'text\' name=\'attackerGroups[]\' value=\'"+attacker[i].quantity+"\' onChange=\'attacker["+i+"].quantity=this.value\' style=\'width: 30px;\'></div></div>";
					document.getElementById("attacker").innerHTML=holder;
				}
				setUnits();
				*/
			</script>
    	
    	
    	
	</div>
</div>