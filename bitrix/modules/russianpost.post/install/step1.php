<?IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.donation/install/index.php");?>
<?
if( is_array($russianpost_post_global_errors)&&count($russianpost_post_global_errors)>0 )
{
    foreach ($russianpost_post_global_errors as $val)
    {
        $alErrors .= $val."<br>";
    }
    echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE"=>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
}
else
{
    echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));

    ?>
    <p><a href="settings.php?lang=<?=LANG?>&amp;mid_menu=2&amp;mid=russianpost.post"><?=GetMessage("RUSSIANPOST_POST_SETTINGS_PAGE" )?></a></p>
    <?
}
?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>