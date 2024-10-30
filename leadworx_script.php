<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: Leadworx - Script Installation
Description: Know who is visiting your website and convert anonymous web traffic to hot prospects. Leadworx’s entire suite is designed to fill your top of the funnel with high-quality prospects. With demos setup automatically, your sales team can do what it does best - close deals. 
Author: Leadworx
Author URI: https://www.leadworx.com/
Version: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
add_action('admin_menu', 'leadworx_script_page');
	function leadworx_script_page(){
	$page_title= "Leadworx - Script Installation";
    $menu_title= "Leadworx - Script Installation";
    $capability= "manage_options";
    $menu_slug= "leadworx_script";
    $function= "leadworx_script_page_load_function";
    $icon_url= plugin_dir_url( __FILE__  ).'images/logo-16x16-white.png';
    $position=1007; 			
	add_menu_page($page_title,$menu_title,$capability,$menu_slug,$function,$icon_url,$position);				
}

add_action( 'wp_ajax_authenticate_leadworx', 'authenticate_leadworx' );
add_action( 'wp_ajax_remove_leadworx_script', 'remove_leadworx_script' );
add_action( 'wp_ajax_add_website_script', 'add_website_script' );

global $url;
$url = 'https://leadworx.com/api/v1';
function authenticate_leadworx() {
	global $wpdb;
	global $url;
	$post_url_path  = '/codedrop/wpsite';
	$email = sanitize_email($_POST['leadworx_email']);
	$password = sanitize_text_field($_POST['leadworx_password']);
	$data = array('email' => $email, 'password' => $password);
	$response = wp_remote_post( $url.$post_url_path, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $data,
		'cookies' => array()
    ));
$rest = json_decode( $response["body"], true );
if($rest['error']){
   $res=0;
}else{
   $res="<select name='copyscriptinwphead' class='copyscriptinwphead'>";foreach($rest['result'] as $userData){$res .= "<option value=".$userData['tracking_script']." website_id=".$userData['uuid'].">".$userData['website_url']."</option>";}$res .= "</select>";
}
	echo $res;
	wp_die();
}
function remove_leadworx_script() {
	global $wpdb;
	global $url;
	$post_url_path  = '/codedrop/wpsite/remove';
	$addscripthead  = "";	
	update_option('addscript_custom_script_inheader',$addscripthead);
	update_option('addscript_custom_scripturl',$addscripthead);	
	$get_website_id = get_option('addscript_custom_scriptid');
	echo "coderemovesuccessfully"; 
	$wpscript_remove = false;
	$data = array('wpscript_remove' => $wpscript_remove,'website_id' => $get_website_id);
	$response = wp_remote_post( $url.$post_url_path, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $data,
		'cookies' => array()
    ));
$rest = json_decode( $response["body"], true );
update_option('addscript_custom_scriptid',$addscripthead);
	wp_die();
}
function add_website_script(){
	global $wpdb; 
	global $url;
	$post_url_path  = '/codedrop/wpsite/add';
	$scrpt = '<script src="//adpxl.co/{tracking_id}/an.js"></script><noscript><img src="//adpxl.co/{tracking_id}/spacer.gif" alt=""></noscript>'; 	  
	$copyscriptinwphead = sanitize_text_field($_POST["website_script"]);	   
	$website_url = esc_url_raw($_POST["website_url"]);
	$website_id  = sanitize_text_field($_POST["website_id"]);	   
	update_option('addscript_custom_scriptid',$website_id); 
	update_option('addscript_custom_scripturl',$website_url); 
	$find_n_replace = str_replace("{tracking_id}",$copyscriptinwphead,$scrpt);	 	 
	update_option('addscript_custom_script_inheader',stripslashes($find_n_replace));
	echo '<h4 class="show_website_url">Website - '.esc_html($website_url).'</h4><h4>'.esc_html('Script').'</h4><textarea class="addscripthead" name="addscripthead" required="required">'.esc_textarea($find_n_replace).'</textarea><input type="hidden" name="action" value="update" /><h4>Above script has been inserted into the <code>&lt;head&gt;</code> section of your site.</h4>'; 
	$addscript_customscript = $find_n_replace;
	$addscript_custom_scripturl = $website_url;
	$wpscript_add = true;
	$data = array('wpscript_add' => $wpscript_add,'website_id' => $website_id);
	$response = wp_remote_post( $url.$post_url_path, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $data,
		'cookies' => array()
    ));
$rest = json_decode( $response["body"], true );
	wp_die();
}
 
function leadworx_script_to_website_head() {
   $addscript_custom_script_inheader = get_option('addscript_custom_script_inheader');	
   	if($addscript_custom_script_inheader!=''){
		echo $addscript_custom_script_inheader;
	}
}

