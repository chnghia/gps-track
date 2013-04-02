<? echo '<?xml version="1.0"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="author" lang="en" content="Nghia Chung" />
  <meta name="copyright" lang="en" content="Nghia Chung" />
	<meta name="robots" content="all,follow" />
	<meta name="description" content="<? echo $pageTitle ?>" />
	<meta name="keywords" content="tracking,GPS" />
	<? echo $metaKeywords; ?>
	<? echo $metaDescription; ?>
	<? echo $metaCustom; ?>
	<link rel="stylesheet" media="screen,projection" type="text/css" href="/assets/css/reset.css" />
	<link rel="stylesheet" media="screen,projection" type="text/css" href="/assets/css/main.css" />
	<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="assets/css/main-msie.css" /><![endif]-->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="/assets/css/style.css" />
	<link rel="stylesheet" media="print" type="text/css" href="/assets/css/print.css" />
	<link href="/assets/css/redmond/jquery-ui-1.8.9.custom.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/assets/css/layout-default-latest.css" rel="stylesheet" type="text/css" media="screen" />
	<? echo $pageCSS; ?>
	<script type="text/javascript" src="/assets/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/assets/js/jquery-ui-1.8.9.custom.min.js"></script>
	<script type="text/javascript" src="/assets/js/ui.tabs.paging.js"></script>
	<script type="text/javascript" src="/assets/js/jquery.layout-latest.js"></script>
	<script type="text/javascript" src="/assets/js/main.js"></script>
	<? echo $pageScript; ?>
	<script type="text/javascript">
	var base_url = "<? echo site_url(''); ?>";
	var default_spacing_open = 4, default_spacing_closed = 5;
	</script>
	<title><? echo $pageTitle ?></title>
</head>
<body>
<div id="main">
<? echo $pageHeader; ?>
<? echo $pageContent; ?>
<? echo $pageFooter; ?>
<? $this->load->view('wrapper/loginform'); ?>
<div id="dialog-mesage"><p></p></div>
</div> <!-- /main -->
</body>
</html>