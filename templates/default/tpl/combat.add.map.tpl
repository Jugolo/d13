<script type="text/javascript">
 var attacker=new Array(), defender=new Array();
 function Group(groupType, groupQuantity)
 {
  this.type=groupType;
  this.quantity=groupQuantity;
 }
</script>

{{tvar_unitsHTML}}

  <form method="post" action="">
   <div><div class="cell">{{tvar_ui_node}}</div><div class="cell"><input class="textbox" type="text" name="name"></div></div>
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
   <div style="text-align: left; border-top: 1px solid black; margin-top: 5px; padding-top: 5px;">
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