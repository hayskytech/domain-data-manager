<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>
<?php
global $wpdb;
$user_id = get_current_user_id();
if(isset($_POST["submit"])){
    $data["whatsapp_message"] = $_POST["whatsapp_message"];
    foreach ($data as $key => $value) {
        update_option($key, $value);
    }
    ?>
    <script type="text/javascript">
        window.location.href = "";
    </script>
    <?php
}
?>
<h3>Use these Dynamic Tags:</h3>
<div class="dynamic_tag">
	<b>domain_name</b>
	<b>domain_registrar_name</b>
	<b>registrant_name</b>
	<b>registrant_company</b>
	<b>registrant_address</b>
	<b>registrant_city</b>
	<b>registrant_state</b>
	<b>registrant_zip</b>
	<b>registrant_country</b>
	<b>registrant_email</b>
	<b>registrant_phone</b>
</div>
<style type="text/css">
	.dynamic_tag b{
		padding: 5px;
		margin: 5px;
		display: inline-block;
		/*float: left;*/
		border: 2px solid;
	}
</style>
<form method="post" enctype="multipart/form-data">
    <table class="ui collapsing striped table">
        <tr>
            <td>Whatsapp Message</td>
            <td><textarea name="whatsapp_message" cols="100" rows="10"><?php echo get_option("whatsapp_message"); ?></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submit" value="Save" class="ui blue button"></td>
        </tr>
    </table>
</form>