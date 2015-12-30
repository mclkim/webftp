<?php /* Template_ 2.2.8 2015/12/21 12:06:26 D:\phpdev\workspace\webftp\public\_template\bootstrap\files.tpl.html 000013902 */  $this->include_("bytesize");
$TPL_p_folder_1=empty($TPL_VAR["p_folder"])||!is_array($TPL_VAR["p_folder"])?0:count($TPL_VAR["p_folder"]);
$TPL_breadcrumb_1=empty($TPL_VAR["breadcrumb"])||!is_array($TPL_VAR["breadcrumb"])?0:count($TPL_VAR["breadcrumb"]);
$TPL_folder_1=empty($TPL_VAR["folder"])||!is_array($TPL_VAR["folder"])?0:count($TPL_VAR["folder"]);
$TPL_files_1=empty($TPL_VAR["files"])||!is_array($TPL_VAR["files"])?0:count($TPL_VAR["files"]);?>
<style>
.btn-default {
    background-color: #fff;
    border-color: #ccc;
    border-radius: 5px !important;
    color: #333;
    margin-bottom: 5px;
    margin-left: 3px !important;
}
.btn_pop {
	 background-color: #428bca;
    color: white;
    font-size: 12px;
    font-weight: bold;
    position: absolute;
    right: 0;
    top: -17px;
    z-index: 11;
	min-width: 40px;
}
@media (max-width: 768px){
	.col-md-2,
	.col_mid_02 {
		display:none !important;
	}
	.table-responsive {

	overflow-y: hidden;
	overflow-x: hidden;

	}
	.btn-group02 {
	display: block !important;
	position: fixed;
	bottom: 0;
	left: 0;
	background-color: #f5f5f5;
	width: 100%;
	text-align: center;
	padding: 10px 0;
	}
}
</style>

<script>
$(document).ready(function(){
	$( ".btn-default" ).hover(
	  function() {
		$( this ).find("div").stop().fadeIn();
	  }, function() {
		$( this ).find("div").stop().fadeOut();
	  }
	);
});
</script>

<!-- hidden form -->
<form id="hiddenForm" name="hiddenForm">
	<input type='hidden' name='dir' id='dir' value='<?php echo $TPL_VAR["directory"]?>'>
	<input type='hidden' name='path' id='path' value='<?php echo $TPL_VAR["path"]?>'>
	<input type='hidden' name='current' id='current' value='<?php echo $TPL_VAR["current"]?>'>
</form>
<!-- //hidden form -->

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="."><?php echo PROGRAM_NAME?></a>
		</div>

		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
	            <li class="dropdown">
	              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome,<?php echo $_SESSION["user"]["username"]?><span class="caret"></span></a>
	              <ul class="dropdown-menu" role="menu">
					<li><a href="#" id="uploadBtn">Upload</a></li>
					<li><a href="#" id="newBtn">New</a></li>
	                <li class="divider"></li>
					<li><a href="#" id="downloadBtn">Download</a></li>
					<li><a href="#" id="moveBtn">Move</a></li>
					<li><a href="#" id="renameBtn">Rename</a></li>
					<li><a href="#" id="removeBtn">Delete</a></li>
					<li><a href="#" id="zipBtn">Zip</a></li>					
					<li class="divider"></li>
					<li><a href="#" id="trashBtn">Trash</a></li>
					<li><a href="?logout">Logout</a></li>
	              </ul>
				</li>
              </ul>
			<form class="navbar-form navbar-right" onsubmit="return false;">
				<input type="text" id="search" class="form-control" placeholder="Search...">
			</form>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="row row-offcanvas row-offcanvas-left"> 
		<div class="col-sm-3 col-md-2 sidebar">
			<div class="btn-group">
				<button type="button" class="btn btn-default" data-toggle="modal" id="uploadBtn">
				<span class="glyphicon glyphicon-cloud-upload"></span>
				<div style="display:none;" class="btn_pop">업로드</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="newBtn">
				<span class="glyphicon glyphicon-plus"></span>
				<div style="display:none;" class="btn_pop">새폴더</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="renameBtn">
				<span class="glyphicon glyphicon-edit"></span>
				<div style="display:none;" class="btn_pop">수정하기</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="removeBtn">
				<span class="glyphicon glyphicon-minus"></span>
				<div style="display:none;" class="btn_pop">삭제</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="moveBtn">
				<span class="glyphicon glyphicon-transfer"></span>
				<div style="display:none;" class="btn_pop">이동</div>
				</button>
<?php if(false){?> 				
				<button type="button" class="btn btn-default" data-toggle="modal" id="shortUrlBtn">
				<span class="glyphicon glyphicon-magnet"></span>
				<div style="display:none;" class="btn_pop">파일정보</div>
				</button>
<?php }?>			
			</div>
			<p>
			<ul class="nav nav-pills nav-stacked">
