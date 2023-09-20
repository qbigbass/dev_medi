<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Page\Asset;

CModule::IncludeModule('atwebsite.iblockimageloader');
$moduleId = 'atwebsite.iblockimageloader';


$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$errorMessage = "";
$bVarsFromForm = false;	

IncludeModuleLangFile(__FILE__);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['type'] == 'getTp')
{
	echo CAllIblockImageLoader::GetOffer($_REQUEST['IBID']);
}


$APPLICATION->SetTitle(GetMessage("MAIN_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arTabs = array(
	array("DIV" => "tabSettings", "TAB" => GetMessage("TAB_TITLE"), "TITLE"=> GetMessage("TAB_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $arTabs, false, true);
?>
<?
      CJSCore::Init(array("jquery"));
 ?>
 <div id="tbl_iblock_export_result_div">	
	
</div>
<form method="post" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>" name="iblock_image_loader" id="iblock_image_loader">

<?=bitrix_sessid_post()?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<?
	if(CModule::IncludeModule("iblock")):
	?>	
	<tr class="adm-detail-required-field">
		<td width="50%"><?=GetMessage("TITLE_IBLOCK")?></td>
		<td width="50%">
			<?echo GetIBlockDropDownListEx(
				$IBLOCK_ID,
				'IBLOCK_TYPE_ID',
				'IBLOCK_ID',
				array(
					"MIN_PERMISSION" => "X",
					"OPERATION" => "iblock_export",
				),
				'',
				'',
				'class="adm-detail-iblock-types"',
				'class="adm-detail-iblock-list"'
			);?>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="50%"><?=GetMessage("TITLE_IBLOCK_ELEMENT")?></td>
		<td width="50%">
			<input name="IBLOCK_ELEMENT" id="IBLOCK_ELEMENT" value="" size="5" type="text">
			<input type="button" id="SELECT_ELEMENT" value="..." disabled>&nbsp;<span id="sp_04695ec4810cefcc174992a7dfdcf0c3_n0"></span>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="50%"><?=GetMessage("TITLE_IBLOCK_PROPERTY")?></td>
		<td width="50%">
			<select id="PROP_FILTER" data-prop-id=0 name="PROP_FILTER" class="adm-detail-iblock-types">
				<option value="0"><?=GetMessage("SELECT_PROP")?></option>
			</select>&nbsp;&nbsp;
			<select id="PROP_FILTER_VALUE" data-prop-value-id=0 name="PROP_FILTER_VALUE" class="adm-detail-iblock-list">
				<option value="0"><?=GetMessage("SELECT_PROP_VALUE")?></option>
			</select>
		</td>
	</tr>
	<tr class="adm-detail-required-field" id="addProp">
		<td width="50%"></td>
		<td width="50%">
			<a class="add_prop disabled"><?=GetMessage("ADD_PROP_ROW")?></a>
		</td>		
	</tr>
	<tr class="adm-detail-required-field">
		<td width="50%"><?=GetMessage("OFFER_IMAGE_PROP")?></td>
		<td width="50%">			
			<select id="IMAGE_PROP" name="IMAGE_PROP" style="width:150px">
				<option value="0"><?=GetMessage("SELECT_PROP")?></option>
			</select>
		</td>
	</tr>
	<tr name="PREVIEW_PICTURE_FIELD" id="PREVIEW_PICTURE_FIELD" class="adm-detail-file-row">
		<td width="40%" class="adm-detail-valign-top"><?=GetMessage("IBLOCK_PREVIEW_PICTURE")?></td>
		<td width="60%">
			<?
				echo \Bitrix\Main\UI\FileInput::createInstance(array(
					"name" => "PREVIEW_PICTURE",
					"description" => true,
					"upload" => true,
					"allowUpload" => "I",
					"medialib" => true,
					"fileDialog" => true,
					"cloud" => true,
					"delete" => true,
					"maxCount" => 1
				))->show(
					($bVarsFromForm ? $_REQUEST["PREVIEW_PICTURE"] : ($ID > 0 && !$bCopy ? $str_PREVIEW_PICTURE: 0)),
					$bVarsFromForm
				);
			?>
		</td>
	</tr>
	<tr name="DETAIL_PICTURE_FIELD" id="DETAIL_PICTURE_FIELD" class="adm-detail-file-row">
		<td width="40%" class="adm-detail-valign-top"><?=GetMessage("IBLOCK_DETAIL_PICTURE")?></td>
		<td width="60%">
			<?
				echo \Bitrix\Main\UI\FileInput::createInstance(array(
					"name" => "DETAIL_PICTURE",
					"description" => true,
					"upload" => true,
					"allowUpload" => "I",
					"medialib" => true,
					"fileDialog" => true,
					"cloud" => true,
					"delete" => true,
					"maxCount" => 1
				))->show(
					($bVarsFromForm ? $_REQUEST["DETAIL_PICTURE"] : ($ID > 0 && !$bCopy ? $str_DETAIL_PICTURE: 0)),
					$bVarsFromForm
				);
			?>
		</td>
	</tr>
	<tr name="MORE_PICTURE_FIELD" id="MORE_PICTURE_FIELD" style="display: none" class="adm-detail-file-row">
		<td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%"><?=GetMessage("IBLOCK_MORE_PICTURES")?></td>
		<td>
		<?
			echo \Bitrix\Main\UI\FileInput::createInstance(array(
				"name" => "MORE_PICTURE",
				"description" => true,
				"upload" => true,
				"allowUpload" => "I",
				"medialib" => true,
				"fileDialog" => true,
				"cloud" => true,
				"delete" => true,
				"maxCount" => ""
			))->show(
				($bVarsFromForm ? $_REQUEST["MORE_PICTURE"] : ($ID > 0 && !$bCopy ? $str_MORE_PICTURE: 0)),
				$bVarsFromForm
			);
		?>
		</td>
	</tr>				
	<?endif?>	
<?$tabControl->Buttons();?>
	<input type="button" id="start_button" value="Загрузить" OnClick="StartUpload();" class="adm-btn-save" disabled>
<?$tabControl->End();?>
</form>
<script>
	BX.message({
        SELECT_PROP: "<?=GetMessage('SELECT_PROP')?>",
        SELECT_PROP_VALUE: "<?=GetMessage('SELECT_PROP_VALUE')?>",
        IBLOCK_ELEMENTS_UPDATED: "<?=GetMessage('IBLOCK_ELEMENTS_UPDATED')?>",
    });
	let arrResult,
		elementButton = document.getElementById('SELECT_ELEMENT'),
		elementInput = document.getElementById('IBLOCK_ELEMENT'),
		elementSpan = document.getElementById('sp_04695ec4810cefcc174992a7dfdcf0c3_n0'),
		propFilter = document.querySelectorAll('#PROP_FILTER'),
		propFilterValue = document.querySelectorAll('#PROP_FILTER_VALUE'),
		imageProp = document.getElementById('IMAGE_PROP');	
		addProp = document.querySelector(".add_prop");
		
	function ready(){
		elementInput.setAttribute('disabled', true);		
		for (var i = 0; i < propFilter.length; ++i) {
			propFilter[i].setAttribute('disabled', true);
		}
		for (var i = 0; i < propFilterValue.length; ++i) {
			propFilterValue[i].setAttribute('disabled', true);
		}
	
		imageProp.setAttribute('disabled', true);
	};
	document.querySelector("#IBLOCK_ID").addEventListener('change', function (e) 
	{
		getTP(e.target.value);		
	});
	document.querySelector("#IBLOCK_TYPE_ID").addEventListener('change', function (f) 
	{	
		elementInput.setAttribute('disabled', true);
		elementButton.setAttribute('disabled', true);
		elementInput.value = "";
		elementSpan.innerHTML = "";
	});
	document.querySelector('#IBLOCK_ELEMENT').addEventListener('change', function (g) 
	{
		if(g.target.value != null && g.target.value != 0 && g.target.value != '')
		{
			activeFilterProp(arrResult);
			for (var i = 0; i < propFilterValue.length; ++i) {
				propFilterValue[i].innerHTML = "";
				propFilterValue[i].options[propFilterValue[i].options.length] = new Option(BX.message("SELECT_PROP_VALUE"), 0);
			}

			addProp.className = "add_prop enabled";
			imageProp.removeAttribute('disabled');
			imageProp.innerHTML = "";
			imageProp.options[imageProp.options.length] = new Option(BX.message("SELECT_PROP"), 0);
			activeImageProp(arrResult);
			var data = {};
			    data['type'] = 'getElement';
			    data['ELID'] = document.getElementById('IBLOCK_ELEMENT').value;

			BX.ajax.post(
				'image_loader_ajax.php',
				data,
				function(result)
				{
					if (result != null && result != ''){
						elementSpan.innerHTML = result;
					}										
				}
			);			
		}
		else
		{	
			propFilter.setAttribute('disabled', true);
			propFilter.innerHTML = "";
			propFilter.options[propFilter.options.length] = new Option(BX.message("SELECT_PROP"), 0);
			imageProp.setAttribute('disabled', true);
			imageProp.innerHTML = "";
			imageProp.options[imageProp.options.length] = new Option(BX.message("SELECT_PROP"), 0);
		}		
	});
	$(document).on('change', '#PROP_FILTER', function (h) 
	{
		var id = h.target.getAttribute("data-prop-id");

		if (h.target.value != 0)
		{
			var data = {};
			    data['type'] = 'getTpValues';
			    data['ELID'] = document.getElementById('IBLOCK_ELEMENT').value;
				data['IBID'] = document.getElementById('IBLOCK_ID').value;
				data['PROPID'] = h.target.value;
				
			BX.ajax.post(
				'image_loader_ajax.php',
				data,
				function(result)
				{
					propResult = JSON.parse(result);
					if (propResult != null && propResult != ''){
						activeFilterPropValue(propResult, id);
					}										
				}
			);
		}
		else
		{
			var propValElement = document.querySelector('[data-prop-value-id="'+id+'"]')
			propValElement.setAttribute('disabled', true);
			propValElement.innerHTML = "";
			propValElement.options[propValElement.options.length] = new Option(BX.message("SELECT_PROP_VALUE"), 0);
		}
	});
	document.querySelector('#PROP_FILTER_VALUE').addEventListener('change', function (m) 
	{
		if (m.target.value != 0)
		{
			document.querySelector('#start_button').removeAttribute('disabled');
		}
		else
		{
			document.querySelector('#start_button').setAttribute('disabled', true);
		}
	});
	document.querySelector('#IMAGE_PROP').addEventListener('change', function (k) 
	{
		if(k.target.value != null && k.target.value != 0 && k.target.value != '')
		{
			document.querySelector("#MORE_PICTURE_FIELD").style = "";
		}
		else
		{
			document.querySelector("#MORE_PICTURE_FIELD").style.display = "none";
		}
	});

	function getTP(id){
		del_addedRows();
		for (var i = 0; i < propFilter.length; ++i) {
			propFilter[i].setAttribute('disabled', true);
			propFilter[i].innerHTML = "";
			propFilter[i].options[propFilter[i].options.length] = new Option(BX.message("SELECT_PROP"), 0);
		}
		for (var i = 0; i < propFilterValue.length; ++i) {
			propFilterValue[i].setAttribute('disabled', true);
			propFilterValue[i].innerHTML = "";
			propFilterValue[i].options[propFilterValue[i].options.length] = new Option(BX.message("SELECT_PROP_VALUE"), 0);
		}
		addProp.className = "add_prop disabled";
		imageProp.setAttribute('disabled', true);
		imageProp.innerHTML = "";
		imageProp.options[imageProp.options.length] = new Option(BX.message("SELECT_PROP"), 0);
		elementInput.setAttribute('disabled', true);
		elementButton.setAttribute('disabled', true);
		elementInput.value = "";
		elementSpan.innerHTML = "";
	    var selected = id;

	    if (selected != 0)
	    {
		    var data = {};
			    data['type'] = 'getTp';
				data['IBID'] = selected;
			BX.ajax.post(
				'image_loader_ajax.php',
				data,
				function(result)
				{
					arrResult = JSON.parse(result);
					activeElementBlock(arrResult ,selected);											
				}
			);
		}
	}
	
	function activeElementBlock(arrRes, sel)
	{
		if (!arrRes.ERROR)
		{
			elementInput.removeAttribute('disabled');
			elementButton.removeAttribute('disabled');
			elementButton.onclick = function(){ return jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=ru&IBLOCK_ID='+sel+'&n=IBLOCK_ELEMENT&iblockfix=y&tableId=iblockprop-E-14-2', 900, 700)};			
		}
		else 
		{
			elementInput.value = "";
			elementSpan.style = "color:red";
			elementSpan.innerHTML = arrResult.ERROR;					
		}			
	}
	
	function activeFilterProp(arrPropRes) {
		for (var i = 0; i < propFilter.length; ++i) {
			if (propFilter[i].value == 0){			
				propFilter[i].removeAttribute('disabled');
				propFilter[i].innerHTML = "";
				propFilter[i].options[propFilter[i].options.length] = new Option(BX.message("SELECT_PROP"), 0);
				for (key in arrPropRes.OFFER_PROPS){
					propFilter[i].options[propFilter[i].options.length] = new Option(arrPropRes.OFFER_PROPS[key].NAME, arrPropRes.OFFER_PROPS[key].CODE);
				}
			} 
		}			
	}
	
	function activeFilterPropValue(arrPropValuesRes, id) {
		var propFilterValueElement = document.querySelector('[data-prop-value-id="'+id+'"]');
		propFilterValueElement.removeAttribute('disabled');
		propFilterValueElement.innerHTML = "";
		propFilterValueElement.options[propFilterValueElement.options.length] = new Option(BX.message("SELECT_PROP_VALUE"), 0);
		for (key in arrPropValuesRes){
			propFilterValueElement.options[propFilterValueElement.options.length] = new Option(arrPropValuesRes[key].NAME, arrPropValuesRes[key].VALUE);

		}				
	}
	
	function activeImageProp(arImgRes)
	{
		for (key in arImgRes.IMAGE_PROPS){
			imageProp.options[imageProp.options.length] = new Option(arImgRes.IMAGE_PROPS[key].NAME, arImgRes.IMAGE_PROPS[key].CODE);
		}
	}

	$('.add_prop').click(function(){
		if ($(this).hasClass("enabled")){
			var newPropId = document.querySelectorAll("#PROP_FILTER").length;
			$("#addProp").before('<tr class="adm-detail-required-field added" id="'+newPropId+'">\
			<td width="50%" class="adm-detail-content-cell-l"></td>\
			<td width="50%" class="adm-detail-content-cell-r">\
				<select id="PROP_FILTER" data-prop-id='+newPropId+' name="PROP_FILTER" class="adm-detail-iblock-types">\
					<option value="0">'+BX.message("SELECT_PROP")+'</option>\
				</select>&nbsp;&nbsp;\
				<select id="PROP_FILTER_VALUE" data-prop-value-id='+newPropId+' name="PROP_FILTER_VALUE" class="adm-detail-iblock-list" disabled>\
					<option value="0">'+BX.message("SELECT_PROP_VALUE")+'</option>\
				</select><a data-del-id="'+newPropId+'" class="del_button">x</a>\
			</td>\
		</tr>');
		}
		var newProp = document.querySelector('[data-prop-id="'+newPropId+'"]');
		newProp.removeAttribute('disabled');
		newProp.innerHTML = "";
		newProp.options[newProp.options.length] = new Option(BX.message("SELECT_PROP"), 0);
		for (key in arrResult.OFFER_PROPS){
			newProp.options[newProp.options.length] = new Option(arrResult.OFFER_PROPS[key].NAME, arrResult.OFFER_PROPS[key].CODE);
		}
	});
	function del_addedRows(){
		var addedRows = document.querySelectorAll(".added");
		for (var i = 0; i < addedRows.length; ++i) {
			addedRows[i].remove();
		}
	}
	$(document).on("click", ".del_button", function(){
		$('#'+$(this).data("del-id")).remove();
	});

	function StartUpload (){
		var formData = new FormData();
		var data = {};
		data['type'] = 'uploadIMG';
		formData = $("#iblock_image_loader").serializeArray();

		var objData = {};
		var count = -1;
		objData['PROP_FILTER'] = [];
		objData['PROP_FILTER_VALUE'] = [];
		$(formData).each(function(index, obj){
			if(obj.name.indexOf('MORE_PICTURE') == 0){
				if(obj.name == 'MORE_PICTURE[name]')
					count ++;
				objData[obj.name + count] = obj.value;
			}
			else
			{
				if (obj.name == 'PROP_FILTER' || obj.name == 'PROP_FILTER_VALUE'){
					objData[obj.name].push(obj.value);
				}
				else{
					objData[obj.name] = obj.value;
				}
			}
		});
		
		data['form'] = JSON.stringify(objData);
		BX.ajax.post(
			'image_loader_ajax.php',
			data,
			function(result)
			{
				if (result && result > 0)
				{
					$('#tbl_iblock_export_result_div').html();
					$('#tbl_iblock_export_result_div').html('<div class="adm-info-message-wrap adm-info-message-gray"><div class="adm-info-message"><div class="adm-info-message-title"></div>'+BX.message("IBLOCK_ELEMENTS_UPDATED")+ +result+'<div class="adm-info-message-buttons"></div></div></div>');											
				}
			}
		);
	}
	document.addEventListener("DOMContentLoaded", ready);
</script>
<style>
	.add_prop {
		text-decoration: underline;
    	cursor: pointer;
	}
	.disabled {
		opacity: 0.5;
		cursor: inherit;
	}
	.del_button {
		margin-left: 10px;
	    font-size: 15px;
	    border: 1px solid;
	    vertical-align: middle;
	    padding: 0 6px 2px;
	    border-radius: 50%;
	    width: 20px;
	    cursor: pointer;
	}
</style>