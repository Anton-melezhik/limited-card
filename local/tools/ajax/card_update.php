<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('crm');
if ($_REQUEST['deal_id'] && $_REQUEST['id']) {
    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');
    $start_date_array=explode("-", $_REQUEST['start_date']);
    $start_date=$start_date_array[2].".".$start_date_array[1].".".$start_date_array[0];
    $end_date_array=explode("-", $_REQUEST['end_date']);
    $end_date=$end_date_array[2].".".$end_date_array[1].".".$end_date_array[0];
    $entity_data_class::update($_REQUEST['id'], array(
        "UF_DOGOVOR" => $_REQUEST['dogovor'],
        "UF_DATE_START" => $start_date,
        "UF_DATE_END" => $end_date,
        "UF_RESPONSIBLE" => json_encode($_REQUEST['users_responsible']),
    ));
}

