{{tvar_tpl_allianceMenu}}

<form method="post" class="pure-form" action="?p=alliance&action=add">
	<div><div class="cell">{{tvar_ui_node}}</div><div class="cell"><select class="dropdown" name="nodeId">{{tvar_nodeList}}</select></div></div>
	<div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><input class="textbox" type="text" name="name" value=""></div></div>
	<div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_add}}"></div></div>
</form>
<div>{{tvar_ui_cost}}: {{tvar_costData}}</div>
<hr>
<a class="external" href="?p=node&action=get">{{tvar_nodeName}}</a></div>