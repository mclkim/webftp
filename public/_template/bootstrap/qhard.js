// -------------------------------------------------------------------------------------
// 1.업로드
// -------------------------------------------------------------------------------------
$(document).on("click", "#uploadBtn", function() {
	$("#uploadModal").modal({
		remote : '?qupload'
	});
});
// -------------------------------------------------------------------------------------
// 2.다운로드
// -------------------------------------------------------------------------------------
function Download(dir, path, entry) {
	var url = '?qdownload';
	var pars = "&dir=" + encodeURIComponent(dir);
	pars += '&path=' + encodeURIComponent(path);
	pars += '&entry=' + encodeURIComponent(entry);
	document.location.href = url + pars;
}
$(document).on("click", '#downloadBtn', function() {
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");
	var folderboxes = [], fileboxes = [];
	var dir = $("#dir").val();
	var path = $("#path").val();

	if ($(folderchk).length == 0 && $(filechk).length == 0) {
		alert("다운로드할 항목을 선택하여 주세요.!!!\t");
		//alert('Please select at least one directory or file.');
		return;
	} else {
		if (confirm('선택한 파일을 다운로드하시겠습니까?\t')) {

			$(folderchk).each(function() {
				folderboxes.push($(this).attr('value'));
			});

			$(filechk).each(function() {
				fileboxes.push($(this).attr('value'));
			});

			var pars = "dir=" + encodeURIComponent(dir) + "&";
			pars += "path=" + encodeURIComponent(path) + "&";
			pars += "folder=" + encodeURIComponent(folderboxes) + "&";
			pars += "file=" + encodeURIComponent(fileboxes) + "&";

			document.location.href = '?qdownload.zip&' + pars;
		}
	}
});
// -------------------------------------------------------------------------------------
// 3.새폴더 생성
// -------------------------------------------------------------------------------------
$(document).on("click", "#newBtn", function() {
	$("#newModal").modal({
		remote : '?qnew'
	});
});
// -------------------------------------------------------------------------------------
// 5.이름 변경
// -------------------------------------------------------------------------------------
$(document).on("click", '#renameBtn', function() {
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");

	if ($(folderchk).length == 0 && $(filechk).length == 0) {
		alert("변경할 항목을 선택하여 주세요");
		return;
	} else if ($(folderchk).length > 1 || $(filechk).length > 1) {
		alert("변경할 항목 하나만 선택하여 주세요");
		return;
	} else {
		$("#renameModal").modal({
			remote : '?qrename'
		});
	}
});
// -------------------------------------------------------------------------------------
// 6.삭제
// -------------------------------------------------------------------------------------
$(document).on("click", '#removeBtn', function() {
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");
	var folderboxes = [], fileboxes = [];
	var dir = $("#dir").val();
	var path = $("#path").val();

	if ($(folderchk).length == 0 && $(filechk).length == 0) {
		alert("삭제할 항목을 선택하여 주세요");
		return;
	} else {
		if (confirm('선택한 파일을 삭제하시겠습니까?\t')) {

			$(folderchk).each(function() {
				folderboxes.push($(this).attr('value'));
			});

			$(filechk).each(function() {
				fileboxes.push($(this).attr('value'));
			});

			var pars = "dir=" + encodeURIComponent(dir) + "&";
			pars += "path=" + encodeURIComponent(path) + "&";
			pars += "folder=" + encodeURIComponent(folderboxes) + "&";
			pars += "file=" + encodeURIComponent(fileboxes) + "&";

			$.ajax({
				type : "POST",
				url : "?qremove", //
				data : pars,
				success : function(msg) {
					document.location.href = '?qfiles&dir='
							+ encodeURIComponent(dir) + '&path='
							+ encodeURIComponent(path);
				},
				error : function() {
					alert("failure");
				}
			});
		}
	}
});
// -------------------------------------------------------------------------------------
// 8.이동
// -------------------------------------------------------------------------------------
$(document).on("click", '#moveBtn', function() {
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");

	if ($(folderchk).length == 0 && $(filechk).length == 0) {
		alert("이동할 항목을 선택하여 주세요");
		return;
	} else {
		$("#moveModal").modal({
			remote : '?qmove'
		});
	}
});
//-------------------------------------------------------------------------------------
//9.파일검색
//-------------------------------------------------------------------------------------
$(document).on("keydown", "#search", function(e) {
	var dir = $("#dir").val();
	var path = $("#path").val();
	var search = $("#search").val();

	if (e.keyCode == '13') {
		if (search == "") {
			alert("검색파일명을 입력해 주세요.!!!\t");
			$("#search").focus();
			return false;
		}
		//alert($(".form-control").val());
		//alert(dir);alert(path);
		document.location.href = '?qfind&dir='
			+ encodeURIComponent(dir) + '&path='
			+ encodeURIComponent(path) + '&search='
			+ encodeURIComponent(search);
	}
});
//-------------------------------------------------------------------------------------
// 10.압축하기
//-------------------------------------------------------------------------------------
$(document).on("click", '#zipBtn', function() {
	var folderchk = $("input[name='folderchk[]']:checked");
	var filechk = $("input[name='filechk[]']:checked");
	var folderboxes = [], fileboxes = [];
	var dir = $("#dir").val();
	var path = $("#path").val();

	if ($(folderchk).length == 0 && $(filechk).length == 0) {
		alert("압축할 항목을 선택하여 주세요.!!!\t");
	} else {
		$("#zipModal").modal({
			remote : '?qzip'
		});
	}
});
// -------------------------------------------------------------------------------------
// 11.휴지통
// -------------------------------------------------------------------------------------
$(document).on("click", '#trashBtn', function() {
	var dir = $("#dir").val();
	var path = $("#path").val();

	if (confirm('휴지통에 있는 모든 항목을 삭제하시겠습니까?\t')) {
		$.ajax({
			url : "?qtrash.trash", //
			type : "POST",
			data : null,
			success : function(res) {
				res = $.parseJSON(res);
				if (res.code == 1) {
					alert(res.message);
					document.location.href = '?qfiles&dir='
							+ encodeURIComponent(dir) + '&path='
							+ encodeURIComponent(path);
				}
			},
			error : function() {
				alert("failure");
			}
		});
	}
});
// -------------------------------------------------------------------------------------
// 12.짧은 url + qr코드
// -------------------------------------------------------------------------------------
$(document).on('click', '#shortUrlBtn', function(){
//	var current = $("#current").val();
	var fileCheckEle = $("input[name='filechk[]']:checked");
	var current = fileCheckEle.parent().attr("filePath");

	if(fileCheckEle.length != 1){
		alert('파일은 하나 선택해주세요');
		return false;
	}

	var _url ="?shortLink" +
		"&path=" + encodeURIComponent(current) +
		"&fileName=" + encodeURIComponent(fileCheckEle.val());

	$.ajax({
		url : _url,
		type : "GET",
		success: function(res){
			res = $.parseJSON(res);
			if(res.code == 'success'){
				$("#shortUrlModal .thumbnail img").attr({
					"src" : res.val.qrcodeUrl
				}).parents("a.thumbnail:first").attr({
					"href" : res.val.shortUrl,
					"target" : "_blank"
				});

				var cmtHtml =
					"전체주소 : " + res.val.fullUrl + "<br />" +
					"짧은주소 : " + res.val.shortUrl + "<br />" +
					"파일명 : " + res.val.fileName + "<br />" +
					"파일크기 : " + fileCheckEle.parents("tr").find("td:eq(2)").text() + "<br />" +
					"파일타입 : " + fileCheckEle.parents("tr").find("td:eq(3)").text() + "<br />" +
					"수정시간 : " + fileCheckEle.parents("tr").find("td:eq(4)").text() + "<br />";
				$("#shortUrlModal div.thumbnailCmt").html(cmtHtml).css({
					"overflow" : "hidden",
					"word-break" : "break-word"
				});

				$("#shortUrlModal div.qrcode_wd a").attr("href", res.val.qrcodeUrl + "&set=webdn");
				$("#shortUrlModal div.qrcode_print a").attr("href", res.val.qrcodeUrl + "&set=printdn");

				$("#shortUrlModal").modal({
				});
			}else{
				alert(res.msg);
			}
		},
		error : function(){
			alert("failure");
		}
	});
});
// -------------------------------------------------------------------------------------
// 정렬 리스트
// -------------------------------------------------------------------------------------
function OnSort(obj, dir, path) {
	var url = '?qfiles';
	var sortby = obj.getAttribute("sort");
	var sortorder = Cookie.get("sortorder") || "DESC";
	var pars = "&dir=" + encodeURIComponent(dir);
	pars += '&path=' + encodeURIComponent(path);
	Cookie.set("sortby", obj.getAttribute("sort"));
	Cookie.set("sortorder", ("DESC" === sortorder) ? "ASC" : "DESC");
	document.location.href = url + pars;
}
// -------------------------------------------------------------------------------------
// Cookie
// -------------------------------------------------------------------------------------
var Cookie = {
	set : function(name, value, expireday) {
		var expires = "";
		if (expireday) {
			var date = new Date();
			date.setTime(date.getTime() + expireday * 24 * 60 * 60 * 1000);
			expires = "; expires=" + date.toGMTString();
		}

		document.cookie = name + "=" + value + expires + "; path=/";
		return true;
	},
	get : function(name) {
		var re = new RegExp("(\;|^)[^;]*(" + name + ")\=([^;]*)(;|$)");
		var res = re.exec(document.cookie);
		return res != null ? res[3] : null;
	},
	del : function(name) {
		this.set(name, "", -1);
	}
}
// -------------------------------------------------------------------------------------
$(document).on("click", "#checkAll", function() {
	$('input:checkbox').not(this).prop('checked', this.checked);
});
