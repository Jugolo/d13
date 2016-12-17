<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">

<div class="card card-shadow">

  <div class="card-header">
  	{{tvar_ui_combat}}
  	<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    
					<script type="text/javascript">
			 var attacker=new Array(), defender=new Array();
			 function Group(groupType, groupQuantity)
			 {
			  this.type=groupType;
			  this.quantity=groupQuantity;
			 }
			</script>

		<div class="swiper-container">
			<div class="swiper-wrapper">
				 {{tvar_unitsHTML}}
			</div>
			<div class="swiper-button-prev"></div>
    		<div class="swiper-button-next"></div>
			<div class="swiper-pagination"></div>
			<div class="swiper-scrollbar"></div>
		</div>
			

	  <form method="post" action="">
	   <div><div class="cell">{{tvar_ui_node}}</div><div class="cell"><select class="dropdown" name="name"">{{tvar_nodeList}}</select></div></div>
	   <div>
		{{tvar_ui_focus}}
		<select class="dropdown" name="attackerFocus" id="attackerFocus">
		 <option value="hp">{{tvar_ui_hp}}</option>
		 <option value="damage">{{tvar_ui_damage}}</option>
		 <option value="armor">{{tvar_ui_armor}}</option>
		</select> | 
		<select class="dropdown" id="attackerUnit">{{tvar_units}}</select>
		<input class="button" type="button" value="{{tvar_ui_add}}" onClick="addGroup()">
	   </div>
	   <div class="container" id="attacker"></div>
	   <div>
		<div class="cell" style="float: right;">{{tvar_ui_cost}}: {{tvar_costData}}</div>
		<input class="button" type="submit" value="{{tvar_ui_send}}">
	   </div>
	  </form>
	 </div>
	</div>

			<script type="text/javascript">
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
			</script>
    
    </div>
  </div>
  </div>
</div>


