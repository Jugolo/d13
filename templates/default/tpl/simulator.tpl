
<script type="text/javascript">
 var attacker=new Array(), defender=new Array();
 function Group(groupType, groupQuantity)
 {
  this.type=groupType;
  this.quantity=groupQuantity;
 }
</script>

<script type="text/javascript">
{{tvar_attackerArray}}
{{tvar_defenderArray}}
{{tvar_units}}
</script>


 <div class="container">
  <div class="content" style="text-align: center;">
   <form method="post" action="">
    <div style="border-bottom: 1px solid black; margin-bottom: 5px; padding-bottom: 5px;">
     {{tvar_ui_focus}}
     <select class="dropdown" name="attackerFocus" id="attackerFocus">
      <option value="hp">{{tvar_ui_hp}}</option>
      <option value="damage">{{tvar_ui_damage}}</option>
      <option value="armor">{{tvar_ui_armor}}</option>
     </select> | 
     <select class="dropdown" name="attackerFaction" id="attackerFaction" onChange="setFaction(this.value, 'attacker')">{{tvar_factions}}</select>
     <span id="attackerUnitSpan"><select class="dropdown" id="attackerUnit"></select></span>
     <input class="button" type="button" value="{{tvar_ui_add}}" onClick="addGroup('attacker')">
    </div>
    <div class="container" id="attacker"></div>
    <div class="container"{{tvar_showOutput}}>{{tvar_attacker_output}}</div>
    <div class="container"{{tvar_showOutput}}>{{tvar_attacker_outcome}}</div>
    <div style="text-align: center;"><input class="button" type="submit" value="{{tvar_ui_versus}}"></div>
    <div class="container"{{tvar_showOutput}}>{{tvar_defender_outcome}}'</div>
    <div class="container"{{tvar_showOutput}}>{{tvar_defender_output}}</div>
    <div class="container" id="defender"></div>
    <div style="border-top: 1px solid black; margin-top: 5px; padding-top: 5px;">
     {{tvar_ui_focus}}
     <select class="dropdown" name="defenderFocus" id="defenderFocus">
      <option value="hp">{{tvar_ui_hp}}</option>
      <option value="damage">{{tvar_ui_damage}}</option>
      <option value="armor">{{tvar_ui_armor}}</option>
     </select> | 
     <select class="dropdown" name="defenderFaction" id="defenderFaction" onChange="setFaction(this.value, 'defender')">{{tvar_factions}}</select>
     <span id="defenderUnitSpan"><select class="dropdown" id="defenderUnit"></select></span>
     <input class="button" type="button" value="{{tvar_ui_add}}" onClick="addGroup('defender')">
    </div>
   </form>
  </div>
 </div>
 
<script type="text/javascript">
 function addGroup(prefix)
 {
  var group=new Group(document.getElementById(prefix+"Unit").value, 0);
  if (prefix=="attacker") attacker.push(group);
  else defender.push(group);
  setUnits(prefix);
 }
 function removeGroup(prefix, index)
 {
  if (prefix=="attacker") attacker.splice(index, 1);
  else defender.splice(index, 1);
  setUnits(prefix);
 }
 function setFaction(faction, prefix)
 {
  document.getElementById(prefix+"UnitSpan").innerHTML="<select class='dropdown' id='"+prefix+"Unit'>"+units[faction]+"</select>";
 }
 function setUnits(prefix)
 {
  var holder="", faction=document.getElementById(prefix+"Faction");
  if (prefix=="attacker")
  {
   for (var i=0; i<attacker.length; i++)
    holder+="<div class='cell' style='text-align: center;'><div class='row'><a class='link' href='javascript: removeGroup(\"attacker\", "+i+")'>x</a></div><div class='row'><img src='{{tvar_global_directory}}templates/{{tvar_global_template}}/images/units/"+faction.value+"/"+attacker[i].type+".png' class='unitBlock'></div><div class='row'><input type='hidden' name='"+prefix+"GroupUnitIds[]' value='"+attacker[i].type+"'><input class='textbox numeric' type='text' name='"+prefix+"Groups[]' value='"+attacker[i].quantity+"' onChange='attacker["+i+"].quantity=this.value' style='width: 30px;'></div></div>";
  }
  else
  {
   for (var i=0; i<defender.length; i++)
    holder+="<div class='cell' style='text-align: center;'><div class='row'><input type='hidden' name='"+prefix+"GroupUnitIds[]' value='"+defender[i].type+"'><input class='textbox numeric' type='text' name='"+prefix+"Groups[]' value='"+defender[i].quantity+"' onChange='defender["+i+"].quantity=this.value' style='width: 30px;'></div><div class='row'><img src='{{tvar_global_directory}}templates/{{tvar_global_template}}/images/units/"+faction.value+"/"+defender[i].type+".png' class='unitBlock'></div><div class='row'><a class='link' href='javascript: removeGroup(\"defender\", "+i+")'>x</a></div></div>";
  }
  document.getElementById(prefix).innerHTML=holder;
 }
 setFaction(0, "attacker");
 setFaction(0, "defender");
 setUnits("attacker");
 setUnits("defender");
 {{tvar_focusData}}
</script>
