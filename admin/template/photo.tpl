{combine_script id='jquery.timepicker' require='jquery.ui.datepicker,jquery.ui.slider' load='footer' path=$SCHEDULER_PATH|@cat:"admin/template/jquery-ui-timepicker-addon.js"}

{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.code`.js"}

{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-$lang_info.code" load='footer' path=$datepicker_language}
{/if}

{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_css path=$SCHEDULER_PATH|@cat:"admin/template/style.css"}

{footer_script require='jquery.timepicker'}{literal}
jQuery(document).ready(function() {
  jQuery('#scheduled_for').datetimepicker({
    minDate: 0, /* we can't schedule in the past */
    numberOfMonths: 3,
    dateFormat: "yy-mm-dd",
    timeText: '{/literal}{'selection'|@translate|escape:javascript}{literal}',
    hourText: '{/literal}{'Hour'|@translate|escape:javascript}{literal}',
    minuteText: '{/literal}{'Minute'|@translate|escape:javascript}{literal}',
    currentText: '{/literal}{'Now'|@translate|escape:javascript}{literal}',
    closeText: '{/literal}{'Validate'|@translate|escape:javascript}{literal}'
  });
});
{/literal}{/footer_script}

<style>
#scheduled_for {ldelim}
  background-image:url({$ROOT_URL}{$themeconf.admin_icon_dir}/datepicker.png);
  background-repeat:no-repeat;
  padding-left:20px;
  cursor:pointer;
}
</style>

<h2>{$TITLE} &#8250; {'Edit photo'|@translate} {$TABSHEET_TITLE}</h2>

<form action="{$F_ACTION}" method="post" id="catModify">
  <fieldset>
    <legend>{'Schedule photo'|@translate}</legend>

  <table>
    <tr>
      <td id="albumThumbnail">
        <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
      </td>
      <td style="vertical-align:top">

    <p>
      <strong>{'Schedule for'|@translate}</strong>
      <br>
      <input type="text" id="scheduled_for" name="scheduled_for" style="width:120px;" value="{$SCHEDULED_FOR}">
    </p>

    <p>
      <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="save_scheduler">
    </p>
    </td>
  </tr>
</table>
  </fieldset>
</form>