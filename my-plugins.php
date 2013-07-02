<?php
/*
Plugin Name: My-Plugins
Plugin URI: http://wordpress.org/extend/plugins/my-plugins/
Description: Displays all the plugins - just insert [my plugins] in post where you want to display a table of plugins that you use. .
Version: 0.3
Author: Matej Nastran
Author URI: http://matej.nastran.net/
*/
/*  Copyright 2013  Matej Nastran (email : matej@nastran.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$my_plugins_version = "0.3";

if (!function_exists('add_filter'))
	die ("Hello World!");


if (!function_exists('get_plugins'))
				require_once (ABSPATH."wp-admin/includes/plugin.php");

add_filter('the_content', 'matej_plugins');
add_filter('the_head', 'matej_plugins_head');


function matej_plugins_head () {
			global $plugin_css_displayed;
			if (is_feed())
				return;
			$plugin_css_displayed = true;
			?>
<style type="text/css" media="screen">

table.plugins {
	border-width: 1px 1px 1px 1px;
	border-style: outset outset outset outset;
	border-color: gray gray gray gray;
	border-collapse: collapse;
}
table.plugins th {
	border-width: 1px 1px 1px 1px;
	border-style: inset inset inset inset;
	border-color: gray gray gray gray;
}
table.plugins td {
	border-width: 1px 1px 1px 1px;
	border-style: inset inset inset inset;
	border-color: gray gray gray gray;
}


			
.widefat {
	width: 100%;
}

.widefat td, .widefat th {
	padding: 2px 2px;
}

.widefat th {
	text-align: left;
}

.plugins p {
	margin: 2px;
	padding: 0;
}

table .name {
	text-align: center;
	font-weight: bold;
}

.plugins .name {
	font-size: 14px;
}
td {
}
.alternate {
	background: #f1f1f1;
}

table .vers {
	text-align: center;
}
.active td {
	background: #BEB;
}
.active .name {
	background: #9C9;
}
.alternate.active td {
	background: #ADA;
}
.alternate.active .name {
	background: #8B8;
}

</style>			
			
			<?php
}


function matej_plugins ($content) {
			global $post, $plugin_css_displayed;
			
			$tag = "[my plugins]";
			if (!strstr($content, $tag))
				return $content;
         $plugins = get_plugins ();				
			if (!is_single()){
				echo "Ker je seznam pluginov kar precej dolg (".sizeof($plugins)." jih je), <a href=\"".$post->guid."\">klikni tu</a>, če te zanima...";
				return;
			}
			if (!$plugin_css_displayed)			
				matej_plugins_head();	
			if (empty($plugins)) {
				echo '<p>';
				_e("Couldn&#8217;t open plugins directory or there are no plugins available."); // TODO: make more helpful
				echo '</p>';
			} else {
			ob_start();
			$i = 1;
			?>
			<table class="widefat plugins">
				<thead>
				<tr>
					<th style="text-align: center">Nr.</th>
					<th style="text-align: center"><?php _e('Plugin'); ?></th>
					<th style="text-align: center"><?php _e('Version'); ?></th>
					<th style="text-align: center"><?php _e('Description'); ?></th>
				</tr>
				</thead>
			<?php
				$style = '';
			
				foreach($plugins as $plugin_file => $plugin_data) {
					$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';
			
					if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
						$toggle = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
						$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
						$style .= $style == 'alternate' ? ' active' : 'active';
					} else {
						$toggle = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
					}
			
					$plugins_allowedtags1 = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
					$plugins_allowedtags2 = array('abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
			
					// Sanitize all displayed data
					$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags1);
					$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags1);
					$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags2);
					$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags1);
					//$plugin_data['Description'] = preg_replace ("@<a .*?".">(.*?)</a>@mis", "\\1", $plugin_data['Description']); 
			
					if ( $style != '' )
						$style = 'class="' . $style . '"';
					if ( is_writable(ABSPATH . PLUGINDIR . '/' . $plugin_file) )
						$edit = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."' class='edit'>".__('Edit')."</a>";
					else
						$edit = '';
			
					$author = ( empty($plugin_data['Author']) ) ? '' :  ' <cite><br />' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';
			
					echo "
				<tr $style>
					<td class='name'>$i.</td>
					<td class='name'>{$plugin_data['Title']}</td>
					<td class='vers'>{$plugin_data['Version']}</td>
					<td class='desc'><p>{$plugin_data['Description']}$author</p></td>
					</tr>";
				 //do_action( 'after_plugin_row', $plugin_file );
				 $i++;
				}
			?>
			</table>
			<br />Output generated by <em><a href="http://wordpress.org/extend/plugins/my-plugins/">My-Plugins</a></em> plugin by Matej Nastran</a>.<br /><br /> 
<?php 			
        $ret = ob_get_contents();
        ob_end_clean ();
         return str_replace ($tag, $ret, $content);
     }
}
?>