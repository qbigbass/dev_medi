<?

use \TwoFingers\Location\Settings;

/**
 * Class TF_LOCATION_Settings
 *
 *
 * @deprecated
 */
class TF_LOCATION_Settings
{
    

    

    /**
     * @param $arFields
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     *
     * @deprecated
     */
    public static function SetSettings($arFields)
    {
        Settings::setList($arFields);
    }
}