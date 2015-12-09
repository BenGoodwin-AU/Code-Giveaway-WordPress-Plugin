<?php if(isset($_POST['giveaway'])){
update_option( 'def_giveaway', $_POST['giveaway'] );} ?>
<div class="wrap">
 <div class="icon32" id="icon-options-general"><br></div>	<h2>Beta Key Giveaways - View Keys</h2>
 <p> Use this page to view existing beta keys or individually delete them. </p>
 <p>Still confused? First of all check the <a target="_blank" href="http://keygiveawayscript.com/help.html">help file</a>, then ask for help on the <a target="_blank" href="http://keygiveawayscript.com">product website</a></p>

</div>
<style>
td{border:1px solid #000;}
table {margin-top:10px;border:1px solid #000;border-collapse:collapse;width:95%;}
th{background:#aaa;}
#bkey {width:300px;margin-right:20px}
#countofkeys {color:#ff0000;font-size:14px;line-height: 30px;}
#giveaway {width:300px;margin-bottom:10px;}
.del {text-align:center;}
</style>
<form name="form" method="POST">
Giveaway:
<select name="giveaway" id="giveaway">
<?php echo get_giveaways(); ?>
</select>
<br/>
<label><input type="radio" name="type" style="margin-right:5px;border:none;" value="0" checked>Unused keys</label>
<label><input type="radio" name="type" style="margin-right:5px;border:none;margin-left:10px;" value="1">Used keys</label>
<label><input type="radio" name="type" style="margin-right:5px;border:none;margin-left:10px;" value="2">All keys</label>
<br/><input type="submit" style='margin-top:10px;' value="Show keys"/>
</form><BR/>
<?php
if(isset($_POST['type']) && isset($_POST['giveaway'])){
list_keys($_POST['type'],$_POST['giveaway']);}
if(isset($_GET['key'])) delete_key($_GET['key']);
?>
