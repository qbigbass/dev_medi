<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
Bitrix\Main\Page\Asset;

if ($arParams['GUEST_MODE'] !== 'Y')
{
	Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
	Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
}
$this->addExternalCss("/bitrix/css/main/bootstrap.css");

CJSCore::Init(array('clipboard', 'fx'));

$APPLICATION->SetTitle("");
// echo "<pre>";
// var_dump($arResult);
// echo "</pre>";
if (!empty($arResult['ERRORS']['FATAL']))
{
	foreach ($arResult['ERRORS']['FATAL'] as $error)
	{
		ShowError($error);
	}

	$component = $this->__component;

	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED]))
		{
			$APPLICATION->AuthForm('', false, false, 'N', false);
		}
	}
	else
	{
		if (!empty($arResult['ERRORS']['NONFATAL']))
		{
			foreach ($arResult['ERRORS']['NONFATAL'] as $error)
			{
				ShowError($error);
			}
		}
		?>
		<h1 class="ff-medium">
			<?= Loc::getMessage('SPOD_LIST_MY_ORDER', array(
				'#ACCOUNT_NUMBER#' => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
				'#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
				)) ?>
			</h1>
		<div class="detail-text-wrap flex">

				<div class="flex-item">

					<div class="sale_status">
						Статус: &nbsp;
							<strong><?
							if ($arResult['CANCELED'] !== 'Y')
							{
								echo htmlspecialcharsbx($arResult["STATUS"]["NAME"]);
							}
							else
							{
								echo Loc::getMessage('SPOD_ORDER_CANCELED');
							}
							?></strong>
					</div>

					<div class="sale_price">
							<?= Loc::getMessage('SPOD_ORDER_PRICE')?>: &nbsp;
							<strong><?= $arResult["PRICE_FORMATED"]?></strong>
					</div>


					<div class="paysystem">
						<h2 class="ff-medium">
							<?= Loc::getMessage('SPOD_ORDER_PAYMENT') ?>
						</h2>
						<?
						foreach ($arResult['PAYMENT'] as $payment)
						{
							?>
							<span class="paysystem_name_title"><?=GetMessage('SPOD_PAYSYSTEM_NAME')?></span> <span class="paysystem_name_value"><strong><?=$payment["PAY_SYSTEM_NAME"]?></strong></span><br />
							<span class="paysystem_paid_title"><?=GetMessage('SPOD_ORDER_PAID')?></span>
							<span class="paysystem_paid_value"><strong><?=($payment["PAID"]=='Y')? "Да":"Нет"?></strong></span>
							<?
						}?>

	                      		<?
								$allow_pay_status = COption::GetOptionString("sale","allow_pay_status", "Q");

								 if ($arResult["PAYED"] == "Y"): ?>
	                                Оплачен <?= $arResult["DATE_PAYED"]; ?>
	                            <? elseif (($arResult['STATUS']['ID'] >= $allow_pay_status || $arResult['STATUS']['ID'] == 'Q') && $arResult['STATUS']['ID'] != 'W' && $arResult["PAYED"] != "Y"): ?>
	                                <span id="pay_order_block">
	                                    <?
	                                    $ORDER_ID = $ID;
	                            if (!empty($arResult["PAYMENT"]))
	                            {
	                                foreach ($arResult['PAYMENT'] AS $payment)
	                                {


	                            if($payment['PAID'] == 'N')
	                            {

	                                echo "<div id='order_pay' style='margin: 0 auto;'>".$payment['BUFFERED_OUTPUT']."</div>";


	                            }
	                            }
	                        }

	                                    try {
	                                        include($arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"]);
	                                    } catch (\Bitrix\Main\SystemException $e) {
	                                        if ($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
	                                            $message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
	                                        else
	                                            $message = $e->getMessage();

	                                        ShowError($message);
	                                    }

	                                    ?>
	                                </span>
	                                <?/**/?>

	                            <? endif; ?>
	                    <?//}?>
					</div>

	</div>
			<div class="flex-item">
				<div class="sale_details">


					<h4><?=GetMessage('SPOD_PARAMETRS')?></h4><br />
					<ul class="sale_details_list">
						<?
						if (isset($arResult["ORDER_PROPS"]))
						{	$group="";
						foreach ($arResult["ORDER_PROPS"] as $property)
						{

							if($property["CODE"]=="ZIP" || $property["CODE"]=="") {
								continue;
							}
									//var_dump($property);
						?>

						<li class="sale_details_item">
							<strong><?= htmlspecialcharsbx($property['NAME']) ?>:</strong>
								<?
								if ($property["TYPE"] == "Y/N")
								{
									echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
								}
								else
								{
									if ($property['MULTIPLE'] == 'Y'
										&& $property['TYPE'] !== 'FILE'
										&& $property['TYPE'] !== 'LOCATION')
									{
										$propertyList = unserialize($property["VALUE"]);
										foreach ($propertyList as $propertyElement)
										{
											echo $propertyElement . '</br>';
										}
									}
									elseif ($property['TYPE'] == 'FILE')
									{
										echo $property["VALUE"];
									}
									else
									{
										echo htmlspecialcharsbx($property["VALUE"]);
									}
								}
								?>

						</li>
						<?
					}
				}
				?>
			</ul>
	</div>


			</div>
		</div>


				<div class="order_basket">
					<h2 class="ff-medium">
						<?= Loc::getMessage('SPOD_ORDER_BASKET')?>
					</h2>
					<div class="table-simple-wrap">
					<table class="table-simple">
						<thead>
							<th><?= Loc::getMessage('SPOD_NAME')?></th>
							<th><?= Loc::getMessage('SPOD_PRICE')?></th>
							<th><?= Loc::getMessage('SPOD_QUANTITY')?></th>
							<th><?= Loc::getMessage('SPOD_ORDER_PRICE')?></th>
						</thead>

<?
						$discount_price=0;
						foreach ($arResult['BASKET'] as $basketItem)
						{
							$discount_price+=$basketItem["DISCOUNT_PRICE"]*$basketItem["QUANTITY"];
							?>
							<tr class="itemRow">
								<td class="sale_basket_item">
								<?if ($basketItem['DETAIL_PAGE_URL']==""){?>
									<div class="basket_image_href" >
								<?}else{?>
									<a class="basket_image_href" href="<?=$basketItem['DETAIL_PAGE_URL']?>">
								<?}?>
										<?
										if (strlen($basketItem['PICTURE']['SRC']))
										{
											$imageSrc = $basketItem['PICTURE']['SRC'];
										}
										else
										{
											$imageSrc = $this->GetFolder().'/images/no_photo.png';
										}
										?>
										<img src="<?=$imageSrc?>">
								<?if ($basketItem['DETAIL_PAGE_URL']==""){?>
									</div>
								<?}else{?>
									</a>
								<?}?>

<?//var_dump($basketItem);
                                                //__($basketItem);?>
							<div class="basket_item_contet">
								<?if ($basketItem['DETAIL_PAGE_URL']==""){?>
									<div class="basket_item_name" ><?=htmlspecialcharsbx($basketItem['NAME'])?></div>
								<?}else{?>
									<a class="basket_item_name" href="<?=$basketItem['DETAIL_PAGE_URL']?>"><?=htmlspecialcharsbx($basketItem['NAME'])?></a>
								<?}?>
									<br>
							<?		if (is_array($basketItem["SKU_DATA"]) && !empty($basketItem["SKU_DATA"])):
										$propsMap = array();
										foreach ($basketItem["PROPS"] as $propValue)
										{
											if (empty($propValue) || !is_array($propValue))
												continue;
											$propsMap[$propValue['CODE']] = (isset($propValue['~VALUE']) ? $propValue['~VALUE'] : $propValue['VALUE']);
										}
										unset($propValue);

										foreach ($basketItem["SKU_DATA"] as $propId => $arProp):

											$selectedIndex = 0;
											// if property contains images or values
											$isImgProperty = false;
											if (!empty($arProp["VALUES"]) && is_array($arProp["VALUES"]))
											{
												$counter = 0;
												foreach ($arProp["VALUES"] as $id => $arVal)
												{
													$counter++;
													if (isset($propsMap[$arProp['CODE']]))
													{
														if ($propsMap[$arProp['CODE']] == $arVal['NAME'] || $propsMap[$arProp['CODE']] == $arVal['XML_ID'])
															$selectedIndex = $counter;
													}
													if (!empty($arVal["PICT"]) && is_array($arVal["PICT"]) && ($arProp['CODE'] == 'TSVET_1' || $arProp['CODE'] == 'TSVET_1_')
														&& !empty($arVal["PICT"]['SRC']))
													{
														$isImgProperty = true;
													}
												}
												unset($counter);
											}
											$countValues = count($arProp["VALUES"]);
											$full = ($countValues > 5) ? "full" : "";

											$marginLeft = 0;
											if ($countValues > 5 && $selectedIndex > 5)
												$marginLeft = ((5 - $selectedIndex)*20).'%';

											if ($isImgProperty): // iblock element relation property
											?>
													<span class="bx_item_section_name_gray">
														<?=htmlspecialcharsbx($arProp["NAME"])?>:
													</span>
														<?
																$counter = 0;
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):
																	$counter++;
																	$selected = ($selectedIndex == $counter ? ' bx_active' : '');
																?>
																	<span
																		class="sku_prop<?=$selected?>"
																		data-sku-selector="Y"
																		data-value-id="<?=$arSkuValue["XML_ID"]?>"
																		data-sku-name="<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																	>
																		<a href="javascript:void(0)" class="cnt" title="<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>"><span class="cnt_item" style="background-image:url(<?=$arSkuValue["PICT"]["SRC"];?>)"></span></a>
																	</span>
																<?
																endforeach;
																unset($counter);
																?>


											<?
											else:


											?>
													<span class="bx_item_section_name_gray">
														<?=htmlspecialcharsbx($arProp["NAME"])?>:
													</span>
																<?
																if (!empty($arProp["VALUES"]))
																{
																	$counter = 0;
																	foreach ($arProp["VALUES"] as $valueId => $arSkuValue):
																		$counter++;
																		$selected = ($selectedIndex == $counter ? ' bx_active' : '');
																	 if ($selected):?>
																		<span
																			class="sku_prop<?=$selected?>"
																			data-sku-selector="Y"
																			data-value-id="<?=($arProp['TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory' ? $arSkuValue['XML_ID'] : htmlspecialcharsbx($arSkuValue['NAME'])); ?>"
																			data-sku-name="<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>"
																			data-element="<?=$arItem["ID"]?>"
																			data-property="<?=$arProp["CODE"]?>"
																		>
																			<?=htmlspecialcharsbx($arSkuValue["NAME"]); ?>
																		</span>
																	<?
                                                                    endif;
																	endforeach;
																	unset($counter);
																}
																?>

											<?
											endif;
?>
<?
										endforeach;
									endif;


                                    foreach ($basketItem["PROPS"] as $propValue)
									{
									   if ($propValue['CODE'] != 'CML2_ARTICLE') continue;
									    ?>

									<span class="bx_item_section_name_gray">
										<?=htmlspecialcharsbx($propValue["NAME"])?>:
									</span>

												<?
												if (!empty($propValue["VALUE"]))
												{?>
														<span
															class="sku_prop0"
															data-sku-selector="Y"
															data-value-id="<?=htmlspecialcharsbx($propValue["VALUE"]); ?>"
															data-sku-name="<?=htmlspecialcharsbx($propValue["NAME"]); ?>"
															data-element="<?=$arItem["ID"]?>"
															data-property="<?=$propValue["CODE"]?>"
														>
															<?=htmlspecialcharsbx($propValue["VALUE"]); ?>
														</span>
													<?
												}
												?>

                                   <? }?>
					</div>

							</td>

							<td class="basket_base_price">
							<?//var_dump($basketItem);?>
							<?if (strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"]))
								{?>
									<span class="base_price_old"><?= $basketItem['BASE_PRICE_FORMATED'] ?></span>
									<span class="discount_price"><?= $basketItem['PRICE_FORMATED'] ?></span>
								<?}else{?>
									<p class="base_price"><?=$basketItem['BASE_PRICE_FORMATED']?></p>
								<?}?>
							</td>
							<td class="basket_quantity">
								<p class="quantity_p"><?=$basketItem['QUANTITY']?>
								<?
								if (strlen($basketItem['MEASURE_NAME']))
								{
									echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
								}
								else
								{
									echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
								}
								?></p>
							</td>
							<td>
								<div class="sum">
									<div class="sale-order-detail-order-item-td-text">
										<strong class="bx-price all"><?=$basketItem['FORMATED_SUM']?></strong>
									</div>
								</div>
							</td>
						</tr>
						<?
					}
					?>
					<tr class="itemRow">
						<td></td><td></td><td></td>
						 <td align="right">
							 Стоимость: <span class="summary_price_value"><?=$arResult['PRODUCT_SUM_FORMATED']?></span><br>
						<?if ($discount_price!=0)
						{
							?>
							Скидка: <span class="discount_price"><?=$discount_price?> руб </span><span class="discount_percent">(-<?=round($discount_price/($arResult["PRODUCT_SUM"]+$discount_price)*100)?>%)</span><br>
							<?
						}?>
						<?/*if (floatval($arResult["ORDER_WEIGHT"]))
						{
							?>
							Вес: <?= $arResult['ORDER_WEIGHT_FORMATED'] ?><br>
							<?
						}*/?>
					<?if (strlen($arResult["PRICE_DELIVERY_FORMATED"]))
						{
							?>
							Доставка:  <span class="summary_price_value"><?= $arResult["PRICE_DELIVERY_FORMATED"] ?></span><br>
							<?
						} ?>
						<br>


						<strong><?= Loc::getMessage('SPOD_SUMMARY')?>: <span class="summary_price_value"><?=$arResult['PRICE_FORMATED']?></span></strong>
						</td>
					</tr>
				</table>
			</div>
				</div>

				<?
				if ($arParams['GUEST_MODE'] !== 'Y')
				{
					?>
					<div>
						<?/*<a href="<?=$arResult["URL_TO_COPY"]?>" class="btn-simple btn-micro">
							<?= Loc::getMessage('SPOD_ORDER_REPEAT') ?>
						</a>*/?>
						<?
						if ($arResult["CAN_CANCEL"] === "Y" &&  ($arResult['STATUS_ID'] == 'N'))
						{
							?>
							<a href="<?=$arResult["URL_TO_CANCEL"]?>" class="btn-simple btn-micro">
								<?= Loc::getMessage('SPOD_ORDER_CANCEL') ?>
							</a>
							<?
						}
						?>
					</div>
					<?
				}
				?>
				<br><br>
				<?
				if ($arParams['GUEST_MODE'] !== 'Y' && $arResult['LOCK_CHANGE_PAYSYSTEM'] !== 'Y')
				{
					?>
					<a class="btn-simple btn-micro btn-black" href="<?= $arResult["URL_TO_LIST"] ?>">&larr; <?= Loc::getMessage('SPOD_RETURN_LIST_ORDERS')?></a>
					<?
				}
				?><br><br>
			</div>
			<?
			$javascriptParams = array(
				"url" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
				"templateFolder" => CUtil::JSEscape($templateFolder),
				"paymentList" => $paymentData
			);
			$javascriptParams = CUtil::PhpToJSObject($javascriptParams);
			?>
			<script>
				BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?=$javascriptParams?>);
			</script>
			<?
		}
		?>
