<?php
defined('SCHEDULER_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Basic checks and infos                                                |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];
$self_url = SCHEDULER_ADMIN.'-photo&amp;image_id='.$_GET['image_id'];

/* Initialisation */
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$picture = pwg_db_fetch_assoc(pwg_query($query));

$query = '
SELECT *
  FROM '.SCHEDULER_TABLE.'
  WHERE image_id = '.$picture['id'].'
;';
$scheduler = pwg_db_fetch_assoc(pwg_query($query));

if (isset($scheduler['scheduled_for']))
{
  $scheduler['scheduled_for'] = date('Y-m-d H:i', strtotime($scheduler['scheduled_for']));
}

// +-----------------------------------------------------------------------+
// | Photo[Scheduler] tab                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('photo'); // <= don't forget tabsheet id
$tabsheet->select('scheduler');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+

if (isset($_POST['scheduled_for']))
{
  $scheduled_for = date('Y-m-d H:i', strtotime($_POST['scheduled_for']));
  
  $level = $picture['level'];
  if (isset($scheduler['level']))
  {
    $level = $scheduler['level'];
  }
  
  $query = '
DELETE
  FROM '.SCHEDULER_TABLE.'
  WHERE image_id = '.$picture['id'].'
;';
  pwg_query($query);

  single_insert(
    SCHEDULER_TABLE,
    array(
      'image_id' => $picture['id'],
      'scheduled_for' => $scheduled_for,
      'level' => $level
      )
    );

  single_update(
    IMAGES_TABLE,
    array('level' => 32),
    array('id' => $picture['id'])
    );

  invalidate_user_cache();
  
  $_SESSION['page_infos'] = array(l10n('Photo informations updated'));
  redirect($self_url);
}

// +-----------------------------------------------------------------------+
// | Display page                                                          |
// +-----------------------------------------------------------------------+

/* Template */
$template->assign(array(
  'F_ACTION' => $self_url,
  'scheduler' => $conf['scheduler'],
  'TITLE' => render_element_name($picture),
  'TN_SRC' => DerivativeImage::thumb_url($picture),
  'SCHEDULED_FOR' => @$scheduler['scheduled_for'],
));

$template->set_filename('scheduler_content', realpath(SCHEDULER_PATH . 'admin/template/photo.tpl'));
?>