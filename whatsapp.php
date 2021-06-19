<a href="
<?php
$wa = 'https://web.whatsapp.com/send?phone=918498000172&text=Hi';
echo $wa;
?>" target="blank"><h1>CLICK here</h1></a>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	setTimeout(function() {
		myWindow = window.open("<?php echo $wa; ?>","blank");
		setTimeout(function(){
			// myWindow.close();
		}, 6000);
	}, 1000);
});
</script>
<!-- This file should be deleted -->