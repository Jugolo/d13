<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/nodeBackground.png);">

<div class="card large-card card-shadow">

	<div class="card-header">
  		<div class="d13-heading">{{tvar_ui_messages}} {{tvar_ui_list}}</div>
  		<a class="external" href="?p=node&action=list"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	</div>
  
  
	<div class="card-content">
		<div class="card-content-inner">

		<div class="list-block">
			<ul>
					
				
   			
			<form method="post" action="?p=message&action=remove" id="messageList">
			{{tvar_messages}}
			{{tvar_remove}}
			</form>
			
   			{{tvar_controls}}
					
			</ul>
			
		</div>

	</div>
	</div>

	<div class="card-footer">
		<div><a class="external" href="?p=message&action=add">{{tvar_ui_send}}</a>{{tvar_removeAll}}</div>
	</div>
	
</div>