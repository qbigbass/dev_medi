<div class="shoesWrap" id="filter">
	<div class="flex limiter" style="margin-bottom: 18px;">
		<div class="flex-item">
			<div class="sizeHeader">
				<div class="sizeTitle">
					<span class="h3 ff-medium title">Длина стопы<span class="starrequired" title="Обязательное поле">*</span>&nbsp;<span class="help_icon grey  get_medi_popup_Window" data-id="help-dlina" data-title="Как правильно снять мерки" data-svg="/upload/content/shoes/1.svg">?</span></span>
				</div>
				<div class="sizeTitle help">
					<span class="h3 ff-medium title get_medi_popup_Window" data-id="shoes-1" data-title="Как пользоваться сервисом подбора обуви" data-src="/shoes/help.inc.html">Как подобрать обувь <span class="help_icon magenta">?</span></span>
				</div>
			</div>
			<input class="length" name="shoes_filter[length][]" id="length" placeholder="Введите значние (см)" type="number" step="0.1" min="10" max="32">
            <div class="sizeTitle">
				<span class="h3 ff-medium title">Полнота стопы <span class="help_icon grey get_medi_popup_Window" data-svg="/upload/content/shoes/2.svg" data-title="Как правильно снять мерки"  data-id="help-polnota">?</span></span>
            </div>
			<input class="sizeCircle" name="shoes_filter[fullness][]" id="fullness" placeholder="Введите значние (см)" type="number" step="0.1"  min="10" max="32">
		</div>
		<div class="flex-item">
			<div class="h3 ff-medium title" >Для кого<span class="starrequired" title="Обязательное поле">*</span>&nbsp;</div>
			<div class="btn-wrap person">
                <?foreach($arResult['PROPERTIES']['FOR_WHO'] AS $key=>$who){
                    if (in_array($who['PROPERTY_FOR_WHO_VALUE'], ['Женщины', 'Мужчины'])){?>
				<div class="btn-simple btn-border btn-medium for-who" data-value="<?=$who['PROPERTY_FOR_WHO_ENUM_ID']?>"><?=$who['PROPERTY_FOR_WHO_VALUE']?></div>
                <?}
                }?>
			</div>
			<div class="h3 ff-medium title" style="margin-bottom: 14px;">Сезон</div>
			<div class="btn-wrap seasons">
                <?foreach($arResult['PROPERTIES']['SEASON'] AS $key=>$season){?>
				<div class="btn-simple btn-border btn-medium season" data-value="<?=$season['PROPERTY_SEASON_ENUM_ID']?>" ><?=$season['PROPERTY_SEASON_VALUE']?></div>
                <?}?>
			</div>
		</div>
	</div>
	<div class="light-gray-bg">
        <div class="flex">
            <?if ($arResult['PROPERTIES']['PRODUCT_TYPE']):?>
            <div class="section offerType">
                <div class="questions-answers-list">
                    <div class="question-answer-wrap">
                        <div class="question h3 ff-medium">Вид изделия
                            <div class="open-answer "><div class=" arrow_icon" ></div></div>
                        </div>
                        <div class="answer" style="display: none;">
                            <?foreach($arResult['PROPERTIES']['PRODUCT_TYPE'] AS $key=>$prod_type){?>
                            <div class="filterCheckboxField webFormItemField ">
                                <input type="checkbox" class="prod_type" id="prod_type_<?=$prod_type['PROPERTY_PRODUCT_TYPE_ENUM_ID']?>" name="shoes_filter[PRODUCT_TYPE][]" data-count="<?=$prod_type['CNT']?>" value="<?=$prod_type['PROPERTY_PRODUCT_TYPE_ENUM_ID']?>">
                                <label for="prod_type_<?=$prod_type['PROPERTY_PRODUCT_TYPE_ENUM_ID']?>"><?=$prod_type['PROPERTY_PRODUCT_TYPE_VALUE']?></label>
                            </div>
                            <?}?>

                        </div>
                    </div>
                </div>
            </div>
            <?endif;?>
            <?if ($arResult['PROPERTIES']['USE_FOR']):?>
            <div class="section medical">
                <div class="questions-answers-list">
                    <div class="question-answer-wrap">
                        <div class="question h3 ff-medium">Медицинское назначение
                            <div class="open-answer "><div class=" arrow_icon" ></div></div>
                        </div>
                        <div class="answer" style="display: none;">
                            <?foreach($arResult['PROPERTIES']['USE_FOR'] AS $key=>$use_for){?>
                            <div class="filterCheckboxField webFormItemField ">
                                <input type="checkbox" class="use_for" id="use_for_<?=$use_for['PROPERTY_USE_FOR_ENUM_ID']?>" name="shoes_filter[USE_FOR][]" data-count="<?=$use_for['CNT']?>" value="<?=$use_for['PROPERTY_USE_FOR_ENUM_ID']?>">
                                <label for="use_for_<?=$use_for['PROPERTY_USE_FOR_ENUM_ID']?>"><?=$use_for['PROPERTY_USE_FOR_VALUE']?></label>
                            </div>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
            <?endif;?>

            <?if ($arResult['PROPERTIES']['ATT_BRAND']):?>
            <div class="section brandOf">
                <div class="section-header questions-answers-list">
                    <div class="question-answer-wrap">
                        <div class="question h3 ff-medium">Бренд
                            <div class="open-answer "><div class=" arrow_icon" ></div></div>
                        </div>
                        <div class="answer" style="display: none;">
                        <?foreach($arResult['PROPERTIES']['ATT_BRAND'] AS $key=>$brand){?>
                            <div class="filterCheckboxField webFormItemField ">
                                <input type="checkbox" class="brands" id="brand_<?=$brand['PROPERTY_ATT_BRAND_VALUE']?>" name="shoes_filter[ATT_BRAND][]" data-count="<?=$brand['CNT']?>" value="<?=$brand['PROPERTY_ATT_BRAND_VALUE']?>">
                                <label for="brand_<?=$brand['PROPERTY_ATT_BRAND_VALUE']?>"><?=$brand['NAME']?></label>
                            </div>
                        <?}?>
                        </div>
                    </div>
                </div>
            </div>
            <?endif;?>
        </div>
        <div style="text-align: center;">
            <h3 class="h3 ff-medium" id="results_count">Укажите параметры поиска</h3>
            <div class="btn-simple btn-medium disabled" id="show_but" >Показать</div>
        </div>
    </div>
	<div class="limiter" id="catalogColumn">
	<div class="shoes_results" id="sresult">

	</div>
	</div>
</div>
