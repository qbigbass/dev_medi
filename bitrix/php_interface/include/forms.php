<?

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

AddEventHandler('form', 'onBeforeResultAdd', 'modifyBeforeResultAdd');

AddEventHandler('form', 'onAfterResultAdd', 'modifyAfterResultAdd');

function modifyBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
{
  global $APPLICATION;

    $phone = '';
    $phone_fields = [17,24,34,66,76,81,86,116,146,176,182,212,2,6,10, 242, 251];
    foreach($phone_fields as $pf)
    {
        if (!empty($arrVALUES['form_text_'.$pf]))
        {
            $parsedPhone = Parser::getInstance()->parse($arrVALUES['form_text_'.$pf]);
            $phone =  $parsedPhone->format(Format::E164);
            $pf_id = $pf;
        }
    }
    if ($phone != '')
    {
        set_msuid($phone);

    }
  //  форма  ID=12 (Подолог)
  if ($WEB_FORM_ID == 12)
  {
      $statuses = ['s1'=> 54, 's2' => 61, 's3' => 55, 's4' => 56, 's5' => 57, 's6' => 58, 's7' => 59, 's8' => 60];

      $arFields['STATUS_ID'] = $statuses[SITE_ID];
  }
   //  форма  ID=6 (Сканирование)
  elseif ($WEB_FORM_ID == 6)
    {
        $statuses = ['s1'=> 20, 's2' => 21, 's3' => 22, 's4' => 23, 's5' => 24, 's6' => 25, 's7' => 26, 's8' => 27];

        $arFields['STATUS_ID'] = $statuses[SITE_ID];
    }
    elseif ($WEB_FORM_ID == 9)
      {
          if (SITE_ID == 's2') {

              $arFields['STATUS_ID'] = 34;
          }

      }
    elseif ($WEB_FORM_ID == 13)
      {
          if (SITE_ID == 's2') {

               $arFields['STATUS_ID'] = 66;
          }

      }
      elseif ($WEB_FORM_ID == 14)
        {
            $statuses = ['s1'=> 70, 's2' => 71, 's3' => 72, 's4' => 73, 's5' => 74, 's6' => 75, 's7' => 76, 's8' => 77];

            $arFields['STATUS_ID'] = $statuses[SITE_ID];
        }

        elseif ($WEB_FORM_ID == 15)
          {
              $statuses = ['s1'=> 78, 's2' => 79, 's3' => 80, 's4' => 81, 's5' => 82, 's6' => 83, 's7' => 84, 's8' => 85];

              $arFields['STATUS_ID'] = $statuses[SITE_ID];
          }    elseif ($WEB_FORM_ID == 16)
                {
                    if (SITE_ID == 's2') {

                        $arFields['STATUS_ID'] = 87;
                    }

                }
}
function modifyAfterResultAdd($WEB_FORM_ID, $RESULT_ID)
{
  global $APPLICATION;

    if (isset($_COOKIE['_ga']) && in_array($WEB_FORM_ID, [2,3, 4, 5,6,7,8,9,10,11,12,13,14,15,17])) {
        CFormResult::SetField($RESULT_ID, 'GCID', $_COOKIE['_ga']);
    }
    if (isset($_COOKIE['_msuid']) && in_array($WEB_FORM_ID, [2,3, 4, 5,6,7,8,9,10,11,12,13,14,15,17])) {
        CFormResult::SetField($RESULT_ID, 'MSUID', $_COOKIE['_msuid']);
    }
    if (isset($_COOKIE['_ym_uid']) && in_array($WEB_FORM_ID, [2,3, 4, 5,6,7,8,9,10,11,12,13,14,15,17])) {
        CFormResult::SetField($RESULT_ID, 'YCID', $_COOKIE['_ym_uid']);
    }
    else
    {
        $arAnswer = CFormResult::GetDataByID(
            $RESULT_ID,
            array(),
            $arResult,
            $arAnswer2);
        if (!empty($arAnswer2['PHONE']))
        {
            $res = array_values($arAnswer2['PHONE']);
            $phone = $res[0]['USER_TEXT'];
            if (!empty($phone))
            {
                $msuid = set_msuid($phone);
                CFormResult::SetField($RESULT_ID, 'MSUID', $msuid);
            }
        }

    }


}
