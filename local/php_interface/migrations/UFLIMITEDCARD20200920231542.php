<?php

namespace Sprint\Migration;


class UFLIMITEDCARD20200920231542 extends Version
{
    protected $description = "Пользовательское поле для сохранения ссылки на сводную карту ";

    protected $moduleVersion = "3.14.6";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'CRM_DEAL',
  'FIELD_NAME' => 'UF_CRM_LIMITEDCART',
  'USER_TYPE_ID' => 'url',
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
    'POPUP' => 'Y',
    'SIZE' => 20,
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Карточка материалов',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Карточка материалов',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Карточка материалов',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
