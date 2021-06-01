<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('crm');
CModule::IncludeModule('iblock');
$dbRes = CCrmDeal::GetList(
    array("NAME", "ID"),
    array(
        "ID" => $_REQUEST['deal'],
    ),
    array()
);
if ($arRes = $dbRes->Fetch()) {
    $deals_dogs=$arRes['UF_N_DOG_GER'];
}

$entity_data_class = MyHelper::getHlClassByName('TypeWorkMont');
$rsData = $entity_data_class::getList(array(
    'order' => array('ID'=>'ASC'),
    'select' => array('*'),
));
$arr_type_work=[];
while($el = $rsData->fetch()){
    $arr_type_work[$el['ID']]=$el['UF_NAME'];
}

$entity_data_class = MyHelper::getHlClassByName('TimeWorkMont');
$rsData = $entity_data_class::getList(array(
    'order' => array('ID'=>'ASC'),
    'select' => array('UF_USER'),
    'filter' => ['UF_DEAL'=>$_REQUEST['deal'], 'UF_DATE'=> $_REQUEST['date'], "UF_WORK"=> $_REQUEST['serv']]
));

$users_array=[];
while($el = $rsData->fetch()){
    $users_array[]=$el['UF_USER'];
}

$IBLOCK_MONT=MyHelper::getIblockId("installers");
$users_mont=[];
$res = \CIBlockElement::GetList($sort['sort'], ['IBLOCK_ID'=>$IBLOCK_MONT, "ID"=> $users_array], false, false, ['NAME', 'ID']);
while($row = $res->GetNext()) {
    $users_mont[$row['ID']]=$row['NAME'];
}

$entity_data_class_new = MyHelper::getHlClassByName('TimeWorkMont');
$rsData_new = $entity_data_class_new::getList(array(
    'order' => array('ID'=>'ASC'),
    'select' => array('*'),
    'filter' => ['UF_DEAL'=>$_REQUEST['deal'], 'UF_DATE'=> $_REQUEST['date'], "UF_WORK"=> $_REQUEST['serv']]
));

while($el = $rsData_new->fetch()){
    //dump($el);

    ?>
    <div class="item-mont" data-id="<?=$el['UF_USER']?>">
        <div class="mont-fio"><?=$users_mont[$el['UF_USER']]?></div>
        <div class="mont-dog">
            <select class="deals-dogs">
                <?foreach ($deals_dogs as $key=>$dog){?>
                    <option value="<?=$key?>" <?if ($dog==$el['UF_DOGOVOR']) echo "selected";?>><?=$dog?></option>
                <?}?>
            </select>
        </div>
        <div class="mont-width"><input class="input-number-check" type="number" value="<?=$el['UF_WIDTH']?>"></div>
        <div class="mont-work">
            <select class="slim-mont-type-time mont-type-time">
                <?foreach ($arr_type_work as $key => $type){?>
                    <option value="<?=$key?>" <?if ($key==$el['UF_TYPEWORK']) echo "selected";?>><?=$type?></option>
                <?}?>
            </select>
        </div>
        <div class="mont-close">X</div>
    </div>
<?
}
