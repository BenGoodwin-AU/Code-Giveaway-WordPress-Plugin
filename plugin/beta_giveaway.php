<?php
/*
Plugin Name: Beta Key Giveaways
Plugin URI: http://keygiveawayscript.com
Description: Allows you to easily create and manage beta key giveaways. Features advance security to help prevent key farming and an easy-to-use shortcode which you can put on any post/page.
Version: 1.1
Author: Ben Goodwin
*/

//load css styling for the plugin
    add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );
    function safely_add_stylesheet() {
        wp_enqueue_style( 'prefix-style', plugins_url('/css/key-redemption-ui.css', __FILE__) );
    }


//actions on plugin activation
register_activation_hook(__FILE__, 'activation_func');
function activation_func(){
global $wpdb;
$query="CREATE TABLE if not exists `giveaways` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(500) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
";
$wpdb->query($query);
$query="CREATE TABLE if not exists `betakeys` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`give_away` INT(10) NOT NULL DEFAULT '0',
	`bkey` VARCHAR(150) NOT NULL,
	`ip` VARCHAR(20) NULL DEFAULT NULL,
	`user_id` INT(5) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `give_away` (`give_away`),
	CONSTRAINT `FK_betakeys_giveaways` FOREIGN KEY (`give_away`) REFERENCES `giveaways` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
";
$wpdb->query($query);
$query="insert into `giveaways` (`name`) values ('Default');";
$wpdb->query($query);
}
function betagiveaway_menu() {
 add_menu_page('List keys', 'Beta Key Giveaways', 'edit_posts', 'beta_giveaway/view.php', '',   plugins_url('beta_giveaway/icon.png'), 99);
	add_submenu_page( 'beta_giveaway/view.php', 'List all', 'List all', 'edit_posts', 'beta_giveaway/view.php' ); 
	add_submenu_page( 'beta_giveaway/view.php', 'Add new keys', 'Add new keys', 'edit_posts', 'beta_giveaway/add_new_k.php' ); 
	add_submenu_page( 'beta_giveaway/view.php', 'Add new giveaway', 'Add new giveaway', 'edit_posts', 'beta_giveaway/add_new_g.php' ); 
}
add_action('admin_menu', 'betagiveaway_menu');

// check if key is used
function used_or_not($str){
if($str==NULL) return "Not Used"; else return $str;
}

// get list of used/unused/all keys
function list_keys($type=0,$giveaway){
global $wpdb;
$query="SELECT betakeys.bkey, betakeys.ip, $wpdb->usermeta.meta_value, betakeys.id
FROM betakeys LEFT JOIN $wpdb->usermeta ON $wpdb->usermeta.user_id = betakeys.user_id AND $wpdb->usermeta.meta_key = 'nickname'
where betakeys.`give_away`='$giveaway'";
$keys=$wpdb->get_results($query,ARRAY_N);
$host = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];
$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
if(count($keys)>0){
switch ($type) {
     case 0:
        $query1="select bkey,id from betakeys where user_id is NULL and ip is NULL and give_away='$giveaway';";
		$unkeys=$wpdb->get_results($query1,ARRAY_N);
		$report="<table style='width:600px;'><tr><th>Key</th><th>Del</th></tr>";
		if(count($unkeys)>0){
		foreach($unkeys as $unkeys_){ $url = !empty($query) ? "http://$host$self?$query"."&key=$unkeys_[1]" : "http://$host$self"."?key=$unkeys_[1]"; $report.="<tr><td >".$unkeys_[0]."</td><td class='del'><a href='$url'>x</a></td></tr>";}
		$report.="</table>";
		echo "<br/><span id='countofkeys'>Unused keys: ".count($unkeys)." | Total number of keys: ".count($keys)."</span><br/>".$report;
		}
		else echo "<br/><span style='color:#ff0000;'>There are no keys that match your query</span>";
        break;
    case 1:
        $query2="select betakeys.bkey,betakeys.ip,$wpdb->usermeta.meta_value,betakeys.id from betakeys
inner join $wpdb->usermeta on ($wpdb->usermeta.user_id = betakeys.user_id)
where meta_key='nickname' and give_away='$giveaway'";
		$ukeys=$wpdb->get_results($query2,ARRAY_N);
		$report="<table><tr ><th>Key</td><th>IP</th><th>User Name</th><th>Del</th></tr>";
		if(count($ukeys)>0){
		foreach($ukeys as $ukeys_){ $url = !empty($query) ? "http://$host$self?$query"."&key=$ukeys_[3]" : "http://$host$self"."?key=$ukeys_[3]"; $report.="<tr><td >".$ukeys_[0]."</td><td >".$ukeys_[1]."</td><td >".$ukeys_[2]."</td><td class='del'><a href='$url'>x</a></td></tr>";}
		$report.="</table>";
		echo "<br/><span id='countofkeys'>Used keys: ".count($ukeys)." | Total number of keys: ".count($keys)."</span><br/>".$report;
		}
		else echo "<br/><span style='color:#ff0000;'>There are no keys that match your query</span>";
        break;
    case 2:
        $report="<table><tr ><th>Key</td><th>IP</th><th>User Name</th><th>Del</th></tr>";
		foreach($keys as $keys_){ $url = !empty($query) ? "http://$host$self?$query"."&key=$keys_[3]" : "http://$host$self"."?key=$keys_[3]"; $report.="<tr><td >".$keys_[0]."</td><td >".used_or_not($keys_[1])."</td><td >".used_or_not($keys_[2])."</td><td class='del'><a href='$url'>x</a></td></tr>";}
		$report.="</table>";
		echo "<br/><span id='countofkeys'>Total number of keys: ".count($keys)."</span><br/>".$report;
		break;
}
}
else echo "<br/><span style='color:#ff0000;'>There are no keys in the database</span>";
}
// add new key
function add_key($key,$giveaway){
global $wpdb;
$query="SELECT * FROM `betakeys` where bkey=$key and give_away=$giveaway";
$keys=$wpdb->get_results($query,ARRAY_N);
if(count($keys)==0){$query="insert into betakeys (`bkey`,`give_away`) values ('$key','$giveaway');";$wpdb->query($query);}
}

