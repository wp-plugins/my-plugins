<?php

if (!defined('ABSPATH'))
	die("Hello world!");

require_once( ABSPATH . "wp-includes/class-snoopy.php");

function matej_register2 ($what, $version){
  $register = "http://matej.nastran.net/register.php?what=$what&update=1&version=$version&url=".base64_encode(get_bloginfo('url'))."&name=".base64_encode(get_bloginfo('name'));
  $my_name = $what."_plugin_registered";
  $is_registered = get_option($my_name) == true;
  $snoopy = new Snoopy();
  if (!$is_registered)
  {
      $result = $snoopy->fetch($register);
      if($result) if ($snoopy->results == "OK")
            update_option($my_name, true);
  }

}

function matej_info ()
{
  $snoopy = new Snoopy();
  $info_url = "http://matej.nastran.net/register.php?info=1";
  $result = $snoopy->fetch($info_url);
  if($result)
      $info = $snoopy->results;
  else
  	 $info = "";
  	?>
        <div class="wrap">
        	   <?php echo $info; ?>
        </div>
    <?php
}

?>