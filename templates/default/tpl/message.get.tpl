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
    
    	 	<div><div class="cell">{{tvar_ui_sender}}</div><div class="cell">{{tvar_senderName}}</div></div>
		<div><div class="cell">{{tvar_ui_subject}}</div><div class="cell">{{tvar_subject}}</div></div>
		</div>
		<div style="border-bottom: 1px solid black; padding-bottom: 5px; margin-bottom: 5px;">{{tvar_body}}</div>
		<div><a class="external" href="?p=message&action=add&messageId={{tvar_id}}">{{tvar_ui_reply}}</a></div>
    	
    </div>
  </div>
</div>
</div>