<?php

namespace Sprint\Migration;


class Task_243321_20231029195230 extends Version
{
    protected $description = "Новое св-во Активная размерная характеристика для Санкт Петербурга в ИБ \"Пакет предложений\"";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('offers', 'catalog');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Активная размерная характеристика СПБ',
  'ACTIVE' => 'Y',
  'SORT' => '10000',
  'CODE' => 'SELECTED_SIZE_CHARACT_SPB',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'S',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => '',
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'IS_REQUIRED' => 'N',
  'VERSION' => '2',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
));
    
    }

    public function down()
    {
        //your code ...
    }
}
