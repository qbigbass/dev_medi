<?php

namespace Sprint\Migration;


class Task_232486_v120230831150022 extends Version
{
    protected $description = "HL Лайки к постам энциклопедии";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'LikesEncPosts',
  'TABLE_NAME' => 'likes_enc_posts',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Лайки к постам энциклопедии',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_CLIENT_IP',
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
    'ru' => 'CLIENT_IP',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'CLIENT_IP',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'CLIENT_IP',
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
  'FIELD_NAME' => 'UF_ENC_POST_ID',
  'USER_TYPE_ID' => 'integer',
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
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'ENC_POST_ID',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'ENC_POST_ID',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'ENC_POST_ID',
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
