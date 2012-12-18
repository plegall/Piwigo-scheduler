<?php
defined('SCHEDULER_PATH') or die('Hacking attempt!');

/**
 * add a tab on photo properties page
 */
function scheduler_tabsheet_before_select($sheets, $id)
{
  if ($id == 'photo')
  {
    $sheets['scheduler'] = array(
      'caption' => l10n('Scheduler'),
      'url' => SCHEDULER_ADMIN.'-photo&amp;image_id='.$_GET['image_id'],
      );
  }
  
  return $sheets;
}

/**
 * add a prefilter on photo edit page
 */
function scheduler_picture_edit_prefilter($content)
{
  $search = 'selected=$level_options_selected}';
  $replace = 'selected=$level_options_selected_scheduler}';

  return str_replace($search, $replace, $content);
}

function scheduler_photo_edit_replace_level()
{
  global $page, $template;

  if (!isset($_GET['page']))
  {
    return;
  }

  if ($_GET['page'] != 'photo')
  {
    return;
  }

  if (isset($_GET['tab']) and $_GET['tab'] != 'properties')
  {
    return;
  }
  
  // is the current photo on schedule?
  $query = '
SELECT *
  FROM '.SCHEDULER_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
;';
  $scheduler = pwg_db_fetch_assoc(pwg_query($query));

  if (!isset($scheduler['scheduled_for']))
  {
    return;
  }
    
  if (isset($_POST['submit']))
  {
    // we have to change the level in the scheduler table
    single_update(
      SCHEDULER_TABLE,
      array('level' => $_POST['level']),
      array('image_id' => $_GET['image_id'])
      );

    $scheduler['level'] = $_POST['level'];

    // then picture_modify.php will update IMAGES_TABLE to level 32 (=scheduled)
    $_POST['level'] = 32;
  }
  
  $template->set_prefilter('picture_modify', 'scheduler_picture_edit_prefilter');
    
  $template->assign('level_options_selected_scheduler', array($scheduler['level']));
}
?>