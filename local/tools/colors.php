<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$arElements = [];

$query = "SELECT *  FROM b_color ";
$obElm  = $DB->Query($query);
$upd_count = 0;
$updexist_count = 0;
$nexist_count = 0;
while($arElm = $obElm->GetNext())
{
    $FILE = CFile::GetFileArray($arElm['UF_FILE']);
    if ($FILE['FILE_SIZE'] > 3000)
    {
        print_r($FILE);
        echo $arElm['ID']."<br>";

        $upd_count ++;
    }
}

echo $upd_count;