add_action('wp_head', 'leadworx_script_to_website_head');
if($_REQUEST['page']=='leadworx_script'){ 
function leadworx_script_page_load_function(){
if($_POST['action']=='update'){
	$addscripthead  = $_POST["addscripthead"];
	update_option('addscript_custom_script_inheader',stripslashes($addscripthead));
	$msg_update ="The Leadworx script has been successfully updated to your website"; 
}

if($_POST['action']=='insert_direct'){	  
	  $copyscriptinwphead = $_POST["addscripthead_direct"];	   
	  update_option('addscript_custom_script_inheader',stripslashes($copyscriptinwphead)); 	  
	  $msg_update ="The Leadworx script has been successfully added to your website"; 
}
$addscript_customscript = get_option("addscript_custom_script_inheader");
$addscript_custom_scripturl = get_option("addscript_custom_scripturl");
$plugin_dir_url =  plugin_dir_url( __FILE__  );

$handle = 'leadworx_custom_stylesheet';
$src = esc_url($plugin_dir_url.'css/leadworx_custom_stylesheet.css');
$deps = '';
$ver = '';
$media = 'all';
wp_enqueue_style( $handle, $src, $deps, $ver, $media );

$handle_script_min = 'jquery.validate.min';
$src_script_min = esc_url($plugin_dir_url.'script/jquery.validate.min.js');
$deps_script_min = '';
$ver_script_min = false;
$in_footer_min = false;
wp_enqueue_script( $handle_script_min, $src_script_min, $deps_script_min, $ver_script_min, $in_footer_min );

$handle_script = 'leadworx_custom_script';
$src_script = esc_url($plugin_dir_url.'script/leadworx_custom_script.js');
$deps_script = '';
$ver_script = false;
$in_footer = false;
wp_enqueue_script( $handle_script, $src_script, $deps_script, $ver_script, $in_footer );
?>
<div id="divLoading"></div>
<div class="wpcontent">
  <div class="wrap">
    <h2> <img src="<?php echo esc_url($plugin_dir_url.'images/logo-20x20.png');?>" alt="" style="padding:6px 5px 0 0; float:left;" /> Integrate Leadworx with your website <?php echo $icon_url;?></h2>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible" 
    style="display:<?php if($_REQUEST['action'] || $msg==1 || !empty($msg_update)){ echo 'block';}else{ echo 'none';}?>">
      <p><strong><?php if(!empty($msg_update)){ echo $msg_update;}?></strong></p>
      <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <div id="post-body-content">
      <div class="postbox">
        <div class="inside">
          <?php if(!empty($addscript_customscript) || !empty($addscript_custom_scripturl)){?>
          <form method="post" class="addscripthead_frm" autocomplete="off">
            <div class="codeAddedSuccessfully">
              <h4 class="show_website_url">
                <?php if($addscript_custom_scripturl){?>
                <?php echo esc_html("Website - ");?><?php echo esc_html($addscript_custom_scripturl);}?></h4>
              <h4><?php echo esc_html("Script");?></h4>
              <textarea class="addscripthead" name="addscripthead" required="required"><?php echo esc_textarea($addscript_customscript);?></textarea>
              <input type="hidden" name="action" value="update" />
              <h4><?php echo esc_html('Above script has been inserted into the <code>&lt;head&gt;</code> section of your site.');?></h4>
              <p>
                <input type="submit" name="submit" name="update_script" value="Update" class="button button-primary updatescript_btn" />
                <input type="button" id="remove_script" value="Remove" class="button button-primary" />
              </p>
            </div>
          </form>
          <?php }else{?>
          <div class="hideData">
            <div class="showScriptData" style="display:none">
              <form method="post" class="addscripthead_frm" autocomplete="off">
                <div class="codeAddedSuccessfullyAjax"> </div>
                <p>
                  <input type="submit" name="submit" name="update_script" value="Update" class="button button-primary updatescript_btn" />
                  <input type="button" id="remove_script" value="Remove" class="button button-primary" />
                </p>
              </form>
            </div>
            <div class="showWebsitesData">
              <form method='post' id="save_select_website_frm">
                <input type='hidden' name='action' value='insert' />
                <h4><?php echo esc_html("Select Website");?></h4>
                <div class="showWebsites"></div>
                <p>
                  <input type='button' name='save_select_website' value='Save' id='save_select_website' class='button button-primary' />
                </p>
              </form>
            </div>
          </div>
          <div class="showData">
            <h4><?php echo esc_html("Use your Leadworx's account credentials to login");?></h4>
            <form method="post" id="authenticate_leadworx_credentials" autocomplete="off">
              <h4><?php echo esc_html("Email");?></h4>
              <input type="email" name="leadworx_email" id="leadworx_email" required="required"/>
              <h4><?php echo esc_html("Password");?></h4>
              <input type="password" name="leadworx_password" id="leadworx_password" required="required" autocomplete="off" />
              <p class="padd-top5">
                <input type="button" name="leadworx_authenticate" value="Authenticate" class="button button-primary leadworx_authenticate mrg-top5" />
                <span class="showError"></span> </p>
            </form>
            <h4><?php echo esc_html("Or if you already have your code, paste it below");?></h4>
            <form method="post" id="addscripthead_direct_frm" autocomplete="off">
              <input type="hidden" name="action" value="insert_direct" />
              <textarea required="required" name="addscripthead_direct"></textarea>
              <h4><?php echo esc_html("Above script will be inserted into the <code>&lt;head&gt;</code> tag.");?></h4>
              <p>
                <input type="submit" name="submit" value="Save" class="button button-primary addscripthead_direct_btn" />
              </p>
            </form>
          </div>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php }}?>