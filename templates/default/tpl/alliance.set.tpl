{{tvar_tpl_allianceMenu}}
<form method="post" action="index.php?p=alliance&action=set">
<div><div class="cell">{{tvar_ui_node}}</div><div class="cell"><select class="dropdown" name="nodeId">{{tvar_nodeList}}</select></div></div>
<div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><input class="textbox" type="text" name="name" value="{{tvar_allianceName}}"></div></div>
<div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_set}}"></div></div>
</form>
<div>{{tvar_ui_cost}}: {{tvar_costData}}</div>
<div style="border-top: 1px solid black; padding-top: 5px; margin-top: 5px;"><a class="external" href="index.php?p=node&action=get&nodeId={{tvar_nodeID}}">{{tvar_nodeName}}</a></div>';