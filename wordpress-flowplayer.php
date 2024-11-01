<?php

/*
Plugin Name: wordpress-flowplayer
Version: 0.3
Plugin URI: http://www.jpegserv.com/
Description: This plugin uses flowplayer to enable you to display video media on your blog.
Author: Lee Ferrett
Author URI: http://www.jpegserv.com/
Update:
*/

// SCRIPT INFO ///////////////////////////////////////////////////////////////////////////

/*
	wordpress-flowplayer for Wordpress
	(C) 2007 Lee Ferrett  - GNU General Public License

	About this plugin:
		This plugin uses flow player which is available from http://flowplayer.org/, this plugin 
		includes version 3 of the player and will be updated when a new version comes out. the code for
		this plugin is based on the WP-FLV plugin which is available from 
		http://roel.meurders.nl/wordpress-plugins/wp-flv-video-player-plugin/.

	This Wordpress plugin is released under a GNU General Public License. A complete version of this license
	can be found here: http://www.gnu.org/licenses/gpl.txt

	This Wordpress plugin has been tested with Wordpress 1.6.5;

	This Wordpress plugin is released "as is". Without any warranty. The author cannot
	be held responsible for any damage that this script might cause.

*/

// NO EDITING HERE!!!!! ////////////////////////////////////////////////////////////////

### Use WordPress 2.6 Constants
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}






	

add_action('admin_menu', 'wpflow_admin_menu');
function wpflow_admin_menu(){
	add_options_page('wordpress-flowplayer, options page', 'wordpress-flowplayer', 9, basename(__FILE__), 'wpflow_options_page');
}

add_action('wp_head', 'wpflow_header');
function wpflow_header(){
	echo '';
	echo "\n<!-- Begin wordpress-flowplayer -->\n\t";
	echo '<script type="text/javascript" src="'.WP_PLUGIN_URL.'/wordpress-flowplayer/flowplayer-3.0.0.min.js"></script>';
	echo "\n<!-- End wordpress-flowplayer -->\n\n";
}

