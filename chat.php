<?php 
$custom_os='android';
if(isset($_REQUEST['os'])){
	if($_REQUEST['os']=='ios')
		$custom_os='ios';

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chat with as</title>
</head>
<body style="margin:0px;padding:0px">
<script>
function async(u, c) {
	var d = document, t = 'script',
	o = d.createElement(t),
	s = d.getElementsByTagName(t)[0];
	o.type = 'text/javascript';
	o.async = true;
	o.defer = 'defer';

	o.src = '//' + u;
	if (c) { o.addEventListener('load', function (e) { c(null, e); SnapEngage.startLink();
	SnapEngage.setCallback('Close', function (type, status) {
			<?php if ($custom_os=='ios') { ?>
				window.location.href="closeChatPage://Close";
			<?php }else{ ?>
				AndroidCallback.closeIt();
			<?php } ?>
	});
	
	  }, false); }
	s.parentNode.insertBefore(o, s);
	
	
	
}

async('commondatastorage.googleapis.com/code.snapengage.com/js/566863df-dcda-4bae-8a1a-862776593bc3.js', function() {
});
</script>
</body>
</html>

