<div class="wrap">
 <div class="icon32" id="icon-options-general"><br></div>	<h2>Beta Key Giveaways - Add Giveaway</h2>
 <p> Use this page to create giveaways. <b>Clicking the "x" letter will delete an entire giveaway. </b> </p>
 <p>Still confused? First of all check the <a target="_blank" href="http://keygiveawayscript.com/help.html">help file</a>, then ask for help on the <a target="_blank" href="http://keygiveawayscript.com">product website</a></p>
</div>
<style>
#giveaway {width:300px;margin-right:20px}
td{border:1px solid #000;}
table {margin-top:10px;border:1px solid #000;border-collapse:collapse;width:95%;}
th{background:#aaa;}
.del {text-align:center;}
#countofkeys {color:#ff0000;font-size:14px;line-height: 30px;}
</style>
<form name="form" method="POST">
<label style="margin-right:20px;">Giveaway name:</label><input type="text" id="giveaway" name="giveaway" /><input type="submit" value="Add Giveaway"/></form><BR/>
<?php
if(isset($_POST['giveaway']) && strlen($_POST['giveaway'])>3){
add_giveaway($_POST['giveaway']);
echo 'Giveaway was added';
} 
else if(isset($_POST['giveaway'])&&strlen($_POST['giveaway'])<3){ echo "Giveaway name can't be empty or this short<br/>";} 
if(isset($_GET['giveawayid'])) delete_giveaway($_GET['giveawayid']);

echo list_giveaways();
?>
