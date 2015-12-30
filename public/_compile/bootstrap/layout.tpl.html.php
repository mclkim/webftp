<?php /* Template_ 2.2.8 2015/12/21 12:06:26 D:\phpdev\workspace\webftp\public\_template\bootstrap\layout.tpl.html 000001879 */ ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title><?php echo $TPL_VAR["name"]?> :: <?php echo $TPL_VAR["control"]?></title>

    <!-- Bootstrap -->
    <link href="<?php echo $TPL_VAR["bootstrap"]["min"]["css"]?>" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="<?php echo $TPL_VAR["custom"]["css"]?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- Bootstrap core JavaScript
	   ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?php echo $TPL_VAR["jquery"]["min"]?>"></script>
	<script src="<?php echo $TPL_VAR["bootstrap"]["min"]["js"]?>"></script>
	<script src="_template/bootstrap/qhard.js"></script>
	<script src="_template/bootstrap/sorttable/sorttable.js"></script>
</head>

<body>
	<!-- header -->
<?php $this->print_("header",$TPL_SCP,1);?>

	<!-- /header -->

	<!-- content -->
<?php $this->print_("content",$TPL_SCP,1);?>

	<!-- /content -->

	<!-- footer -->
<?php $this->print_("footer",$TPL_SCP,1);?>

	<!-- /footer -->
  </body>
</html>