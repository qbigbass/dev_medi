<?php
/**
 * @copyright Copyright &copy; �������� MEAsoft, 2014
 */

IncludeModuleLangFile(__FILE__);
?>
<link href="/bitrix/components/measoft.courier/css/jquery-ui.css" type="text/css"  rel="stylesheet" />
<table id="ms_courier" onclick="return false;" style="display:block;">
    <tr>
        <td><?php print GetMessage("MEASOFT_FIELDS_DATE_PUTN")?>:</td>
        <td width="150">
            <input type="text" name="MS_DATE_PUTN" value="<?php print $_REQUEST["MS_DATE_PUTN"] ? htmlspecialcharsbx($_REQUEST["MS_DATE_PUTN"]) : date('d.m.Y'); ?>" class="ms_date_putnClass" id="ms_date_putn"/>
            <br><?php print GetMessage("MEASOFT_HINTS_DATE_PUTN")?>
        </td>
    </tr>
    <tr>
        <td><?php print GetMessage("MEASOFT_FIELDS_TIME_MIN")?>:</td>
        <td>
            <select name="MS_TIME_MIN" id="ms_time_min" />
                <?php for ($i = 6; $i < 22; $i++) {
                    print '<option value="'.($i < 10 ? '0' : '').$i.':00"'.($i == 9 ? ' selected' : '').'>'.$i.':00</value>';
                } ?>
            </select>
            <br><?php print GetMessage("MEASOFT_HINTS_TIME_MIN")?>
        </td>
    </tr>
    <tr>
        <td><?php print GetMessage("MEASOFT_FIELDS_TIME_MAX")?>:</td>
        <td>
            <select name="MS_TIME_MAX" id="ms_time_max" />
                <?php for ($i = 6; $i < 22; $i++) {
                    print '<option value="'.($i < 10 ? '0' : '').$i.':00"'.($i == 18 ? ' selected' : '').'>'.$i.':00</value>';
                } ?>
            </select>
            <!--<input type="text" name="MS_TIME_MAX" value="<?php print $_REQUEST["MS_TIME_MAX"] ? htmlspecialcharsbx($_REQUEST["MS_TIME_MAX"]) : '18:00'; ?>" id="ms_time_max"/>-->
            <br><?php print GetMessage("MEASOFT_HINTS_TIME_MAX")?>
        </td>
    </tr>
</table>