//giveaway itself
function giveaway ($atts, $content = null){
global $wpdb,$current_user;
extract( shortcode_atts( array( 'gid'  => '0'), $atts ) );
ob_start();
if (is_user_logged_in()==1){
$query="select * from betakeys where user_id=$current_user->ID and give_away=$gid;";
$user=$wpdb->get_results($query,ARRAY_N);
if(count($user)==0){
$ip=GetIP();
$query="select * from betakeys where ip='$ip' and give_away=$gid;";
$user=$wpdb->get_results($query,ARRAY_N);
if(count($user)==0){
$query1="select * from betakeys where user_id is NULL and ip is NULL and give_away='$gid';";
$unkeys=$wpdb->get_results($query1,ARRAY_N);
if(count($unkeys)>0){

$id=$unkeys[0][0];
$allowed=1;
$url=get_permalink();
$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
$url = !empty($query) ? "$url"."&get_key=1" : "$url"."?get_key=1";


?>
<div class="keyredemption"><center><p><h3>
Keys left: <?php echo count($unkeys); ?> <br/><br/> 
<a href="<?php echo $url; ?>" ><input type='button' value='Get key' /></a></h3></p></center></div>


<?php
if(isset($_GET['get_key']) && $allowed==1 && $_GET['get_key']==1){
$query1="update betakeys set user_id='$current_user->ID', ip='$ip' where id='$id';";
$wpdb->query($query1);
$allowed==0;

?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">$(".keyredemption").hide(); </script>
<br/><div class="keyredemption"><center><p><h3>Your key is: <br /><br /><b><?php echo $unkeys[0][2];?></span></b></h3></p></center></div>
<?php
}}
else {?>
 <div class="keyredemption"><center><p><h3>We're sorry, all the keys have already been taken.</h3></p></center></div>
 <?php } 
}
else { ?>
 <div class="keyredemption"><center><p><h3>This IP address has already been used to take a key!</h3></p></center></div>
 <?php }

}
else { 
$query="select bkey from betakeys where user_id=$current_user->ID and give_away=$gid;";
$user=$wpdb->get_results($query,ARRAY_N);
?>
 <div class="keyredemption"><center><p><h3>You have already redeemed a key. In case you forgot it, your key is:</p>
 <p><i><?php echo $user[0][0];?></i></h3></p></div></center>
 <?php } //end of if key taken by user
} //end of logged in
else 
{ ?>
 <div class="keyredemption"><center><p><h3>You need to be <a href="<?php echo wp_login_url(); ?>" title="Login">logged in</a> to claim your key.</p>
 <p>Not registered? <a href="<?php echo site_url('/wp-login.php?action=register') ?>" title="Login">Join today!</div></h3></a></p></center>
<?php }
$output_string=ob_get_contents();;
 ob_end_clean();
return $output_string;
}
add_shortcode('giveaway', 'giveaway');

function get_giveaways(){
global $wpdb;
$query="SELECT * from giveaways";
$giveaways=$wpdb->get_results($query,ARRAY_N);
$opt=get_option( 'def_giveaway');
foreach ($giveaways as $giveaways_){
if($opt==$giveaways_[0]) $s='selected'; else $s='';
$list.="<option value='$giveaways_[0]' $s>$giveaways_[1] | id=$giveaways_[0]</option>";
}
return $list;
}

function add_giveaway($name){
global $wpdb;
$query="insert into `giveaways` (`name`) values ('$name');";
$wpdb->query($query);
}

function GetIP() 
{ 
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
		$ip = getenv("REMOTE_ADDR"); 
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
	else 
		$ip = "unknown"; 
	return($ip); 
} 
function delete_key($key){
global $wpdb;
$query="delete from `betakeys` where `id`=$key;";
$cnt=$wpdb->query($query);
if($cnt!=0)  echo "<span id='countofkeys'>Key with id $key was deleted</span>";
}

function delete_giveaway($gid){
global $wpdb;
$query="delete from `giveaways` where `id`=$gid;";
$cnt=$wpdb->query($query);
if($cnt!=0) echo "<span id='countofkeys'>Giveaway with id $gid was deleted</span>";
}

function list_giveaways(){
global $wpdb;
$query="SELECT * from giveaways";
$giveaways=$wpdb->get_results($query,ARRAY_N);
$report="<table style='width:400px;'><tr><th>Giveaway</th><th>Del</th></tr>";
$keys=$wpdb->get_results($query,ARRAY_N);
$host = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];
$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
foreach ($giveaways as $giveaways_){
$url = !empty($query) ? "http://$host$self?$query"."&giveawayid=$giveaways_[0]" : "http://$host$self"."?giveawayid=$giveaways_[0]";
$report.="<tr><td >".$giveaways_[1]."</td><td class='del'><a href='$url'>x</a></td></tr>";
}
$report.="</table>";
return $report;
}