<?php /* Template_ 2.2.8 2015/12/16 18:07:53 D:\phpdev\workspace\Kaiser\public\_template\bootstrap\zip.tpl.html 000002231 */ ?>
<div id="form-content">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>Zip entries</h3>
	</div>
	<div class="modal-body">
		<form id="modal-form" class="contact" name="contact" onsubmit="return false;">
			<fieldset>
				<label class="control-label">Save the zip file on the server as:</label> <input type="text"
					class="form-control" name="save_filename" id="save_filename"
					placeholder="Zip file name">
			</fieldset>
		</form>
	</div>
	<div class="modal-footer">
		<input class="btn btn-primary" type="submit" value="Zip file"
			id="submit" data-loading-text="Processing..."> 
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
	</div>
</div>

<script type="text/javascript">
$(document).on("click", 'input#submit', function() {	
	var dir = $.trim($("#dir").val());
	var path = $.trim($("#path").val());
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");
	var save_filename = $.trim($("input[name=save_filename]").val());
	var folderboxes = [],fileboxes = [];
	
	$(folderchk).each(function() {
		folderboxes.push($(this).attr('value'));
	});
	
	$(filechk).each(function() {
		fileboxes.push($(this).attr('value'));
	});

	if (save_filename == "") {
		alert("저장할 파일명을 입력해 주세요. ");
		$("input[name=save_filename]").focus();
		return false;
	}

	var btn = $(this);
	btn.button('loading');

	var pars = "dir=" + encodeURIComponent(dir) + "&";
		pars += "path=" + encodeURIComponent(path) + "&";
		pars += "folder="+encodeURIComponent(folderboxes)+"&";
		pars += "file="+encodeURIComponent(fileboxes)+"&";
		pars += "save_filename=" + encodeURIComponent(save_filename) + "&";

	$.ajax({
		url : "?qzip.zip", //
		type : "POST",
		data : pars,
		success : function(res) {
			$("div#zipModal").modal('hide');
			document.location.href = '?qfiles&dir='
					+ encodeURIComponent(dir) + '&path='
					+ encodeURIComponent(path);
		},
		error : function() {
			alert("failure");
		}
	});
});
</script>