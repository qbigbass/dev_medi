<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(false);?>
<?if(CModule::IncludeModule("currency")):
	//__($arResult["DROP_MENU"]);
	//__($arResult["ITEMS"]);

    global $USER;
$link = 'lk';
    if ($USER->IsAuthorized() && !empty(array_intersect([20], $USER->GetUserGroupArray())))
{
    $lk_menu = '<ul>
            <li class="top-lk-personal head"><a href="'.SITE_DIR.'salons/profile/" 1>'.$USER->GetFirstName().' '.$USER->GetLastName().'</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'salons/profile/">Личный кабинет</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'salons/orders/">Мои заказы</a></li>
			<li class="top-lk-exit"><a href="'.SITE_DIR.'exit/">Выход</a></li>
		</ul>';
}elseif ($USER->IsAuthorized() && !empty(array_intersect([29], $USER->GetUserGroupArray())))
    {
        $lk_menu = '<ul>
            <li class="top-lk-personal head"><a href="'.SITE_DIR.'smp/">'.$USER->GetFirstName().' '.$USER->GetLastName().'</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'smp/">Личный кабинет</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'smp/orders/">Мои заказы</a></li>
			<li class="top-lk-exit"><a href="'.SITE_DIR.'exit/">Выход</a></li>
		</ul>';
    }elseif ($USER->IsAuthorized())
{
    $lk_menu = '<ul>
            <li class="top-lk-personal head"><a href="'.SITE_DIR.'lk/">'.$USER->GetFirstName().' '.$USER->GetLastName().'</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'lk">Личный кабинет</a></li>
			<li class="top-lk-personal"><a href="'.SITE_DIR.'lk/?orders">Статус заказа</a></li>
		    <li class="top-lk-personal"><a href="'.SITE_DIR.'lk/?history">История покупок</a></li>
			<li class="top-lk-exit"><a href="'.SITE_DIR.'exit/">Выход</a></li>
		</ul>';
}
	?>
    <script>var $addsales = <?=(SITE_ID != 's3' ? "1" : "0");?>;</script>
	<?if (!empty($arResult)):
		 ?>
		<div id="mainMenuStaticContainer">
			<div id="mainMenuContainer"<?if(!empty($_SESSION["TOP_MENU_FIXED"]) && $_SESSION["TOP_MENU_FIXED"] == "Y"):?> class="auto-fixed"<?endif;?>>
				<div class="limiter">
					<div class="min_menu_container">
						<a href="<?=SITE_DIR?>catalog/" class="minCatalogButton" id="catalogSlideButton">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/catalogButton.svg" alt=""> <?//=GetMessage("CATALOG_BUTTON_LABEL")?>

						</a>
						<?/*<div class="min_menu">
								<a href="/salons/"><img src="/bitrix/templates/dresscodeV2/images/menu/map_w.svg"/></a>
								<a href="#" class="openSearch"><img src="/bitrix/templates/dresscodeV2/images/menu/search_w.svg"/></a>
								<a href="/lk/" <?if ($USER->IsAuthorized()){?> class="openAuth"<?}?>><img src="/bitrix/templates/dresscodeV2/images/menu/user_w.svg"/></a>
								<a href="/personal/cart/"><img src="/bitrix/templates/dresscodeV2/images/menu/cart_w.svg"/></a>
                        <?if ($USER->IsAuthorized()){?>
                            <div class="topAuth">
                                <?=$lk_menu?>
                            </div>
                            <?}?>
						</div>*/?>

                        <div class="min_menu">
                            <div id="topSearch3" style="display: block;position: inherit;
