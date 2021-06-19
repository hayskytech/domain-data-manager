<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script type="text/javascript" src="https://semantic-ui.com/javascript/library/tablesort.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/dropdown.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/sp-1.0.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/sp-1.0.1/datatables.min.js"></script>

<?php
error_reporting(E_ERROR | E_PARSE);

if(is_admin()) {
    echo '<div style="padding-top: 20px; padding-right: 20px">';
}
$table_name = 'num';
$today = date('Y-m-d');
if ($_POST["status"]=='ALL') {
    $status = '';
    $status_val = 'ALL';
} elseif (!isset($_POST["status"])) {
	$status = "WHERE status='Unfiltered'";
	$status_val = 'Unfiltered';
} else {
    $status = "WHERE status='".$_POST["status"]."'";
    $status_val = $_POST["status"];
}
global $wpdb;
if (isset($_FILES["import"])) {
    if (pathinfo($_FILES["import"]["name"],PATHINFO_EXTENSION)==="csv") {
        $targetPath = dirname(__FILE__)."/" .rand(11,99). $_FILES["import"]["name"];
        move_uploaded_file($_FILES["import"]["tmp_name"], $targetPath);
        $handle = fopen($targetPath, "r");
        $imported = 0; $failed = 0;
        while(($filesop = fgetcsv($handle, 1000, ",")) !== false) {
            if (!$col) {
                for ($i=0; $i < count($filesop); $i++) { 
                    $col[$i] = $filesop[$i];
                }
            } else {
                $data["phone"] = '';
                $data["status"] = 'Unfiltered';
                for ($i=0; $i < count($filesop); $i++) { 
                    $data[$col[$i]] = sanitize_text_field($filesop[$i]);
                    if ($col[$i]=="create_date") {
                        $data[$col[$i]] = date("Y-m-d",strtotime($data[$col[$i]]));
                    }
                    if ($col[$i]=="update_date") {
                        $data[$col[$i]] = date("Y-m-d",strtotime($data[$col[$i]]));
                    }
                    if ($col[$i]=="expiry_date") {
                        $data[$col[$i]] = date("Y-m-d",strtotime($data[$col[$i]]));
                    }
                    $data["phone"] = str_replace('.', '', $data["registrant_phone"]); 
                }
                if ($data["phone"]) {
                    $wpdb->insert($table_name,$data);
                }
                if ($wpdb->insert_id) {
                    $imported++;
                } else {
                    $failed++;
                }
            }
        }
        echo $imported." rows imported. ".$failed." rows failed.";
        fclose($handle);
        unlink($targetPath);
    } else {
        $message = "Invalid File Type. Upload Excel File.";
        echo $message;
    }
}
if($_POST["action"]){
    $data["domain_name"] = $_POST["domain_name"];
    $data["status"] = $_POST["status"];
    if($_POST["action"]=='Add'){
        $wpdb->insert($table_name,$data);
    } else if($_POST["action"]=='Add New' || $_POST["action"]=='Edit' || $_POST["no_list"]=='yes'){
    $columns = rawurlencode('"num","domain_name","query_time","create_date","update_date","expiry_date","domain_registrar_id","domain_registrar_name","domain_registrar_whois","domain_registrar_url","registrant_name","registrant_company","registrant_address","registrant_city","registrant_state","registrant_zip","registrant_country","registrant_email","registrant_phone"');
    if ($_POST["action"]!='Edit') {
        ?>
        <h2>Import from CSV (excel)</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="import">
            <input type="submit" name="import_csv" value="Import (csv)" class="ui grey button">
            <a href="data:text/plain;charset=UTF-8,<?php echo $columns; ?>" download="filename.csv">Download Sample CSV</a>
        </form>
        <?php
    }
    ?>
    <form method="POST" enctype="multipart/form-data">
        <h2 id="small_frm">Add New Here</h2>
        <input type="hidden" name="id">
        <input type="hidden" name="no_list" value="yes">
        <table class="ui blue striped table collapsing">
            <tr>
                <td id="domain_name_field"></td>
                <td><select class="ui search dropdown" name="status" autofocus="">
                        <option value="">Select</option>
                        <option value="Unfiltered">Unfiltered</option>
                        <option value="Lead">Lead</option>
                        <option value="Contacted">Contacted</option>
                        <option value="Lost">Lost</option>
                        <option value="Responded">Responded</option>
                    </select>
                    <script type="text/javascript">
                        $(".ui.dropdown").dropdown();
                    </script>
                </td>
            </tr>
            <tr>
                <td>Domain Name</td>
                <td><input type="text" name="domain_name">
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="action" value="Add" class="ui blue mini button"></td>
            </tr>
        </table>
        </form>
        <style type="text/css">
            .ui.dropdown{
                width: 100% !important;
            }
        </style>
        <?php
    }
    if($_POST["action"]=='Edit'){
        $id = $_POST["id"];
        echo $id;
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id",ARRAY_A);
        $data = $row;
        ?>
        <script type="text/javascript">
            $('input[name=action]').val('Save');
            $('input[name=id]').val('<?php echo $_POST["id"]; ?>');
            $('#small_frm').html('Edit Here');
        </script>
	    <script type="text/javascript">
	        $('input[name=domain_name]').val('<?php echo $data["domain_name"]; ?>');
	        $('#domain_name_field').html('<?php echo $data["domain_name"]; ?>');
	        $('select[name=status]').val('<?php echo $data["status"]; ?>');
	        x = $('select[name=status]').children('option[value="<?php echo $data["status"]; ?>"]').text();
	        $("select[name=user]").parent().children(".text").html(x);
	        y = $('select[name=status]').parent().children(".text");
	        y.html(x);
	        y.css("color","black");
	    </script>
        <?php
    }
    if($_POST["action"]=='Save'){
        $id = $_POST["id"];
        $wpdb->update($table_name,$data,array('id' => $id));
    }
    if($_POST["action"]=='Delete'){
        $id = $_POST["id"];
        $wpdb->delete($table_name,array('id' => $id));
    }
} 
if($_POST["action"]=="DELETE"){
    $status = $_POST["status"];
    $wpdb->delete($table_name,array('status' => $status));
    $status_val = 'ALL';
}
if(($_POST["action"]!='Edit') && $_POST["action"]!='Add New' && $_POST["no_list"]!='yes') {
    ?><div></div>
    <form class="inline-form" method="POST"><input type="submit" name="action" value="Add New" class="ui green button"></form>
    <form class="inline-form" id="main_form" method="POST">
        <input type="hidden" name="page" value="num_admin">
        <select name="status">
            <option value="Unfiltered">Unfiltered</option>
            <option value="Lead">Lead</option>
            <option value="ALL">ALL</option>
            <option value="Contacted">Contacted</option>
            <option value="Responded">Responded</option>
            <option value="Lost">Lost</option>
        </select>
        <script type="text/javascript">
            $('select[name=status]').val('<?php echo $status_val; ?>');
        </script>
        <input type="submit" class="ui blue button" name="action" value="SHOW">
        <input type="submit" class="ui red button" name="action" value="DELETE" id="delete_all">
    </form>
    <script type="text/javascript">
        $("#delete_all").click(function() {
            x = $('select[name=status]').val();
            if (!confirm("Do you want to delete "+x+" domains?")) {
                event.preventDefault();
            }
        });
    </script>
    <br><br>
    <div style="overflow-x:auto">
    <table id="myTable" class="ui unstackable celled table dataTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Domain</th>
                <th>Phone</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rows = $wpdb->get_results("SELECT id,status,domain_name,domain_registrar_name,registrant_name,registrant_company,registrant_address,registrant_city,registrant_state,registrant_zip,registrant_country,registrant_email,registrant_phone,phone 
FROM $table_name ".$status." LIMIT 1000");
        // $wpdb->show_errors(); $wpdb->print_error();
        foreach($rows as $row){
            $row = (array) $row;
            if ($row["phone"]) {
                include 'msg.php';
            }
            echo '<tr row-id="'.$row["id"].'">';
            echo '<td>'.++$sl.'</td>';
            echo '<td title="'.$row["domain_registrar_name"].'">
            <big><a href="http://'.$row["domain_name"].'" target="blank">'.$row["domain_name"].'</a></big> - 
            <a href="http://'.$row["domain_name"].'" target="blank"><i class="external alternate green icon"></i></a></td>';
            echo '<td><a href="'.$wa.'" target="blank">'.$row["phone"].'</a><br>
            '.$row["status"].'</td>';
            echo '<td><b>'.$row["registrant_name"].'</b>, '.$row["registrant_company"].'';
            echo '<br>'.$row["registrant_address"].'<br>'.$row["registrant_city"].', '.$row["registrant_state"].'</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
    </div>
    <form method="post" id="action_form">
        <input type="hidden" name="id">
        <input type="hidden" name="action">
    </form>
    <script type="text/javascript">
        $(document).ready(function(){
            $("td:nth-child(2)").append('<i class="trash alternate red icon" onclick="delete_now(this)"></i> <i class="edit blue icon" onclick="edit_now(this)"></i>');
        });
        function edit_now(x){
            var id = $(x).parent().parent().attr("row-id");
            var frm = $("#action_form")
            frm.children("input[name=id]").val(id);
            frm.children("input[name=action]").val("Edit");
            frm.submit();
        }
        function delete_now(x){
            var id = $(x).parent().parent().attr("row-id");
            var frm = $("#action_form")
            frm.children("input[name=id]").val(id);
            frm.children("input[name=action]").val("Delete");
            if (confirm("Do you want to delete?")) {
            frm.submit();
            }
        }
    </script>
    <style type="text/css">
    	.inline-form{
    		display: inline-block;
    	}
        .edit.icon, .trash.icon{
            float: right !important;
            font-size: 140%;
            cursor: pointer;
        }
        big>a{ font-weight: bold; }
        .inline-form{
            display: inline-block;
        }
        a:visited{
            color: brown;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: left;
        }
        .dataTables_info{
            display: block;
        }
    </style>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#myTable").DataTable( {
            dom: "BlifrtpLr",
            "aaSorting": [],
            buttons: [
                "csv", "excel", "pdf", "print"
            ],
             "pageLength": 100
        } );
    } );
    </script>
    <?php
}
if(is_admin()) {
    echo '</div>';
}
?>