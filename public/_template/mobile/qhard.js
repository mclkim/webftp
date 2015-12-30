// -------------------------------------------------------------------------------------
// 2.다운로드
// -------------------------------------------------------------------------------------
function Download(dir, path, entry) {
	document.location.href = '?qdownload&dir=' + encodeURIComponent(dir)
	+ '&path=' + encodeURIComponent(path)	
	+ '&entry=' + encodeURIComponent(entry);	
}