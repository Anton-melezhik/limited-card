<?php

namespace Sprint\Migration;


class HBWORKTIME20210601144742 extends Version
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
  'NAME' => 'TimeWorkMont',
  'TABLE_NAME' => 'b_timeworkmont',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Рабочие часы монтажников по объектам',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_USER',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '10',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'I',
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
    'ru' => 'ID пользователя',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID пользователя',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID пользователя',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'ID пользователя',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DEAL',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '20',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'I',
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
    'ru' => 'ID объекта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID объекта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID объекта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'ID объекта',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_CARD',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '30',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'I',
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
    'ru' => 'ID карточки',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID карточки',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID карточки',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'ID карточки',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '40',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
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
    'ru' => 'Дата записи работника',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата записи работника',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Дата записи работника',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Дата записи работника',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_TYPEWORK',
  'USER_TYPE_ID' => 'hlblock',
  'XML_ID' => '',
  'SORT' => '50',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DISPLAY' => 'CHECKBOX',
    'LIST_HEIGHT' => 5,
    'HLBLOCK_ID' => 'TypeWorkMont',
    'HLFIELD_ID' => 0,
    'DEFAULT_VALUE' => 0,
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Тип работы',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Тип работы',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Тип работы',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Тип работы',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_WORK',
  'USER_TYPE_ID' => 'integer',
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
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID проделанной работы',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID проделанной работы',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'ID проделанной работы',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'ID проделанной работы',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_WIDTH',
  'USER_TYPE_ID' => 'double',
  'XML_ID' => '',
  'SORT' => '70',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'PRECISION' => 4,
    'SIZE' => 20,
    'MIN_VALUE' => 0.0,
    'MAX_VALUE' => 0.0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Количество работ',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Количество работ',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Количество работ',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Количество работ',
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DOGOVOR',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'I',
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
    'ru' => 'Договор',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Договор',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Договор',
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
