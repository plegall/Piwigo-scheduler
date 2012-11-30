<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * The installation function is called by main.inc.php and maintain.inc.php
 * in order to install and/or update the plugin.
 *
 * That's why all operations must be conditionned :
 *    - use "if empty" for configuration vars
 *    - use "IF NOT EXISTS" for table creation
 *
 * Unlike the functions in maintain.inc.php, the name of this function must be unique
 * and not enter in conflict with other plugins.
 */

function scheduler_install() 
{
  global $conf, $prefixeTable;
  
  // add a new table
	pwg_query('
CREATE TABLE IF NOT EXISTS `'. $prefixeTable .'scheduler` (
  `image_id` int(11) NOT NULL,
  `scheduled_for` datetime NOT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');
}

?>