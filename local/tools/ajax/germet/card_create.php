<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('crm');
$result=[];
if ($_REQUEST['deal_id']) {

    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');
    $start_date_int=strtotime($_REQUEST['start_date']);
    $end_date_int=strtotime($_REQUEST['end_date']);

    //проверка на даты
    if ($end_date_int>$start_date_int) {
        $filter_first=array('UF_DEAL'=>$_REQUEST['deal_id']);
        $rsData = $entity_data_class::getList(array(
            'order' => array('ID'=>'ASC'),
            'select' => array('*'),
            'filter' => $filter_first
        ));
        if($el = $rsData->fetch()){
            $result['error']="Сводная таблица для данной сделки создана!";
        } else {
            $start_date_array = explode("-", $_REQUEST['start_date']);
            $start_date = $start_date_array[2] . "." . $start_date_array[1] . "." . $start_date_array[0];
            $end_date_array = explode("-", $_REQUEST['end_date']);
            $end_date = $end_date_array[2] . "." . $end_date_array[1] . "." . $end_date_array[0];
            //тут нужна проверка на дурака, что бы 2 раза не мог пользователь создать карту для объекта
            $rsData = $entity_data_class::add(array(
                "UF_DEAL" => $_REQUEST['deal_id'],
                "UF_DATA" => json_encode([]),
                "UF_DATE_START" => $start_date,
                "UF_DATE_END" => $end_date,
                "UF_RP" => $_REQUEST['user_rp'],
                "UF_RESPONSIBLE" => json_encode($_REQUEST['users_responsible']),
            ));

            $url = (CMain::IsHTTPS() ? "https://" : "http://") . $_SERVER['SERVER_NAME'];
            $arUpdateData = array("UF_CRM_LIMITEDCART" => $url . "/germet/item.php?DEAL_ID=" . $_REQUEST['deal_id']);
            $deal = new CCrmDeal(false);
            $result_deal = $deal->Update($_REQUEST['deal_id'], $arUpdateData, true, true, array());
            $result['error']='';
            $result['deal_id']=$_REQUEST['deal_id'];
        }
    } else {
        $result['error']="Вы ввели некорректный временной интервал!";
    }

    echo json_encode($result);
}

