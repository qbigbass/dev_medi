<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обновление MRObuv");
if (!$USER->IsAuthorized())
 die();

 $groups = $USER->GetUserGroupArray();

 if (!in_array(1, $groups) && !in_array(8, $groups)){

     LocalRedirect("/");die;
 }


$HLBLOCK_ID = 17;

$query = "SELECT count(*) as filloff FROM b_mrobuv WHERE UF_OFFER_ID > 0";
$query2 = "SELECT count(*) as alloff FROM b_mrobuv ";
$countFill  = $DB->Query($query);
$countOffers = $countFill->Fetch();

$countAll  = $DB->Query($query2);
$countAllOffers = $countAll->Fetch();

// Получаем список свойств для обновления
// 345  - Я.Маркет   114 - Бренд   154 - Похожие   152 - Сопутствующие

?>
<style>
.tool_notice {padding: 1em;margin-bottom: 2em;}
.tool_notice.error {
    border: 1px solid #e20074;
    background: #fff5fa;
    color: #e20074;
}
.tool_notice.alert {
    border: 1px solid #38e200;
    background: #f7fff5;
    color: #03e200;
}
</style>
<div class="container">
    <?
    if (isset($_SESSION['tools'])){?>
    <div class=" tool_notice <?=($_SESSION['tools']['alert'] ? 'alert' : 'error')?>">
        <?=$_SESSION['tools']['alert']?>
        <?=$_SESSION['tools']['error']?>
    </div>
    <?
    unset($_SESSION['tools']);
    }?>
    <form method="post" action="">

    <input type="hidden" name="action" value="update"/>
    <div class="row">
        <div class="ten  columns">
            Кол-во записей с установленным OfferID: <b><?=$countOffers['filloff']?></b>  &nbsp;&nbsp;&nbsp;  Всего записей: <b><?=$countAllOffers['alloff']?></b>

        </div>
    </div>
    <div class="row">
        <div class="five columns">
            <br><br>
            <input type="submit" name="mark" value="Обновить" class="button button-primary" onsubmit="return confirm('Запустить обновление?');"/>
        </div>

    </div>

    </form>
</div>

<?
if ($_REQUEST['action'] == 'update')
{
    $start = microtime(true);
    CModule::IncludeModule("iblock");
    $per_page = 50;
    $page = 0;

    if (isset($_REQUEST['page']))
    {
        $page = intval($_REQUEST['page']);
    }
    $from = $page * $per_page;
    if ($from > $countAllOffers['alloff'])
    {

        $_SESSION['tools']['alert'] = 'Обновление завершено';
        LocalRedirect("/local/tools/shoes_upd.php");die;
    }
    $query = "SELECT ID, UF_NAME, UF_OFFER_ID  FROM b_mrobuv limit $from, $per_page";
    $obElm  = $DB->Query($query);
    $upd_count = 0;
    $updexist_count = 0;
    $nexist_count = 0;
    while($arElm = $obElm->GetNext())
    {
        if (!empty($arElm['UF_NAME'])) {

            $obElement = CIBlockElement::GetList(['ID'=> 'asc'], ['IBLOCK_ID' => 19, '=PROPERTY_CML2_ARTICLE'=>$arElm['UF_NAME'], false, false, ['ID']]);
            if ($arElement=$obElement->GetNext())
            {
                if ($arElm['UF_OFFER_ID'] != $arElement['ID'])
                {
                    $arFields = [];
                    $arFields['UF_OFFER_ID'] = intval($arElement['ID']);

                    $DB->Update("b_mrobuv", $arFields, "WHERE ID='".$arElm['ID']."'", $err_mess.__LINE__);


                    $upd_count++;
                }
                else {
                    $updexist_count++;
                }
            }
            else {
                if (intval($arElm['UF_OFFER_ID']) > 0)
                {
                    $arFields = [];
                    $arFields['UF_OFFER_ID'] = '';
                    $DB->Update("b_mrobuv", $arFields, "WHERE ID='".$arElm['ID']."'", $err_mess.__LINE__);
                }
                $nexist_count++;

            }
        }
    }
    $page++;
    $_SESSION['tools']['alert'] = 'Записи с '.$from.' по '.($from+$per_page).' <br><br>Обновлено: '.$upd_count.'<br>Уже установлен: '.$updexist_count.'<br>'.'Не найдено SKU: '.$nexist_count. '<br> Время  '.(microtime(true) - $start);
    echo '<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=/local/tools/shoes_upd.php?action=update&page='.$page.'">';
    //LocalRedirect("/local/tools/shoes_upd.php?action=update&page=".$page);die;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
