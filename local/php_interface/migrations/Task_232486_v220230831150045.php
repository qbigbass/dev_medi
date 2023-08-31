<?php

namespace Sprint\Migration;


class Task_232486_v220230831150045 extends Version
{
    protected $description = "HL Эксперты к постам энциклопедии ";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'ExpertsEncPosts',
  'TABLE_NAME' => 'experts_enc_posts',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Эксперты к постам энциклопедии ',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ENC_EXPERT_IMG',
  'USER_TYPE_ID' => 'file',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'LIST_WIDTH' => 200,
    'LIST_HEIGHT' => 200,
    'MAX_SHOW_SIZE' => 0,
    'MAX_ALLOWED_SIZE' => 0,
    'EXTENSIONS' => 
    array (
    ),
    'TARGET_BLANK' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Фото эксперта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Фото эксперта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Фото эксперта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ENC_EXPERT_PROF',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Специализация эксперта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Специализация эксперта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Специализация эксперта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ENC_EXPERT_EXP',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Стаж',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Стаж',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Стаж',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ENC_EXPERT_NAME',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'ФИО',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'ФИО',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'ФИО',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_XML_ID',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => '',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => '',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => '',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
  ),
));
        }

    public function down()
    {
        //your code ...
    }
}
