<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//dump($_REQUEST);
if ($_REQUEST['id']>0){
    $entity_data_class = MyHelper::getHlClassByName('TimeWorkMont');
    $date_work= $_REQUEST['id_elem'];
    $IBLOCK_MONT=MyHelper::getIblockId("installers");
    $users_mont=[];
    $res = \CIBlockElement::GetList($sort['sort'], ['IBLOCK_ID'=>$IBLOCK_MONT, "ACTIVE"=>"Y", "PROPERTY_RP"=>$_REQUEST['id']], false, false, ['NAME', 'ID']);
    while($row = $res->GetNext()) {

        $rsData = $entity_data_class::getList(array(
            'order' => array('UF_USER'=>'ASC'),
            'select' => array('*'),
            'filter' => ["UF_USER"=>$row['ID'], "UF_DATE"=> $date_work],
        ));
        if($el = $rsData->fetch()){
            //такой работник за этот день уже найден
            //echo "find";
        } else {
            $users_mont[]=[
                "value" => $row['ID'],
                "text" => $row['NAME'],
            ];
        }
    }
    //dump($users_mont);
    if (count($users_mont)>0) {
        echo json_encode($users_mont, JSON_UNESCAPED_UNICODE);
    } else {
        echo "error";
    }

}


