<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$groups = $USER->GetUserGroupArray();
?>

<div id="nav_start"></div> <? // Якорь для навигации, нельзя кастомизировать           ?>
<div class="order-list">
    <div class="order-list__filter">
        <form name="form1" method="GET" action="<?= $APPLICATION->GetCurPageParam("",
            array("sessid", "delete", "del_id", "action",
                "find_MEDI_ORTO_visit_date_USER_text",
                "find_MEDI_ORTO_visit_date_USER_text_submit",
                "find_MEDI_ORTO_delivery_date_USER_text",
                "find_MEDI_ORTO_delivery_date_USER_text_submit",
                "find_MEDI_ORTO_contractor_USER_text",
                "find_MEDI_ORTO_type_ANSWER_TEXT_dropdown",
                "set_filter"

            ), false) ?>" class="order-list__filter-form">
            <input type="hidden" name="find_MEDI_ORTO_delivery_date_USER_text" value=""/>
            <input type="hidden" name="find_MEDI_ORTO_visit_date_USER_text" value=""/>
            <input type="hidden" name="WEB_FORM_ID" value="<?= $arParams["WEB_FORM_ID"] ?>">
            <? if ($arParams["SEF_MODE"] == "N"): ?>
                <input type="hidden" name="action" value="list" />
            <? endif ?>

            <div class="container">
                <div class="row">
                    <div class="two columns">
                        <label for="order-form__type" class="order-form__label--truncate">Тип заявки:</label>
                        <select id="order-form__type" class="u-full-width" name="find_MEDI_ORTO_type_ANSWER_TEXT_dropdown">
                            <option value="" <?= (empty($arResult['__find']['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown']) ? 'selected': ''); ?>>Любой</option>
                            <?if (in_array(15, $groups) || in_array(16, $groups)  || in_array(1, $groups)){?>
                            <option value="102" <?= ($arResult['__find']['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 102 ? 'selected': ''); ?>>Визит к клиенту</option>
                            <option value="103" <?= ($arResult['__find']['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 103 ? 'selected': ''); ?>>Визит в ЛПУ</option>
                            <?}?>
                            <?if (in_array(17, $groups) || in_array(1, $groups)){?>
                            <option value="104" <?= ($arResult['__find']['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 104 ? 'selected': ''); ?>>Сетевые продажи</option>
                            <?}?>
                        </select>
                    </div>

                   <div class="two columns">
                        <label for="order-form__status" class="order-form__label--truncate">Статус:</label>
                        <select id="order-form__status" class="u-full-width" name="find_status_id">
                            <option value="" <?= (empty($arResult['__find']['find_status_id']) ? 'selected': ''); ?>>Любой</option>
                            <option value="4" <?= ($arResult['__find']['find_status_id'] == 4 ? 'selected': ''); ?>>В работе</option>
                            <option value="5" <?= ($arResult['__find']['find_status_id'] == 5 ? 'selected': ''); ?>>Выполнена</option>
                            <option value="6" <?= ($arResult['__find']['find_status_id'] == 6 ? 'selected': ''); ?>>Отменена</option>
                        </select>
                    </div>

                    <div class="two columns" id="filter_delivery_date">
                        <label for="order-form__delivery-date" class="order-form__label--truncate">Дата выезда:</label>
                        <input id="order-form__delivery-date" class="u-full-width datepicker" type="text"  name="find_MEDI_ORTO_delivery_date_USER_text" value="<?= !empty($arResult['__find']['find_MEDI_ORTO_delivery_date_USER_text']) ? $arResult['__find']['find_MEDI_ORTO_delivery_date_USER_text'] : ''; ?>">
                    </div>
                    <div class="two columns" id="filter_visit_date">
                        <label for="order-form__visit-date" class="order-form__label--truncate">Дата
                            визита:</label>
                        <input id="order-form__visit-date" class="u-full-width datepicker" type="text"  name="find_MEDI_ORTO_visit_date_USER_text" value="<?= !empty($arResult['__find']['find_MEDI_ORTO_visit_date_USER_text']) ? $arResult['__find']['find_MEDI_ORTO_visit_date_USER_text'] : ''; ?>">
                    </div>
                    <div class="two columns">
                        <label for="order-form__contractor" class="order-form__label--truncate">Исполнитель:</label>
                        <select id="order-form__contractor" class="u-full-width"
                                name="find_MEDI_ORTO_contractor_USER_text">
                            <option value="">ВСЕ</option>
                            <? foreach ($arResult['ContrInfo'] as $arOption): ?>
                                <option value="<?= $arOption['NAME'] ?>" <?=($arOption['SELECTED'] == 'Y' ? ' selected' : '' );?>><?= $arOption['NAME']; ?></option>
                            <? endforeach; ?>
                        </select>

                    </div>
                    <div class="two columns" id="filter_doctor_name">
                        <label for="order-form__doctor_name" class="order-form__label--truncate">Врач:</label>
                        <input id="order-form__doctor_name" class="u-full-width " type="text"  name="find_MEDI_ORTO_doctor_name_USER_text" value="<?= !empty($arResult['__find']['find_MEDI_ORTO_doctor_name_USER_text']) ? $arResult['__find']['find_MEDI_ORTO_doctor_name_USER_text'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="four columns">
                        <label>&nbsp;</label>
                        <button type="submit" class="button button-primary" name="set_filter" value="<?= GetMessage("FORM_F_SET_FILTER"); ?>">Найти</button>
                        <input type="hidden" name="set_filter" value="Y"/>
                        <button type="submit" class="button button-secondary" name="del_filter" value="<?= GetMessage("FORM_F_DEL_FILTER"); ?>">Сбросить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container">
        <div class="row">
            <div class="twelve column">
                <table class="u-full-width">
                    <thead>
                        <tr>
                            <th>Тип</th>
                            <th>Номер</th><th>Кто оформил</th><th>Исполнитель</th><?if($_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 104 || $_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 102 ||  (in_array(17, $groups) && !$_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown']) || (in_array(1, $groups) && !$_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'])):?><th>Врач</th><?endif;?><th>Статус</th><th>Дата
                                и время</th>
                        </tr>
                    </thead>
                    <?
                    if (!empty($arResult['arrResults'])):
                        $str_count = 0;?>
                        <? foreach ($arResult['arrResults'] as $arOrder): ?>

                            <?// Проверка доступа к Сетевым продажам
                            if (!in_array(17, $groups) && !in_array(1, $groups) && $arResult['arrAnswers'][$arOrder['ID']][75][104]) {
                                continue;
                            }
                            //print_r($groups;)
                            if ((!in_array(16, $groups) && !in_array(15, $groups) && !in_array(1, $groups)) && ($arResult['arrAnswers'][$arOrder['ID']][75][103] || $arResult['arrAnswers'][$arOrder['ID']][75][102])) {
                                continue;
                            }



                            $str_count++;


                            $href_view = ''; // URL для просмотра
                            if ($arOrder["can_view"]) {
                                if (strlen(trim($arParams["VIEW_URL"])) > 0) {
                                    $href_view = $arParams["SEF_MODE"] == "Y" ? str_replace("#RESULT_ID#", $arOrder["ID"], $arParams["VIEW_URL"]) : $arParams["VIEW_URL"] . (strpos($arParams["VIEW_URL"], "?") === false ? "?" : "&") . "RESULT_ID=" . $arRes["ID"] . "&WEB_FORM_ID=" . $arParams["WEB_FORM_ID"];
                                }
                            }
                            $href_edit = ''; // URL для редактирования
                            if ($arOrder["can_edit"]) {
                                if (strlen(trim($arParams["EDIT_URL"])) > 0) {
                                    $href_edit = $arParams["SEF_MODE"] == "Y" ? str_replace("#RESULT_ID#", $arOrder["ID"], $arParams["EDIT_URL"]) : $arParams["EDIT_URL"] . (strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&") . "RESULT_ID=" . $arRes["ID"] . "&WEB_FORM_ID=" . $arParams["WEB_FORM_ID"];
                                }
                            }

                            if ($arResult['arrAnswers'][$arOrder['ID']][75][104]) {
                                $href_view = '/view_net.php?id='. $arOrder["ID"];
                            }

                                    $sStatusIconClass = '';
                                    $sStatusIconColor = '';
                                    switch ($arOrder['STATUS_ID']) {
                                        case '4':
                                            $sStatusIconClass = 'icon-ok';
                                            $sStatusIconColor = '#cccccc';
                                            $sStatusBgColor = '#fff';
                                            $statusfinish = 1;
                                            break;
                                        case '5':
                                            $sStatusIconClass = 'icon-ok-circled';
                                            $sStatusIconColor = '#006633;';
                                            $sStatusBgColor = '#a0ff9e;';
                                            $statusfinish = 0;
                                            break;
                                        case '6':
                                            $sStatusIconClass = 'icon-cancel-circled';
                                            $sStatusIconColor = '#cc0000';
                                            $sStatusBgColor = '#ffc3c3;';
                                            $statusfinish = 0;
                                            break;
                                    }
                                    //дата, ФИО, телефон, адрес, модель изделия, артикул изделия, размер, цена без скидки, цена со скидкой.
                                    //__($arResult['arrAnswers'][$arOrder['ID']]['74']);
                if (
                empty($arResult['arrAnswers'][$arOrder['ID']]['46'][53]['USER_TEXT']) ||
                empty($arResult['arrAnswers'][$arOrder['ID']]['53'][65]['USER_TEXT']) ||
                empty($arResult['arrAnswers'][$arOrder['ID']]['56'][71]['USER_TEXT']) ||
                empty($arResult['arrAnswers'][$arOrder['ID']]['58'][73]['USER_TEXT']) ||
                empty($arResult['arrAnswers'][$arOrder['ID']]['59'][74]['USER_TEXT']) ||
                empty($arResult['arrAnswers'][$arOrder['ID']]['61'][79]['USER_TEXT'])||
                empty($arResult['arrAnswers'][$arOrder['ID']]['65'][83]['USER_TEXT'])||
                empty($arResult['arrAnswers'][$arOrder['ID']]['66'][84]['USER_TEXT'])||
                empty($arResult['arrAnswers'][$arOrder['ID']]['68'][89]['USER_TEXT'])
                )
                {
                    $statusfinish = 0;
                }


                            ?>
                            <tr data-detail-url="<?= !empty($href_view) ? $href_view : ''; ?>" class="cursor__pointer order-list__item"
                            <?if ($arOrder['STATUS_ID'] == '4') {
                                if ($statusfinish == 1)
                                {
                                    ?>style="background: #faffb1;"<?
                                }
                                else
                                {
                                    ?>style="background: <?=$sStatusBgColor;?>;"<?
                                }
                            }else
                            {?>style="background: <?=$sStatusBgColor;?>;"<?}?>>
                                <td>
                                    <?=($arResult['arrAnswers'][$arOrder['ID']][75][103] ? 'ЛПУ' : ($arResult['arrAnswers'][$arOrder['ID']][75][104] ? 'СП' : 'Клиент'));
                                    ?></td>
                                <td><?= $arOrder['ID']; ?></td>
                                <td><?=($arOrder['USER_LAST_NAME']); ?></td>
                                <td><?=(!empty($arResult['arrAnswers'][$arOrder['ID']][48][60]['USER_TEXT']) ? $arResult['arrAnswers'][$arOrder['ID']][48][60]['USER_TEXT'] :  $arOrder['USER_LAST_NAME']);  ?></td>
                                <?if ($arResult['arrAnswers'][$arOrder['ID']][75][104] || $_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] == 102  ||  ((in_array(17, $groups)  || in_array(1, $groups)) && $_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] != 103)):?>
                                <td><?=(!empty($arResult['arrAnswers'][$arOrder['ID']][51][63]['USER_TEXT']) ? $arResult['arrAnswers'][$arOrder['ID']][51][63]['USER_TEXT'] :  "");  ?></td>
                                <?endif;?>
                                <td class="order-list--item-status no-wrap">

                                    <i class="font-icon <?= $sStatusIconClass; ?>" style="color:<?= $sStatusIconColor; ?>"></i>
                                    <?= $arOrder['STATUS_TITLE']; ?>
                                </td>
                                <td>
<?//__($arResult['arrAnswers'][$arOrder['ID']][69])?>
                                    <?if (!empty($arResult['arrAnswers'][$arOrder['ID']]['46'][53]['USER_TEXT'])): //
                                    // Дата доставки?>

                                        <?=$arResult['arrAnswers'][$arOrder['ID']][46][53]['USER_TEXT']; ?>
                                        <?foreach ($arResult['arrAnswers'][$arOrder['ID']][47] as $k => $res):?>
                                            <?//print_r($res)?>
                                            <?=($res['ANSWER_VALUE'] != 'default' &&  $res['ANSWER_VALUE'] != 'time_accuratly'? '<br/>'.$res['ANSWER_TEXT'] :
            '')
        ; ?>
                                        <?endforeach?>
                                        <?foreach ($arResult['arrAnswers'][$arOrder['ID']][49] as $k => $res):?>
                                            <?=($res['USER_TEXT'] != '' ? ' <br/>'.$res['USER_TEXT'].'' :
                                                '')
                                            ; ?>
                                        <?endforeach?>
                                    <?elseif(!empty($arResult['arrAnswers'][$arOrder['ID']][74][101])):
                                        // Дата визита?>

                                        <?=$arResult['arrAnswers'][$arOrder['ID']][74][101]['USER_TEXT']; ?>
                                    <?endif?>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    <? endif; ?>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="nine columns pager pager--default">
                <?if ($str_count > 0){?>
                <?= $arResult["pager"]; ?>
                <?}?>
            </div>

            <div class="three columns">
                <?if ($_REQUEST['set_filter'] == 'Y' && $arResult['res_counter'] <= 20 && $_REQUEST['find_MEDI_ORTO_type_ANSWER_TEXT_dropdown'] != 104):?>
                    <form action="/list.php" target="_blank" >
                        <input type="hidden" name="type" value="multi">
                        <input type="hidden" name="SHOWALL_1" value="1">
                    <button type="submit" class="button button-primary" name="multi_print"
                            value="Распечатать">Распечатать (<?=$arResult['res_counter']?>)</button>
                    </form>
                <?endif;?>
            </div>
        </div>
    </div>
</div>
