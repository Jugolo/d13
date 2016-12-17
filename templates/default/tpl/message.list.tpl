<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/nodeBackground.png);">

<div class="card card-shadow">
  <div class="card-header">
  	{{tvar_ui_messages}} {{tvar_ui_list}}
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    
    	 	<div><a class="external" href="?p=message&action=add">{{tvar_ui_send}}</a>{{tvar_removeAll}}</div>
   			<div style="border-top: 1px solid black; border-bottom: 1px solid black; padding: 5px 0 5px 0; margin: 5px 0 5px 0;">
			<form method="post" action="?p=message&action=remove" id="messageList">
			{{tvar_messages}}
			{{tvar_remove}}
			</form>
			</div>
   			{{tvar_controls}}
    	
    </div>
  </div>
</div>
</div>