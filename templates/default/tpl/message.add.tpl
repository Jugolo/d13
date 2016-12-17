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
    
    <form method="post" action="?p=message&action=add">
     <div>
      <div><div class="cell"><input class="textbox" type="text" name="recipient" value="{{tvar_recipient}}" style="width: 200px;"></div><div class="cell">{{tvar_ui_recipient}}</div></div>
      <div><div class="cell"><input class="textbox" type="text" name="subject" value="{{tvar_subject}}" style="width: 200px;"></div><div class="cell">{{tvar_ui_subject}}</div></div>
     </div>
     <div><textarea class="textbox" name="body" style="width: 400px; height: 200px;">{{tvar_body}}</textarea></div>
     <div><input class="button" type="submit" value="{{tvar_ui_send}}"></div>
    </form>
    	
    </div>
  </div>
</div>
</div>