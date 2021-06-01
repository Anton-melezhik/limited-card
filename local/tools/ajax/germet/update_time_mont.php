<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//сохраняем значения
//dump($_REQUEST);
$entity_data_class = MyHelper::getHlClassByName('TimeWorkMont');
$date_work=$_REQUEST['id_elem'];
foreach ($_REQUEST['mont'] as $mont => $item_mont) {
    $mont_arr=[];
    $mont_arr['UF_TYPEWORK']=$item_mont['type'];
    $mont_arr['UF_WIDTH']=$item_mont['width'];
    $mont_arr['UF_DOGOVOR']=$item_mont['dog'];
    //обновляем запись
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        'filter' => ['UF_USER'=> $mont, "UF_DATE"=> $date_work, "UF_DEAL"=> $_REQUEST['deal']]
    ));
    if($el = $rsData->fetch()){
        //echo "Такая запись с этим сотрудником уже есть ее будем обновлять ";
        $rsData_add = $entity_data_class::update($el['ID'], $mont_arr);
    } else {

    }
}
echo "ok";