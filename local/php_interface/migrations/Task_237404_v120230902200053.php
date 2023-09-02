<?php

namespace Sprint\Migration;


class Task_237404_v120230902200053 extends Version
{
    protected $description = "Создание нового св-ва Значение сортировки без метки без бренда в ИБ Основной каталог изделий";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('base-catalog', 'catalog');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Значение сортировки без метки без бренда',
  'ACTIVE' => 'Y',
  'SORT' => '10000',
  'CODE' => 'SORT_DEFAULT',
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
