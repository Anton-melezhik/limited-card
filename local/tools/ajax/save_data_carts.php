<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_REQUEST['deal_id'] && $_REQUEST['id']) {
    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');
    //$entity_data_class_dop = MyHelper::getHlClassByName('DopWorkOrMinus');

    //вытаскиваем стары данные из лимитной карточке
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('UF_DEAL', "UF_DATA", "UF_DS"),
        'filter' => array('ID'=>$_REQUEST['id'])
    ));

    $data = $rsData->fetch();
    $data=json_decode($data['UF_DATA'], true);

    foreach ($_REQUEST['data'] as $id_work => $item) {

        //поиск работ с отрицательным остатком
        //проверка на отрицательный баланс для текущих услуг и добавление в HB DopWorkOrMinus
        /*if ($item['vol-balance']<0 && !strpos($id_work, "ds") && $id_work!="users") {
            $rsData_work = $entity_data_class_dop::getList(array(
                'order' => array('ID'=>'ASC'),
                'select' => array(),
                'filter' => array('UF_DEAL'=>$_REQUEST['deal_id'], "UF_WORK_ID" =>$id_work)
            ));
            if($work = $rsData_work->fetch()){
                //такая уже есть, создавать не будем
            } else {
                //надо написать что есть работа с отрицательным балансом
                $entity_data_class_dop::add(
                    array(
                    "UF_DEAL" => $_REQUEST['deal_id'],
                    "UF_WORK_ID" => $id_work,
                    "UF_DATE" => date("d.m.Y"),
                    "UF_TYPE" => "minus",
                    )
                );
            }
        }

        //поиск новых доп работ
        //тут надо намутить скрипт на проверку добавления новых доп услуг в HB DopWorkOrMinus
        if (strpos($id_work, "ds")) {
            if ($data[$id_work]) {
                //такая доп работа уже была
            } else {
                //надо добавить доп работу в таблицу учета ДС
                $entity_data_class_dop::add(
                    array(
                        "UF_DEAL" => $_REQUEST['deal_id'],
                        "UF_WORK_ID" => str_replace("_ds","", $id_work),
                        "UF_DATE" => date("d.m.Y"),
                        "UF_TYPE" => "dop",
                    )
                );
            }
        }*/
    }

    $entity_data_class::update($_REQUEST['id'], array(
        "UF_DATA" => json_encode($_REQUEST['data']),
    ));
    echo "элемент найден, надо его обновить с новыми данными";
}

