<?php /* Template_ 2.2.8 2015/12/18 18:59:45 D:\phpdev\workspace\Kaiser\public\_template\bootstrap\move.tpl.html 000002893 */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $TPL_VAR["name"]?> :: <?php echo $TPL_VAR["control"]?></title>
	<!-- 2 load the theme CSS file -->
	<link rel="stylesheet" href="plugins/jstree/dist/themes/default/style.min.css" />	
	
</head>
<body class="nosidebar">
<div id="form-content">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>Move Files</h3>
	</div>
	<div class="modal-body">
		<!-- 3 setup a container element -->
		<div id='container'></div>
	</div>
	<div class="modal-footer">
		<input class="btn btn-primary" type="submit" value="Move Files" id="submit" data-loading-text="Processing...">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
	</div>	
</div>	
<!-- 4 include the jQuery library 
<script src="plugins/jstree/dist/libs/jquery.js"></script>
-->
<!-- 5 include the minified jstree source -->
<script src="plugins/jstree/dist/jstree.min.js"></script>
<script type="text/javascript">
$(function () {		
	// 6 create an instance when the DOM is ready
	$("#container").jstree ({
		'core' : {
			'data' : {
				'url' : '?qmove.json',
				'data' : function (node) {
					return { 'id' : node.id };
				}
			},
		'check_callback' : true,
		'themes' : {
			'theme' : 'classic',
			'dots' : true,
			'icons' : true,
			}
		},
		'plugins' : ['','dnd','contextmenu','wholerow']
		//'plugins' : ['state','dnd','sort','types','contextmenu','unique']
	}); 

	$('input#submit').click(function(){
		
		var dir = $("#dir").val();
		var path = $("#path").val();
		
		var folderchk = $("input[name='folderchk[]']:checked");
		var filechk = $("input[name='filechk[]']:checked");
		var folderboxes = [],fileboxes = [];

		$(folderchk).each(function() {
			folderboxes.push($(this).attr('value'));
		});
		
		$(filechk).each(function() {
			fileboxes.push($(this).attr('value'));
		});
		
		var ref = $('#container').jstree(true),
		sel = ref.get_selected();
	
		if(!sel.length) { return false; }
		
		// alert(ref.get_path(sel,'/'));
		
		var btn = $(this);
		btn.button('loading');
		
		var pars = "dir="+encodeURIComponent(dir)+"&";
			pars += "path="+encodeURIComponent(path)+"&";
			pars += "folder="+encodeURIComponent(folderboxes)+"&";
			pars += "file="+encodeURIComponent(fileboxes)+"&";
			pars += "targetDir="+ref.get_path(sel,'/')+"&";
		
		$.ajax({
			url: "?qmove.move", //
			type: "POST",
			data: pars,
			success: function(res){
				$("div#moveModal").modal('hide');
				document.location.href = '?qfiles&dir='+encodeURIComponent(dir)+'&path='+encodeURIComponent(path);
			},
			error: function(){
				alert("failure");
			}
		});			
	});			
});
</script>

</body>
</html>