line-height: normal;height:auto;">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    ".default",
                                    array(
                                        "AREA_FILE_SHOW" => "sect",
                                        "AREA_FILE_SUFFIX" => "searchLine3",
                                        "AREA_FILE_RECURSIVE" => "Y",
                                        "EDIT_TEMPLATE" => ""
                                    ),
                                    false
                                );?>


                            </div>
                            <!--<div class="b_head_search">
                        <div id="topSearchLine">

    					</div>
                    </div>--><?
                            $phone = $GLOBALS['medi']['phones'][SITE_ID];
                            $sphone = str_replace([' ', '-'], '', $phone);?>
                            <a href="tel:<?=$phone?>" onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); return true;" id="top-mob-phone"><img src="/bitrix/templates/dresscodeV2/images/menu/tel.svg"/></a>
                        </div>
					</div>
					<?if(count($arResult["ITEMS"]) > 3):?>
					<?include("uniq_tmpl/full_menu.html")?>

					<?endif;?>
					<ul id="mainMenu">
						<li class="moreItem"><?$APPLICATION->IncludeComponent(
    						"bitrix:main.include",
    						".default",
    						array(
    							"AREA_FILE_SHOW" => "sect",
    							"AREA_FILE_SUFFIX" => "menu_phone",
    							"AREA_FILE_RECURSIVE" => "Y",
    							"EDIT_TEMPLATE" => ""
    						),
    						false
    					);?></li>
						<?//__($arResult["DROP_MENU"]);
						foreach($arResult["DROP_MENU"] as $k => $nextElement):
							$ik = $k+1;
							$link = '';
                            if (!empty($nextElement["UF_LINK"])){
                                $link = $nextElement["UF_LINK"];
                            }
                            else{
                                $link = $nextElement["CATLINK"]['SECTION_PAGE_URL'];

                            }
							if ($APPLICATION->GetCurDir() == $link){
								$nextElement["SELECTED"] = 1;
							}?>

							<li class="eChild<?if(!empty($nextElement["ELEMENTS"])):?> allow-dropdown<?endif;?>">
								<a href="<?=$link?>" class="menuLink<?if ($nextElement["SELECTED"]):?> selected<?endif?>">
									<?/*if(!empty($nextElement["PICTURE"])):?>
										<img src="<?=$nextElement["PICTURE"]["src"]?>" alt="<?=$nextElement["TEXT"]?>" title="<?=$nextElement["TEXT"]?>">
									<?endif;*/?>
									<span class="back"></span>
									<span class="link-title"><span class="short-title"><?=$nextElement["NAME"]?></span><span class="full-title"><?=($nextElement["UF_FULLNAME"] != '' ? $nextElement["UF_FULLNAME"]  : $nextElement["NAME"] )?></span></span>
									<span class="dropdown btn-simple btn-micro"></span>
								</a>
								<?if(!empty($nextElement["ELEMENTS"])):?>
									<div class="drop <?if($arResult['ITEMS'][$ik]['MENU_TMPL'] != ''){?>mob_menu<?}?>"<?/*if(!empty($nextElement["BIG_PICTURE"]) && $nextElement['MENU_TMPL'] == ''):?> style="background: url(<?=$nextElement["BIG_PICTURE"]["src"]?>) 50% 50% no-repeat #ffffff;"<?endif;*/?>>
										<div class="limiter">
											<?foreach($nextElement["ELEMENTS"] as $next2Column):

												$sublink = '';
												if (!empty($next2Column["UF_LINK"])){
													$sublink = $next2Column["UF_LINK"];
												}
												else{
													$sublink = $next2Column["CATLINK"]['SECTION_PAGE_URL'];

												}
												if ($APPLICATION->GetCurDir() == $sublink){
													$next2Column["SELECTED"] = 1;
												}
												?>
												<?if(!empty($next2Column)):
													//__($next2Column);?>
													<ul class="nextColumn <?if($arResult['ITEMS'][$ik]['MENU_TMPL'] != ''){?>old_menu<?}?>">
														<?//foreach ($next2Column as $x2 => $next2Element):?>
															<li<?/*if(!empty($next2Element["ELEMENTS"])):?> class="allow-dropdown"<?endif;*/?>>
																<?/*if(!empty($next2Element["PICTURE"]["src"])):?>
																	<a href="<?=$next2Element["LINK"]?>" class="menu2Link has-image">
																		<img src="<?=$next2Element["PICTURE"]["src"]?>" alt="<?=$next2Element["TEXT"]?>" title="<?=$next2Element["TEXT"]?>">
																	</a>
																<?endif;*/?>
																<a href="<?=$sublink?>" class="menu2Link <?if ($next2Column["UF_ADDITIONAL"]== '1'):?> additional<?endif?>">
																	<?=$next2Column["NAME"]?>
																	<?/*if(!empty($next2Element["ELEMENTS"])):?>
																		<span class="dropdown btn-simple btn-micro"></span>
																	<?endif;*/?>
																</a>
																<?/*if(!empty($next2Element["ELEMENTS"])):?>
																	<ul>
																		<?foreach($next2Element["ELEMENTS"] as $x3 => $next3Element):?>
																			<li>
																				<a href="<?=$next3Element["LINK"]?>" class="menu2Link<?if ($next3Element["SELECTED"]):?> selected<?endif?>">
																					<?=$next3Element["TEXT"]?>
																				</a>
																			</li>
																		<?endforeach;?>
																	</ul>
																<?endif;*/?>
															</li>

														<?//endforeach;?>

													</ul>

												<?endif;?>
											<?endforeach;?>

											<?if( $arResult['ITEMS'][$ik]['MENU_TMPL'] != ''):?>
												<?include("uniq_tmpl/".$arResult['ITEMS'][$ik]['MENU_TMPL'])?>
											<?endif;?>
										</div>
									</div>
								<?endif;?>
							</li>
						<?endforeach;?>

						<li class=" eChild"><a href="/services/" class="menuLink" style="background:#f7f7f7;">Услуги</a></li>
						<li class="specialItem eChild"><a href="/stock/" class="menuLink">Акции</a></li>




						<li class="moreItem eChild" style="display:none;">
							<?$APPLICATION->IncludeComponent(
	    						"bitrix:main.include",
	    						".default",
	    						array(
	    							"AREA_FILE_SHOW" => "sect",
	    							"AREA_FILE_SUFFIX" => "menu_links",
	    							"AREA_FILE_RECURSIVE" => "Y",
	    							"EDIT_TEMPLATE" => ""
	    						),
	    						false
	    					);?></li>
					</ul>
				</div>
			</div>
		</div>
	<?endif;?>
<?endif;?>
<script>
$("#catalogSlideButton").click(function () {
  $("body").toggleClass("no-scroll");
$(".menuContainerColor").toggleClass("fixed");
});
</script>
<script>
function showCatalogMenu() {
 $("body").toggleClass("no-scroll");
$(".menuContainerColor").toggleClass("fixed");

}
</script>