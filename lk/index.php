<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/lk.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/lk.js');

$APPLICATION->AddHeadString('<meta name="googlebot" content="noindex" />');

include("pages/init.inc.php");

if ($USER->IsAuthorized())
{
    // получаем данные клиента из Лоймакса
	$user_info = $api->getUserData();

    // проверяем есть ли ID в базе, если нет добавляем
    $filter = Array
    (
        'ID'=>$USER->GetID(),
        "UF_LMXID"   => $user_info['data']['id'],
        "UF_LMX_UID" => $user_info['data']['personUid']
    );
    $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
    if (!$arUser = $rsUsers->GetNext()) {

        $fields = [];
        $fields = Array(
            "UF_LMXID" => $user_info['data']['id'],
            "UF_LMX_UID" => $user_info['data']['personUid']
        );
        $USER->Update($USER->GetID(), $fields);
    }

	$UserQuestions = $api->getUserQuestions();

//__($balance);

//__($detail_balance);

	$OldCard = '';
			//	print_r($questions);
	if (is_array($UserQuestions['data']) && !empty($UserQuestions['data'])){
		foreach ($UserQuestions['data'] as $key => $data) {

			foreach($data['questions'] as $q){
				if ($q['logicalName'] == 'Sex')
				{
					foreach($q['fixedAnswers'] as $answ)
					{
						$genders[$answ['id']] = $answ['name'];
					}
					$sex = $genders[$q['answer']['fixedAnswerIds'][0]];

				}
			}
		}
	}
    else
    {
        //LocalRedirect('/lk/');
    }

	$active_tab = 'main';
	if (isset($_REQUEST['RESULT_ID']) || isset($_REQUEST['letter']) || isset($_REQUEST['WEB_FORM_ID']))
	{
		$active_tab = 'letter';
	}
	if (isset($_REQUEST['history']))
	{
		$active_tab = 'history';
	}
	if (isset($_REQUEST['orders']))
	{
		$active_tab = 'orders';
	}
	?>

<div class="row flex tabs-wrap">

	<?include("pages/sidebar.inc.php");?>

	<div class="tabs-content col-12 col-lg-9">
		<div id="lk" class="tab-content  <?if ($active_tab == 'main' || !$active_tab){?>active<?}?>">
<!--	  Личный кабинет             		-->
			<?include("pages/anketa.inc.php");?>

<!--      Редактирование контактов			-->
			<?include("pages/edit_contact.inc.php");?>

		</div>
		<div id="orders" class="tab-content <?if ($active_tab == 'orders'){?>active<?}?>">
			<?include("pages/orders.inc.php");?>
		</div>
		<div id="history" class="tab-content <?if ($active_tab == 'history'){?>active<?}?>">
			<?include("pages/history.inc.php");?>
		</div>

		<div id="letter" class="tab-content  <?if ($active_tab == 'letter'){?>active<?}?>">
			<?// Письмо директору?>

				<?$APPLICATION->IncludeComponent(
			"bitrix:form.result.new",
			"lk_new",
			array(
				"CACHE_TIME" => "360000",
				"CACHE_TYPE" => "Y",
				"CHAIN_ITEM_LINK" => "",
				"CHAIN_ITEM_TEXT" => "",
				"EDIT_URL" => "",
				"IGNORE_CUSTOM_TEMPLATE" => "N",
				"LIST_URL" => "",
				"SEF_MODE" => "N",
				"SUCCESS_URL" => "",
				"USE_EXTENDED_ERRORS" => "Y",
				"WEB_FORM_ID" => "8",
				"COMPONENT_TEMPLATE" => ".default",
				"VARIABLE_ALIASES" => array(
					"WEB_FORM_ID" => "WEB_FORM_ID",
					"RESULT_ID" => "RESULT_ID",
				)
			),
			false
		);?>

			<br><br>
		</div>
	</div>
</div>

<?}?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
