<?php

namespace Sprint\Migration;


class HBCARD20210601141440 extends Version
{
    protected $description = "";

    protected $moduleVersion = "3.16.3";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'LimitedCardItem',
  'TABLE_NAME' => 'limit_cards',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Сводные таблицы материалов',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DEAL',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '10',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID сделки',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID сделки',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID сделки',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'ID сделки',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATA',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '20',
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
    'en' => '',
    'ru' => 'Данные по карте',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Данные по карте',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Данные по карте',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Данные по карте',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE_START',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '30',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 
    array (
      'TYPE' => 'NONE',
      'VALUE' => '',
    ),
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата старта проекта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата старта проекта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата старта проекта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Дата старта проекта',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE_END',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '40',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 
    array (
      'TYPE' => 'NONE',
      'VALUE' => '',
    ),
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата окончания проекта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата окончания проекта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата окончания проекта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Дата окончания проекта',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_RP',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '50',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'РП',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'РП',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'РП',
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
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_RESPONSIBLE',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '60',
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
    'en' => '',
    'ru' => 'Ответственные лица',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Ответственные лица',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Ответственные лица',
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
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DOGOVOR',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
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
    'en' => '',
    'ru' => 'Номер договора',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Номер договора',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Номер договора',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Номер договора',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DS',
  'USER_TYPE_ID' => 'boolean',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 0,
    'DISPLAY' => 'RADIO',
    'LABEL' => 
    array (
      0 => '',
      1 => '',
    ),
    'LABEL_CHECKBOX' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Доп. соглашение',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Доп. соглашение',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Доп. соглашение',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Доп. соглашение',
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
