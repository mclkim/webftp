<?php /* Template_ 2.2.8 2015/12/16 18:07:53 D:\phpdev\workspace\Kaiser\public\_template\bootstrap\rename.tpl.html 000002107 */ ?>
<div id="form-content">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>Rename</h3>
	</div>
	<div class="modal-body">
		<form id="modal-form" class="contact" name="contact" onsubmit="return false;">
			<fieldset>
				<label class="control-label">Rename:</label> <input type="text"
					class="form-control" name="rename" placeholder="Rename">
			</fieldset>
		</form>
	</div>
	<div class="modal-footer">
		<input class="btn btn-primary" type="submit" value="Rename"
			id="submit" data-loading-text="Processing..."> <a href="#" class="btn" data-dismiss="modal">Cancel</a>
	</div>
</div>

<script type="text/javascript">
$(document).on("click",'input#submit',function() {

	var dir = $("#dir").val();
	var path = $("#path").val();
	var name = $.trim($("input[name=rename]").val());

	if (name == "") {
		alert("변경할 항목명을 입력해 주세요. ");
		$("input[name=rename]").focus();
		return false;
	}

	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");
	var folderboxes = [], fileboxes = [];

	$(folderchk).each(function() {
		folderboxes.push($(this).attr('value'));
	});

	$(filechk).each(function() {
		fileboxes.push($(this).attr('value'));
	});
	
	var btn = $(this);
	btn.button('loading');

	var pars = "dir=" + encodeURIComponent(dir) + "&";
	pars += "path=" + encodeURIComponent(path) + "&";
	pars += "folder=" + encodeURIComponent(folderboxes) + "&";
	pars += "file=" + encodeURIComponent(fileboxes) + "&";
	pars += "rename=" + encodeURIComponent(name) + "&";

	$.ajax({
		url : "?qrename.rename", // 
		type : "POST",
		data : pars,
		success : function(res) {
			$("div#renameModal").modal('hide');
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