<?php

namespace Sprint\Migration;


class Task_237404_20230912100337 extends Version
{
    protected $description = "Создание нового св-ва Значение сортировки бренда в ИБ Бренды";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('brands', 'info');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Значение сортировки бренда',
  'ACTIVE' => 'Y',
  'SORT' => '500',
  'CODE' => 'SORT_PRODUCT_BRAND',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'N',
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