<?php if(""==$TPL_VAR["path"]||"/"==$TPL_VAR["path"]){?>
<?php if(""==$TPL_VAR["directory"]||"/"==$TPL_VAR["directory"]){?>
				<li class="active">
				<a href="?qfiles&dir=&path=">Home</a>
				</li>
<?php }else{?>
				<li>
				<a href="?qfiles&dir=&path=">Home</a>
				</li>
<?php }?>
<?php }?>
			</ul>
			<ul class="nav nav-pills nav-stacked">
<?php if($TPL_p_folder_1){foreach($TPL_VAR["p_folder"] as $TPL_V1){?>
<?php if($TPL_V1["name"]==$TPL_VAR["directory"]){?>
				<li class="active"><a href="?qfiles&dir=<?php echo urlencode($TPL_V1["name"])?>&path=<?php echo urlencode($TPL_VAR["path"])?>"><?php echo $TPL_V1["name"]?><span
						class="badge pull-right"><?php echo $TPL_V1["files_inside"]- 2?></span></a></li>
<?php }else{?>
				<li><a href="?qfiles&dir=<?php echo urlencode($TPL_V1["name"])?>&path=<?php echo urlencode($TPL_VAR["path"])?>"><?php echo $TPL_V1["name"]?><span
						class="badge pull-right"><?php echo $TPL_V1["files_inside"]- 2?></span></a></li>
<?php }?>
<?php }}?>
			</ul>
			<ul class="nav nav-pills nav-stacked">
<?php if(".trash"==$TPL_VAR["directory"]){?>
				<li class="active"><a href="?qfiles&dir=.trash&path=<?php echo urlencode($TPL_VAR["path"])?>">휴지통</a>	</li>
<?php }else{?>
				<li><a href="?qfiles&dir=.trash&path=<?php echo urlencode($TPL_VAR["path"])?>">휴지통</a></li>
<?php }?>
			</ul>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<ol class="breadcrumb">
<?php if($TPL_breadcrumb_1){foreach($TPL_VAR["breadcrumb"] as $TPL_V1){?>
<?php if($TPL_V1["dir"]==$TPL_VAR["directory"]){?>
			<li class="active"><?php echo $TPL_V1["dir"]?></li>
<?php }else{?>
			<li><a href="?qfiles&dir=<?php echo urlencode($TPL_V1["dir"])?>&path=<?php echo urlencode($TPL_V1["path"])?>"><?php echo $TPL_V1["content"]?></a></li>
<?php }?>
<?php }}?>
			</ol>
			<div class="table-responsive">
				<table class="table table-striped sortable">
					<thead>
						<tr>
							<th scope='col' id='cb' class='manage-column column-cb check-column sorttable_nosort ' style=""><input id="checkAll" type="checkbox" /></th>
							<th class="col-md-7">이름</th>
							<th class="col-md-1 text-right">크기</th>
							<th class="col-md-2">유형</th>
							<th class="col-md-2">수정한 날짜</th>
						</tr>
					</thead>
					<tbody>
<?php if(false&&$TPL_VAR["full_path"]!='/'&&$TPL_VAR["full_path"]!='.'){?>
						<tr>
							<td></td>
							<td><a href="?qfiles&dir=..&path=<?php echo urlencode($TPL_VAR["full_path"])?>"><img src="<?php echo $TPL_VAR["ExtPath"]?>/folder.gif" alt=".." class="ui-li-icon">..</a></td>
							<td></td>
							<td class="col_mid_02">&nbsp;상위폴더&nbsp</td>
							<td class="col_mid_02"></td>
						</tr>
<?php }?>
<?php if($TPL_folder_1){$TPL_I1=-1;foreach($TPL_VAR["folder"] as $TPL_V1){$TPL_I1++;?>
						<tr>
							<td><input id="folder-select-<?php echo $TPL_I1?>" type="checkbox" value="<?php echo $TPL_V1["name"]?>" name="folderchk[]"></td>
							<td><a href="?qfiles&dir=<?php echo urlencode($TPL_V1["name"])?>&path=<?php echo urlencode($TPL_VAR["full_path"])?>"><img src="<?php echo $TPL_VAR["ExtPath"]?>/folder.gif" alt="<?php echo $TPL_V1["name"]?>" class="ui-li-icon"><?php echo $TPL_V1["name"]?></a></td>
							<td></td>
							<td class="col_mid_02">&nbsp;파일폴더&nbsp;</td>
							<td class="col_mid_02"><?php echo date("y-m-d h:i A",$TPL_V1["stamp"])?></td>
						</tr>
<?php }}?>

