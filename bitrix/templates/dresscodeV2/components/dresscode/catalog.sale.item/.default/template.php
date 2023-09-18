<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?$this->setFrameMode(true);
use Bitrix\Main\Grid\Declension;?>

<?php
//echo "<pre>"; print_r($arResult["ITEMS"]); echo "</pre>"
?>

<?if(!empty($arResult["ITEMS"])):?>
	<?$uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false);?>
    <div class="bindAction" id="<?=$this->GetEditAreaId($arNextElement["ID"]);?>">
        <div class="ff-medium row h3">Товар участвует в акци<?=(count($arResult['ITEMS'])>1 ? 'ях' : 'и')?>:</div>
        <?
        $timerOn = false;
        ?>
        <?foreach($arResult["ITEMS"] as $ii => $arNextElement):
            $hide_link = $arNextElement['PROPERTY_HIDE_VALUE'] == 'Да' ? 'Y' : 'N';?>
            <?
//            $dayDiff = '';
//            if ($arNextElement['DATE_ACTIVE_TO'] > 0) {
//                $date = DateTime::createFromFormat('d.m.Y H:i:s', $arNextElement['DATE_ACTIVE_TO']);
//                $now = new DateTime();
//                if ($date) {
//                    $dayDiff = $date->diff($now)->format('%a');
//                    if ($dayDiff > 0)
//                    {
//                        $sDeclension = new Declension('день', 'дня', 'дней');
//                        $dayDiff_str = '<br/><span class="action_over">Заканчивается через '.$dayDiff.'&nbsp;'.$sDeclension->get($dayDiff).'</span>';
//                    }
//                }
//            }

            $date = DateTime::createFromFormat('d.m.Y H:i:s', $arNextElement['ACTIVE_TO']);
            $now = new DateTime();
            $daysLeft = $date->diff($now)->format('%a'); // Кол-во дней до окончания акции

            $showAction = false;
            if ( (int)$arNextElement['PROPERTIES']['CNT_DAYS_TIMER']['VALUE'] > 0 && $daysLeft <= (int)$arNextElement['PROPERTIES']['CNT_DAYS_TIMER']['VALUE'] ) {
                $showAction = true;
            } else {
                if ($daysLeft <= DAYS_END_ACTION) {
                    $showAction = true;
                }
            }

            if ($arNextElement['PROPERTIES']['TIMER_ON']['VALUE'] === 'Да' && $arNextElement['ACTIVE_TO'] > 0 && !$timerOn && $showAction) {
                $timerUniqId = $this->randString();
                $endDate = MakeTimeStamp($arNextElement['ACTIVE_TO'], "DD.MM.YYYY HH:MI:SS");
                $timerOn = true;
            } else {
                $timerOn = false;
            }

            if(!empty($arNextElement["EDIT_LINK"])){
                $this->AddEditAction($arNextElement["ID"], $arNextElement["EDIT_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arNextElement["ID"], $arNextElement["DELETE_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
            }
            ?>

            <div class="tb <?if($timerOn):?>action-wrapper<?endif;?>">
                <!-- Таймер окончания акции -->
                <? if ($timerOn):?>
                    <div class="timer">
                        <div class="specialTime smallSpecialTime"
                             id="timer_<?= $timerUniqId; ?>_<?= $uniqID ?>">
                            <div class="specialTimeItem">
                                <div class="specialTimeItemValue timerDayValue">0</div>
                                <div class="specialTimeItemlabel"><?= GetMessage("TIMER_DAY_LABEL") ?></div>
                            </div>
                            <div class="specialTimeItem">
                                <div class="specialTimeItemValue timerHourValue">0</div>
                                <div class="specialTimeItemlabel"><?= GetMessage("TIMER_HOUR_LABEL") ?></div>
                            </div>
                            <div class="specialTimeItem">
                                <div class="specialTimeItemValue timerMinuteValue">0</div>
                                <div class="specialTimeItemlabel"><?= GetMessage("TIMER_MINUTE_LABEL") ?></div>
                            </div>
                            <div class="specialTimeItem">
                                <div class="specialTimeItemValue timerSecondValue">0</div>
                                <div class="specialTimeItemlabel"><?= GetMessage("TIMER_SECOND_LABEL") ?></div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("#timer_<?=$timerUniqId;?>_<?=$uniqID?>").dwTimer({
                                endDate: "<?=$endDate?>"
                            });
                        });
                    </script>
                <?endif;?>

                <div class="tc bindActionImage">
                    <?if($hide_link == 'N'){?><a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>"><?}?><span class="image" title="<?=$arNextElement["NAME"]?>"></span><?if($hide_link == 'N'){?></a><?}?>
                </div>
                <div class="tc">
                    <?if($hide_link == 'N'){?>
                        <a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>" class="theme-color ff-medium">
                    <?}?>
                    <?=$arNextElement["NAME"]?>

                    <?if($hide_link == 'N'){?>
                        </a>
                    <?}?>

                    <?
//                    if ($dayDiff> 0){
//                        echo $dayDiff_str;
//                    }
                    ?>
                </div>
            </div>
        <?endforeach;?>
    </div>
<?endif;?>