<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 12.12.2019
 * Time: 17:26
 *
 *
 */

namespace TwoFingers\Location\Helper;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;
use TwoFingers\Location\Settings;

Loc::loadMessages(__FILE__);

/**
 * Class Options
 *
 * @package TwoFingers\Location
 *
 */
class Tabs
{
    const MAIN      = 'main';
    const LIST      = 'list';
    const CONFIRM   = 'confirm';
    const SALE      = 'sale';

    /**
     * @return array[]
     */
    public static function getMap(): array
    {
        $aTabs = [
            [
                'TAB'   => Loc::getMessage('tfl__tab-main'),
                'DIV'   => self::MAIN,
                'TITLE' => Loc::getMessage('tfl__tab-main_TITLE'),
            ],
            [
                'TAB' => Loc::getMessage('tfl__tab-popup-list'),
                'DIV' => self::LIST,
                'TITLE' => Loc::getMessage('tfl__tab-popup-list_DESCR'),
            ],
            [   'TAB' => Loc::getMessage('tfl__tab-popup-confirm'),
                'DIV' => self::CONFIRM,
                'TITLE' => Loc::getMessage('tfl__tab-popup-confirm_DESCR'),
            ]];

        if (Location::getType() != Location::TYPE__INTERNAL)
            $aTabs[] = [
                'TAB' => Loc::getMessage('tfl__tab-sale'),
                'DIV' => self::SALE,
                'TITLE' => Loc::getMessage('tfl__tab-saleDESCR'),
            ];

        return $aTabs;
    }

    /**
     * @return \array[][]
     * @TODO: add all tabs
     */
    public static function getAllOptions(): array
    {
        return [
            self::SALE => self::getSaleOptions()
        ];
    }

    /**
     * @return array[]
     */
    public static function getSaleOptions(): array
    {
        return [
            ['TF_LOCATION_TEMPLATE', Loc::getMessage('TF_LOCATION_TEMPLATE'), '', ["checkbox", '',
                self::getCheckboxHelp(Loc::getMessage('TF_LOCATION_TEMPLATE_HELP'))]],
            ['TF_LOCATION_DELIVERY', Loc::getMessage('TF_LOCATION_DELIVERY'), '', ["checkbox"]],
            ['TF_LOCATION_DELIVERY_ZIP', Loc::getMessage('TF_LOCATION_DELIVERY_ZIP'), '', ["checkbox", '',
                self::getCheckboxHelp(Loc::getMessage('TF_LOCATION_DELIVERY_ZIP_HELP'))]],
            [Options::ORDER_LINK_CLASS, Loc::getMessage('tfl__' . Options::ORDER_LINK_CLASS), '', ['text', 40]]
        ];
    }

    /**
     * @param      $help
     * @param bool $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCheckboxHelp($help, $br = false): string
    {
        $help = trim($help);
        if (!strlen($help))
            return '';

        return '>' . ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span';
    }

    /**
     * @param       $help
     * @param false $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getHelp($help, $br = false): string
    {
        $help = trim($help);
        if (!strlen($help))
            return '';

        return ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span>';
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showTextRow($code)
    {
        ?><tr><?php
            self::showLabel($code);
            self::showTextCell($code);
        ?></tr><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showCheckboxRow($code)
    {
        ?><tr><?php
            self::showLabel($code);
            self::showInputCheckbox($code);
        ?></tr><?php
    }

    /**
     * @param $code
     */
    public static function showLabel($code)
    {
        $loc = Loc::getMessage($code) ? : Loc::getMessage('tfl__' . $code);

        ?><td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="<?=$code?>"><?=$loc ?>:</label>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showSelectBoxRow($code)
    {
        ?><tr><?php
            self::showLabel($code);
            self::showInputSelectBox($code);
        ?></tr><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showTextCell($code)
    {
        ?><td width="60%" class="adm-detail-content-cell-r">
        <? self::showTextInput($code);?>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showTextInput($code)
    {
        $settingsMap= Settings::getMap();

        ?><input size="<?=isset($settingsMap[$code]['size']) ? $settingsMap[$code]['size'] : '40'?>" type="text" name="<?=$code?>" id="<?=$code?>" value="<?=Options::getValue($code)?>">
        <?self::showPostInput($code);?>
        <?self::showHelp($code);
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showInputSelectBox($code)
    {
        $settingsMap= Settings::getMap();
        ?>
        <td width="60%" class="adm-detail-content-cell-r">
        <select name="<?=$code?>" id="<?=$code?>">
            <?php foreach ($settingsMap[$code]['options'] as $value => $name ):?>
                <option value="<?=$value?>"<?if (Options::getValue($code) == $value):?> selected<?endif?>><?=$name?></option>
            <?php endforeach;?>
        </select>
        <?php self::showHelp($code);?>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showInputCheckbox($code)
    {
        ?><td width="60%" class="adm-detail-content-cell-r">
            <input type="checkbox" name="<?=$code?>" id="<?=$code?>" value="Y" <?if (Options::getValue($code) == 'Y'):?> checked<?endif?>>
            <?php self::showHelp($code);?>
        </td><?php
    }

    /**
     * @param $code
     */
    protected static function showHelp($code)
    {
        $help = Loc::getMessage($code . '_HELP') ?: Loc::getMessage('tfl__' . $code . '_help');
        if (strlen($help)): ?>
            <div class="tfl-help"><?=$help?></div>
        <?php endif;
    }

    /**
     * @param $code
     */
    protected static function showPostInput($code)
    {
        $postInput = Loc::getMessage($code . '_POST_INPUT') ?: Loc::getMessage('tfl__' . $code . '_post-input');
        if (strlen($postInput))
            echo $postInput;
    }
}