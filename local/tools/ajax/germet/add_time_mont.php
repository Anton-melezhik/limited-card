<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//сохраняем значения
//dump($_REQUEST);
$entity_data_class = MyHelper::getHlClassByName('TimeWorkMont');
$date_work=$_REQUEST['id_elem'];
foreach ($_REQUEST['mont'] as $mont => $item_mont) {
    $mont_arr=[];
    $mont_arr['UF_USER']=$mont;
    $mont_arr['UF_DEAL']=$_REQUEST['deal'];
    $mont_arr['UF_CARD']=$_REQUEST['card'];
    $mont_arr['UF_TYPEWORK']=$item_mont['type'];
    $mont_arr['UF_WIDTH']=$item_mont['width'];
    $mont_arr['UF_DOGOVOR']=$item_mont['dog'];
    $mont_arr['UF_WORK']=$_REQUEST['type_work'];
    $mont_arr['UF_DATE']=$date_work;

    //добавляем запись
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        'filter' => ['UF_USER'=> $mont, "UF_DATE"=> $date_work, "UF_DEAL"=> $_REQUEST['deal']]
    ));
    if($el = $rsData->fetch()){
        //echo "Такая запись с этим сотрудником уже есть ее сохранять не будем";
    } else {
        $rsData_add = $entity_data_class::add($mont_arr);
    }
}

echo "ok";