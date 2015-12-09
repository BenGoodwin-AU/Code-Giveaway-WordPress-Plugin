<div class="wrap">
 <div class="icon32" id="icon-options-general"><br></div>	<h2>Beta Key Giveaways - Add Keys</h2>
 <p> Use this page to add new beta keys to the giveaways you have created. <b>To add multiple keys at once input one key per line.</b> </p>
 <p>Still confused? First of all check the <a target="_blank" href="http://keygiveawayscript.com/help.html">help file</a>, then ask for help on the <a target="_blank" href="http://keygiveawayscript.com">product website</a></p>
</div>
<style>
#giveaway {width:300px;margin-bottom:10px;}
#keys {width:355px;margin-bottom:10px;}
</style>
<form name="form" method="POST">
Giveaway:
<select name="giveaway" id="giveaway">
<?php echo get_giveaways(); ?>
</select>
<br/><textarea name='keys' id='keys' rows='15' wrap='off'></textarea><br/><input type="submit" value="Add Keys"/></form><BR/>
<?php
if(isset($_POST['keys']) && !empty($_POST['keys'])){
$text = trim($_POST['keys']);
$textAr = explode("\r\n", $text);
foreach ($textAr as $line) {
if(!empty($line)){
add_key($line,$_POST['giveaway']);}
}
echo "Keys were added"; 
}

?>