add_filter('the_content', 'wpflow_replace', '1');
function wpflow_replace($content){
	$o = wpflow_get_options();
	
	global $post;
	$flowplayer = WP_PLUGIN_URL."/wordpress-flowplayer/flowplayer-3.0.0.swf";
	$flowVars = array("PLAYER", "HREF", "WIDTH", "HEIGHT", "POSTID");
	$flowVals = array($flowplayer, '', $o['width'], $o['height'], $post->ID);
		
		// build the output html.
		$flowCode = "<a class=\"player plain\" id=\"postnum-%POSTID%\">";
		$flowCode .= "<embed src=\"%PLAYER%\" "; 
		$flowCode .= "allowfullscreen=\"true\" ";
		$flowCode .= "allowscriptaccess=\"always\" ";
		$flowCode .= "quality=\"high\" ";
		$flowCode .= "type=\"application/x-shockwave-flash\" ";
		$flowCode .= "pluginspage=\"http://www.adobe.com/go/getflashplayer\" ";
		$flowCode .= "id=\"postnum-%POSTID%\" ";
		$flowCode .= "bgcolor=\"#000000\" ";
		$flowCode .= "name=\"postnum-%POSTID%\" ";
		// flash vars line

		//config={
		//"clip":{
		//	"url":"http://example.com",
		//	"autoPlay":false
		//},
		//	"playerId":"postnum-0",
		//	"playlist":[{
		//	"url":"http://example.com",
		//	"autoPlay":false
		//	}]
		//}

		$flowCode .= "flashvars=\"";
		$flowCode .= "config={&quot;clip&quot;:{";
			$flowCode .= "&quot;url&quot;:&quot;%HREF%&quot;";
			$flowCode .= ",&quot;autoPlay&quot;:false";
		$flowCode .= "},";
			$flowCode .= "&quot;playerId&quot;:&quot;postnum-%POSTID%&quot;,";
			$flowCode .= "&quot;playlist&quot;:[{";
			$flowCode .= "&quot;url&quot;:&quot;%HREF%&quot;,";
			$flowCode .= "&quot;autoPlay&quot;:false";
			$flowCode .= "}]";
		$flowCode .= "}\" ";

		// end of the flash vars line
		$flowCode .= "width=\"%WIDTH%px\" ";
		$flowCode .= "height=\"%HEIGHT%px\">";
		$flowCode .= "
			</a>
			<script language=\"javascript\">
			$f(\"postnum-%POSTID%\", \"%PLAYER%\", {
			clip: {
				url: '%HREF%',
				autoPlay: false
			} 
			});
				</script>";

		preg_match_all ('!\[flow(.*?)\]!i', $content, $matches);

		$flowStrings = $matches[0];
		$flowAttributes = $matches[1];
		for ($i = 0; $i < count($flowAttributes); $i++){
			preg_match_all('!(href|width|height)="([^"]*)"!i',$flowAttributes[$i],$matches);
			$tmp = $flowCode;
			$flowSetVars = $flowSetVals = array();
			for ($j = 0; $j < count($matches[1]); $j++){
				$flowSetVars[$j] = strtoupper($matches[1][$j]);
				$flowSetVals[$j] = $matches[2][$j];
			}
			for ($j = 0; $j < count($flowVars); $j++){
				$key = array_search($flowVars[$j], $flowSetVars);
				$val = (is_int($key)) ? $flowSetVals[$key] : $flowVals[$j];
				if ($flowVars[$j] == 'HEIGHT')
					$val = intval($val);
				$tmp = str_replace('%'.$flowVars[$j].'%', $val, $tmp);
			}
			$content = str_replace($flowStrings[$i], "\n\n".$o['prehtml'].$tmp.$o['posthtml']."\n\n", $content);
		}
		return $content;
	}


	function wpflow_get_options(){
		$defaults = array();
		$defaults['prehtml'] = '<div class="flowPlayer">';
		$defaults['posthtml'] = '</div>';
		$defaults['videourl'] = '/uploads';
		$defaults['width'] = '320';
		$defaults['height'] = '240';

		$options = get_option('rmnlFLOWsettings');
		if (!is_array($options)){
			$options = $defaults;
			update_option('rmnlFLOWsettings', $options);
		}

		return $options;
	}


	function wpflow_options_page(){
		if ($_POST['wpflow']){
			$_POST['wpflow']['prehtml'] = stripslashes($_POST['wpflow']['prehtml']);
			$_POST['wpflow']['posthtml'] = stripslashes($_POST['wpflow']['posthtml']);
			update_option('rmnlFLOWsettings', $_POST['wpflow']);
			$message = '<div class="updated"><p><strong>Options saved.</strong></p></div>';
		}

		$o = wpflow_get_options();

		echo <<<EOT
		<div class="wrap">
			<h2>wordpress flowplayer Options</h2>
			{$message}
			<form name="form1" method="post" action="options-general.php?page=wp-flow.php">	
			<fieldset class="options">
				<p>To make optimal use of the wordpress flowplayer plugin you can set the options below.</p>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
					<tr valign="top">
						<th>Default URL to video files</th>
						<td><input type="text" value="{$o['videourl']}" name="wpflow[videourl]" size="50" /></td>
					</tr>
					<tr valign="top">
						<th>Default movie size (W x H)</th>
						<td>
							<input type="text" value="{$o['width']}" name="wpflow[width]" size="3" maxlength="4" /> x
							<input type="text" value="{$o['height']}" name="wpflow[height]" size="3" maxlength="4" />
						</td>
					</tr>
					<tr valign="top">
						<th>(X)HTML to be placed before each player</th>
						<td><textarea name="wpflow[prehtml]" rows="3" cols="50">{$o['prehtml']}</textarea></td>
					</tr>
					<tr valign="top">
						<th>(X)HTML to be placed after each player</th>
						<td><textarea name="wpflow[posthtml]" rows="3" cols="50">{$o['posthtml']}</textarea></td>
					</tr>
				</table>
			</fieldset>
			<p class="submit">
				<input type="submit" name="Submit" value="Update Options &raquo;" />
			</p>
			</form>
		</div>
EOT;
	}

	if(strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php')) {

		add_action('admin_footer', 'flowAddQuicktag');

		function flowAddQuickTag(){
			$o = wpflow_get_options();

				echo <<<EOT
				<script type="text/javascript">
					<!--
						var flowToolbar = document.getElementById("ed_toolbar");
						if(flowToolbar){
							var flowNr = edButtons.length;
							//edButtons[edButtons.length] = new edButton('ed_popin','','','</a>','');
							edButtons[edButtons.length] = new edButton('ed_flow','','','','');
							var flowBut = flowToolbar.lastChild;
							while (flowBut.nodeType != 1){
								flowBut = flowBut.previousSibling;
							}
							flowBut = flowBut.cloneNode(true);
							flowToolbar.appendChild(flowBut);
							//toolbar.appendChild(flowBut);
							flowBut.value = 'Flow';
							flowBut.onclick = edInsertFlow;
							flowBut.title = "Insert a Flash Video";
							flowBut.id = "ed_flow";
						}

						function edInsertFlow() {
							if(!edCheckOpenTags(flowNr)){
								var U = prompt('Give the Url of the Flash Video File' , '{$o["videourl"]}');
								var W = prompt('Give the width of this video' , '{$o["width"]}');
								var H = prompt('Give the width of this video' , '{$o["height"]}');
								var theTag = '[flow href="' + U + '" width="' + W + '" height="' + H + '"]';
								edButtons[flowNr].tagStart  = theTag;
								edInsertTag(edCanvas, flowNr);
							} else {
								edInsertTag(edCanvas, flowNr);
							}
						}

					//-->
				</script>
EOT;
			
		}
	}

