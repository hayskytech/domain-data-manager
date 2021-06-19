<style type="text/css">
	.inline-form{
		display: inline-block;
	}
	label{
		font-size: 30px;
		text-shadow: white 1px 1px;
	}
	.domain_filter{
		color: #00e300;
		background-color: black;
		padding: 15px;
		border: 2px solid black;
		border-radius: 15px;
	}
	#wpcontent{
		background-image: url('<?php echo plugin_dir_url(__FILE__); ?>/bg.gif');
    	background-size: 400px 400px !important;
    	color: #149414;
	}
	#wpcontent h1, #wpcontent h2{ 
		color: #149414;

		font-size:40px; 
		text-align:center !important; 
		background-color: black;
		padding: 5px;
	}
	#wpcontent label{
		padding: 5px;
		font-weight: bold;
	}
</style>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.3/components/button.min.css">
<?php
error_reporting(E_ERROR | E_PARSE);
global $wpdb;
$i = $_GET["i"];
$n = $_GET["n"];
if (!isset($_GET["i"])) {
	$i = 1;
}
if (!isset($_GET["n"])) {
	$n = 10;
}
include '/simple_html_dom.php';
?>
<h1 class="domain_filter">DOMAIN FILTER</h1>
<!-- <button class="ui green button">DOMAIN FILTER</button> -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<div style="text-align: center;">
	<form class="inline-form" id="main_form" style="text-align: center;">
		<input type="hidden" name="page" value="filter_wir">
		<label>Current:</label>
		<input type="number" name="i" min="1" max="1000" value="<?php echo $i; ?>">
		<label>Stop:</label>
		<input type="number" name="n" min="1" max="1000" value="<?php echo $n; ?>">
		<input type="submit" name="filter" value="Filter" id="filter" class="ui green button">
		<input type="submit" name="whatsapp" value="WhatsApp" id="whatsapp" class="ui green button">
	</form>
	<form class="inline-form">
		<input type="hidden" name="page" value="<?php echo $_GET["page"]; ?>">
		<button class="ui red button">Stop</button>
	</form>
</div>
<script type="text/javascript">
$('select[name=status]').val('<?php echo $_GET["status"]; ?>');
$('input[name=i]').val('<?php echo $i; ?>');
</script>
<?php
if ($i<=$n) {
	if ($_GET["filter"]){
		$rows = $wpdb->get_results("SELECT domain_name,phone,registrant_name,status
		 FROM num WHERE status='Unfiltered' LIMIT ".($i-1).",1");
		if (!count($rows)) {
			echo "<h1>All Domains Completed</h1>";
		} else { 
			// Pending domains found
			$row = $rows[0];
			$base_url = 'http://'.$row->domain_name;
			if($html = file_get_html($base_url)){
				$codes = $html->find('footer',0);
			}
			echo '<h1>';
			if (count($codes)) {
				echo $row->domain_name." is already designed.<br>";
				$wpdb->update('num',array('status'=>'Lost'),array('domain_name'=>$row->domain_name));
			} else {
				echo $row->domain_name." is not designed. It is Lead for us.<br>";
				$wpdb->update('num',array('status'=>'Lead'),array('domain_name'=>$row->domain_name));
			}
			echo '</h1>';
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				setTimeout(function() {
					$('input[name=i]').val('<?php echo $i; ?>');
					$('#filter').click();
				}, 5000);
			});
			</script>
			<?php
		}
	}
	if ($_GET["whatsapp"]) {
		$rows = $wpdb->get_results("SELECT status,domain_name,domain_registrar_name,registrant_name,registrant_company,registrant_address,registrant_city,registrant_state,registrant_zip,registrant_country,registrant_email,registrant_phone,phone FROM num WHERE status='Lead' LIMIT 1");
		if (!count($rows)) {
			echo "<h1>All Domains Completed</h1>";
		} else { 
			// Pending domains found
			$row = (array) $rows[0];
			$i++;
			$phone = $row["phone"];
			$already_msged = $wpdb->get_var("SELECT phone FROM num WHERE phone='$phone' AND status
			='Contacted' LIMIT 1");
			$multi = $wpdb->get_var("SELECT phone FROM num WHERE phone='$phone' AND status
			='Multi' LIMIT 1");
			
			if (!$already_msged && !$Multi) {
				$wpdb->update('num',array('status'=>'Contacted'),array('phone'=>$row["phone"]));
				include 'msg.php';
				?>
				<h2>Trying to send msg to <?php echo $row["phone"]; ?></h2>
				<h1 id="output_msg"></h1>
				<script type="text/javascript">
				$(document).ready(function(){
					myWindow = window.open("<?php echo $wa; ?>","blank");
					var popupTick = setInterval(function() {
				      if (myWindow.closed) {
				        clearInterval(popupTick);
				        $('#output_msg').text('WhatsApp closed. Going to next Lead.');
				        setTimeout(function() {
							$('input[name=i]').val('<?php echo $i; ?>');
							$('#whatsapp').click();
						}, 5000);
				      }
				    }, 5000);
				});
				</script>
				<?php
			} else {
				$wpdb->update('num',array('status'=>'Multi'),array('phone'=>$row["phone"]));
				?>
				<h1 id="output_msg">Message Already sent. (Multi)</h1>
				<script type="text/javascript">
				$(document).ready(function(){
				    setTimeout(function() {
						$('input[name=i]').val('<?php echo $i; ?>');
						$('#whatsapp').click();
					}, 3000);
				});
				</script>
				<?php
			}
		}
	}
} else {
	echo "<h1>All domains completed.</h1>";
}
?>