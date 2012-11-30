<?php
/*
Plugin Name: Scheduler
Version: auto
Description: Schedule the availability of photos.
Plugin URI: auto
Author: plg
Author URI: http://piwigo.org
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $prefixeTable;

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

defined('SCHEDULER_ID') or define('SCHEDULER_ID', basename(dirname(__FILE__)));
define('SCHEDULER_PATH' ,   PHPWG_PLUGINS_PATH . SCHEDULER_ID . '/');
define('SCHEDULER_TABLE',   $prefixeTable . 'scheduler');
define('SCHEDULER_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . SCHEDULER_ID);
define('SCHEDULER_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'scheduler')) . '/');
define('SCHEDULER_DIR',     PWG_LOCAL_DIR . 'scheduler/');
define('SCHEDULER_VERSION', 'auto');

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+

// init the plugin
add_event_handler('init', 'scheduler_init');

// publish scheduled photos
add_event_handler('init', 'scheduler_init_publish');

if (defined('IN_ADMIN'))
{
  // new tab on photo page
  add_event_handler('tabsheet_before_select', 'scheduler_tabsheet_before_select', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

  // replace level for display on photo edition main page
  add_event_handler('loc_begin_admin_page', 'scheduler_photo_edit_replace_level');

  // file containing all previous handlers functions
  include_once(SCHEDULER_PATH . 'include/admin_events.inc.php');
}

// files containing specific plugin functions
include_once(SCHEDULER_PATH . 'include/functions.inc.php');

/**
 * plugin initialization
 *   - check for upgrades
 *   - unserialize configuration
 *   - load language
 */
function scheduler_init()
{
  global $conf, $pwg_loaded_plugins;
  
  // apply upgrade if needed
  if (
    SCHEDULER_VERSION == 'auto' or
    $pwg_loaded_plugins[SCHEDULER_ID]['version'] == 'auto' or
    version_compare($pwg_loaded_plugins[SCHEDULER_ID]['version'], SCHEDULER_VERSION, '<')
  )
  {
    // call install function
    include_once(SCHEDULER_PATH . 'include/install.inc.php');
    scheduler_install();
    
    // update plugin version in database
    if ( $pwg_loaded_plugins[SCHEDULER_ID]['version'] != 'auto' and SCHEDULER_VERSION != 'auto' )
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. SCHEDULER_VERSION .'"
WHERE id = "'. SCHEDULER_ID .'"';
      pwg_query($query);
      
      $pwg_loaded_plugins[SCHEDULER_ID]['version'] = SCHEDULER_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'Scheduler updated to version '. SCHEDULER_VERSION;
      }
    }
  }
  
  // load plugin language file
  load_language('plugin.lang', SCHEDULER_PATH);
  
  // prepare plugin configuration
  $conf['scheduler'] = unserialize($conf['scheduler']);
}

function scheduler_init_publish()
{
  $query = '
SELECT *
  FROM '.SCHEDULER_TABLE.'
  WHERE scheduled_for <= NOW()
;';
  $result = pwg_query($query);

  $updates = array();
  $scheduler_delete_ids = array();
  
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push(
      $updates,
      array(
        'id' => $row['image_id'],
        'date_available' => $row['scheduled_for'],
        'level' => $row['level'],
        )
      );

    $scheduler_delete_ids[] = $row['image_id'];
  }

  if (count($scheduler_delete_ids) > 0)
  {
    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update' => array_keys($updates[0])
        ),
      $updates
      );

    $query = '
DELETE
  FROM '.SCHEDULER_TABLE.'
  WHERE image_id IN ('.implode(',', $scheduler_delete_ids).')
;';
    pwg_query($query);

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    invalidate_user_cache();
  }
}

?>