### Function: Displays wordpress flowplayer Header In WP-Admin

add_action('admin_head', 'wpflow_header_admin');
function wpflow_header_admin() {
	wp_register_script('wpflow-admin', WP_PLUGIN_URL.'/wordpress-flowplayer/wpflow-admin-js.js', false, '0.3');
	echo "\n".'<!-- Start Of Script Generated By wordpress-flowplayer 0.3 -->'."\n";
	echo '<script type="text/javascript">'."\n";
	echo '/* <![CDATA[ */'."\n";
	echo "\t".'var flow_admin_text_enter_flow_href = \''.js_escape(__('Enter Video Url', 'wpflow')).'\';'."\n";
	echo "\t".'var flow_admin_text_enter_flow_width = \''.js_escape(__('Enter Video Width', 'wpflow')).'\';'."\n";
	echo "\t".'var flow_admin_text_enter_flow_height = \''.js_escape(__('Enter Video Height', 'wpflow')).'\';'."\n";
	echo '/* ]]> */'."\n"; 
	echo '</script>'."\n";
	wp_print_scripts(array('sack', 'wpflow-admin'));
	echo '<!-- End Of Script Generated By wordpress-flowplayer 0.3 -->'."\n";
}


### Function: Add Quick Tag wordpress flowplayer In TinyMCE >= WordPress 2.5

add_action('init', 'wpflow_tinymce_addbuttons');
function wpflow_tinymce_addbuttons() {
	if(!current_user_can('edit_posts') && ! current_user_can('edit_pages')) {
		return;
	}
	if(get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "wpflow_tinymce_addplugin");
		add_filter('mce_buttons', 'wpflow_tinymce_registerbutton');
	}
}
function wpflow_tinymce_registerbutton($buttons) {
	array_push($buttons, 'separator', 'wpflow');
	return $buttons;
}
function wpflow_tinymce_addplugin($plugin_array) {
	$plugin_array['wpflow'] = WP_PLUGIN_URL.'/wordpress-flowplayer/tinymce/editor_plugin.js';
	return $plugin_array;
}

?>