<?php if($TPL_files_1){$TPL_I1=-1;foreach($TPL_VAR["files"] as $TPL_V1){$TPL_I1++;?>
						<tr>
							<td filePath="<?php if($TPL_V1["path"]!=''){?><?php echo $TPL_V1["path"]?><?php }else{?><?php echo $TPL_VAR["path"]?><?php }?>/<?php echo $TPL_VAR["directory"]?>"><input id="file-select-<?php echo $TPL_I1?>" type="checkbox" value="<?php echo $TPL_V1["base64"]?>" name="filechk[]"></td>
<?php if($TPL_V1["path"]!=''){?>
							<td><a href="javascript:Download('<?php echo $TPL_VAR["directory"]?>','<?php echo $TPL_V1["path"]?>','<?php echo $TPL_V1["base64"]?>')"><img src="<?php echo $TPL_VAR["ExtPath"]?>/<?php echo $TPL_V1["type"]?>.gif" alt="<?php echo $TPL_V1["name"]?>" class="ui-li-icon"><?php echo $TPL_V1["name"]?></a></td>
<?php }else{?>
							<td><a href="javascript:Download('<?php echo $TPL_VAR["directory"]?>','<?php echo $TPL_VAR["path"]?>','<?php echo $TPL_V1["base64"]?>')"><img src="<?php echo $TPL_VAR["ExtPath"]?>/<?php echo $TPL_V1["type"]?>.gif" alt="<?php echo $TPL_V1["name"]?>" class="ui-li-icon"><?php echo $TPL_V1["name"]?></a></td>
<?php }?>
							<td class='text-right'><?php echo bytesize($TPL_V1["size"], 2)?></td>
							<td class="col_mid_02"><?php echo strtoupper($TPL_V1["ext"])?>&nbsp;파일</td>
							<td class="col_mid_02"><?php echo date("y-m-d h:i A",$TPL_V1["stamp"])?></td>
						</tr>
<?php }}?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadModal">
    <div class="modal-dialog">
        <div class="modal-content">

        </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<!-- Modal -->
<div class="modal fade" id="newModal">
    <div class="modal-dialog">
        <div class="modal-content">

        </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<!-- Modal -->
<div class="modal fade" id="renameModal">
    <div class="modal-dialog">
        <div class="modal-content">

        </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<!-- Modal -->
<div class="modal fade" id="moveModal">
    <div class="modal-dialog">
        <div class="modal-content">

        </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<!-- Modal -->
<div class="modal fade" id="zipModal">
    <div class="modal-dialog">
        <div class="modal-content">

        </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<?php if(false){?>
<!-- Modal -->
<div class="modal fade" id="shortUrlModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div id="form-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal">×</a>
					<h3>파일정보</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<a href="#" class="thumbnail">
								<img src="#" alt="shortUrl">
							</a>
						</div>
						<div class="col-sm-6 col-md-9 thumbnailCmt"></div>
						<div class="col-sm-6 col-md-12">
							<div class="col-sm-6 col-md-6 qrcode_wd">
								<a href="#">
									<img width="103" height="27" src="/_image/mypage/pop_web.gif" alt="웹용 다운로드" title="웹용 다운로드">
								</a>
							</div>
							<div class="col-sm-6 col-md-6 qrcode_print">
								<a href="#">
									<img width="103" height="27" src="/_image/mypage/pop_print.gif" alt="웹용 다운로드" title="웹용 다운로드">
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				<!-- -->
				</div>
			</div>
		</div> <!-- /.modal-content -->
	</div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
<?php }?>
<div class="btn-group02" style="display:none;">
<div class="btn-group">
				<button type="button" class="btn btn-default" data-toggle="modal" id="uploadBtn">
				<span class="glyphicon glyphicon-cloud-upload"></span>
				<div style="display:none;" class="btn_pop">업로드</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="newBtn">
				<span class="glyphicon glyphicon-plus"></span>
				<div style="display:none;" class="btn_pop">새폴더</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="renameBtn">
				<span class="glyphicon glyphicon-edit"></span>
				<div style="display:none;" class="btn_pop">수정하기</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="removeBtn">
				<span class="glyphicon glyphicon-minus"></span>
				<div style="display:none;" class="btn_pop">삭제</div>
				</button>
				<button type="button" class="btn btn-default" data-toggle="modal" id="moveBtn">
				<span class="glyphicon glyphicon-transfer"></span>
				<div style="display:none;" class="btn_pop">이동</div>
				</button>
<?php if(false){?> 				
				<button type="button" class="btn btn-default" data-toggle="modal" id="shortUrlBtn">
				<span class="glyphicon glyphicon-magnet"></span>
				<div style="display:none;" class="btn_pop">파일정보</div>
				</button>
<?php }?>			
			</div>
</div>