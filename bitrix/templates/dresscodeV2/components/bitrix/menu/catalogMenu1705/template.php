<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if(CModule::IncludeModule("currency")):?>
	<?if (!empty($arResult)):
		 ?>
		<div id="mainMenuStaticContainer">
			<div id="mainMenuContainer"<?if(!empty($_SESSION["TOP_MENU_FIXED"]) && $_SESSION["TOP_MENU_FIXED"] == "Y"):?> class="auto-fixed"<?endif;?>>
				<div class="limiter">
					<div class="min_menu_container">
						<a href="<?=SITE_DIR?>catalog/" class="minCatalogButton" id="catalogSlideButton">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/catalogButton.png" alt=""> <?//=GetMessage("CATALOG_BUTTON_LABEL")?>
						</a>
						<div class="min_menu">
								<a href="/salons/"><img src="/bitrix/templates/dresscodeV2/images/menu/map_w.svg"/></a>
								<a href="#" class="openSearch"><img src="/bitrix/templates/dresscodeV2/images/menu/search_w.svg"/></a>
								<a href="/personal/"><img src="/bitrix/templates/dresscodeV2/images/menu/user_w.svg"/></a>
								<a href="/personal/cart/"><img src="/bitrix/templates/dresscodeV2/images/menu/cart_w.svg"/></a>
						</div>
					</div>
					<?if(count($arResult["ITEMS"]) > 3):?>
						<div id="menuCatalogSection">
							<div class="menuSection">
								<a href="<?=SITE_DIR?>catalog/" class="catalogButton"><span class="catalogButtonImage"></span></a>
								<div class="drop">
									<div class="limiter">
										<ul class="menuSectionList">
											<?foreach($arResult["ITEMS"] as $nextElement):?>
												<li class="sectionColumn">
													<div class="container">
														<?if(!empty($nextElement["DETAIL_PICTURE"])):?>
															<a href="<?=$nextElement["LINK"]?>" class="picture">
																<img src="<?=$nextElement["DETAIL_PICTURE"]["src"]?>" alt="<?=$nextElement["TEXT"]?>">
															</a>
														<?endif;?>
														<a href="<?=$nextElement["LINK"]?>" class="menuLink<?if ($nextElement["SELECTED"]):?> selected<?endif?>">
															<?=$nextElement["TEXT"]?>
														</a>
													</div>
												</li>
											<?endforeach;?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?endif;?>
					<ul id="mainMenu">
						<?foreach($arResult["ITEMS"] as $nextElement):?>
							<li class="eChild<?if(!empty($nextElement["ELEMENTS"])):?> allow-dropdown<?endif;?>">
								<a href="<?=$nextElement["LINK"]?>" class="menuLink<?if ($nextElement["SELECTED"]):?> selected<?endif?>">
									<?if(!empty($nextElement["PICTURE"])):?>
										<img src="<?=$nextElement["PICTURE"]["src"]?>" alt="<?=$nextElement["TEXT"]?>" title="<?=$nextElement["TEXT"]?>">
									<?endif;?>
									<span class="back"></span>
									<span class="link-title"><?=$nextElement["TEXT"]?></span>
									<span class="dropdown btn-simple btn-micro"></span>
								</a>
								<?if(!empty($nextElement["ELEMENTS"])):?>
									<div class="drop <?if($nextElement['MENU_TMPL'] != ''){?>mob_menu<?}?>"<?if(!empty($nextElement["BIG_PICTURE"]) && $nextElement['MENU_TMPL'] == ''):?> style="background: url(<?=$nextElement["BIG_PICTURE"]["src"]?>) 50% 50% no-repeat #ffffff;"<?endif;?>>
										<div class="limiter">
											<?foreach($nextElement["ELEMENTS"] as $next2Column):?>
												<?if(!empty($next2Column)):?>
													<ul class="nextColumn <?if($nextElement['MENU_TMPL'] != ''){?>old_menu<?}?>">
														<?foreach ($next2Column as $x2 => $next2Element):?>
															<li<?if(!empty($next2Element["ELEMENTS"])):?> class="allow-dropdown"<?endif;?>>
																<?if(!empty($next2Element["PICTURE"]["src"])):?>
																	<a href="<?=$next2Element["LINK"]?>" class="menu2Link has-image">
																		<img src="<?=$next2Element["PICTURE"]["src"]?>" alt="<?=$next2Element["TEXT"]?>" title="<?=$next2Element["TEXT"]?>">
																	</a>
																<?endif;?>
																<a href="<?=$next2Element["LINK"]?>" class="menu2Link<?if ($next2Element["SELECTED"]):?> selected<?endif?>">
																	<?=$next2Element["TEXT"]?>
																	<?if(!empty($next2Element["ELEMENTS"])):?>
																		<span class="dropdown btn-simple btn-micro"></span>
																	<?endif;?>
																</a>
																<?if(!empty($next2Element["ELEMENTS"])):?>
																	<ul>
																		<?foreach($next2Element["ELEMENTS"] as $x3 => $next3Element):?>
																			<li>
																				<a href="<?=$next3Element["LINK"]?>" class="menu2Link<?if ($next3Element["SELECTED"]):?> selected<?endif?>">
																					<?=$next3Element["TEXT"]?>
																				</a>
																			</li>
																		<?endforeach;?>
																	</ul>
																<?endif;?>
															</li>
														<?endforeach;?>

													</ul>

												<?endif;?>
											<?endforeach;?>

											<?if(!empty($nextElement["ELEMENTS"]) && $nextElement['MENU_TMPL'] != ''):?>
												<?include("uniq_tmpl/".$nextElement['MENU_TMPL'])?>
											<?endif;?>
										</div>
									</div>
								<?endif;?>
							</li>
						<?endforeach;?>
						<li class=" eChild"><a href="/services/" class="menuLink" style="background:#f7f7f7;">Услуги</a></li>
						<li class="specialItem eChild"><a href="/stock/" class="menuLink">Акции</a></li>

						<li class="moreItem eChild"><?$APPLICATION->IncludeComponent(
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

						<li class="moreItem eChild">
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
