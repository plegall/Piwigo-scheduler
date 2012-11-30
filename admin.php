<?php
/**
 * This is the main administration page, if you have only one admin page you can put 
 * directly its code here or using the tabsheet system like bellow
 */

defined('SCHEDULER_PATH') or die('Hacking attempt!');
 
global $template, $page, $conf;


// get current tab
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'home';

// plugin tabsheet is not present on photo page
if ($page['tab'] != 'photo')
{
  // tabsheet
  include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
  $tabsheet = new tabsheet();
  $tabsheet->set_id('scheduler');
  
  $tabsheet->add('home', l10n('Welcome'), SCHEDULER_ADMIN . '-home');
  $tabsheet->add('config', l10n('Configuration'), SCHEDULER_ADMIN . '-config');
  $tabsheet->select($page['tab']);
  $tabsheet->assign();
}

// include page
include(SCHEDULER_PATH . 'admin/' . $page['tab'] . '.php');

// template vars
$template->assign(array(
  'SCHEDULER_PATH'=> get_root_url() . SCHEDULER_PATH, // used for images, scripts, ... access
  'SCHEDULER_ABS_PATH'=> realpath(SCHEDULER_PATH),    // used for template inclusion (Smarty needs a real path)
  'SCHEDULER_ADMIN' => SCHEDULER_ADMIN,
  ));
  
// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'scheduler_content');

?>