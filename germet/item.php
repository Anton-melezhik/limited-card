<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
use Bitrix\Main\Page;
Page\Asset::getInstance()->addJs('/dist/libs/fancybox/js/jquery.fancybox.js');
Page\Asset::getInstance()->addCss('/dist/libs/fancybox/css/jquery.fancybox.css');
Page\Asset::getInstance()->addJs('/dist/libs/slimselect.min.js');
Page\Asset::getInstance()->addCss('/dist/libs/slimselect.min.css');
Page\Asset::getInstance()->addCss('/dist/css/refbook.css');
Page\Asset::getInstance()->addCss('/dist/css/germet.css');
Page\Asset::getInstance()->addCss('/dist/css/icons/style_icons.css');
Page\Asset::getInstance()->addCss('/dist/css/table-scroll.css');
Page\Asset::getInstance()->addJs('/dist/libs/table-scroll.min.js');
CJSCore::Init(array('jquery2'));
$APPLICATION->SetTitle('Сводная таблица');
global $USER;
$USER_ID=$USER->GetID();
?>

<?php
if ($_REQUEST['DEAL_ID']>0) {

    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');

    if ($_REQUEST['DS']>0) {
        $filter_first=array('UF_DEAL'=>$_REQUEST['DEAL_ID'], "ID" => $_REQUEST['DS']);
    } else {
        $filter_first=array('UF_DEAL'=>$_REQUEST['DEAL_ID']);
    }

    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        'filter' => $filter_first
    ));
    if($el = $rsData->fetch()){
        //Номер элемента в справочнике HB
        $EL_ID=$el['ID'];
        $res = Bitrix\Main\UserTable::getList([
            'select' => ["ID", 'XML_ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'ACTIVE', 'WORK_POSITION', "UF_DEPARTMENT"],
            'order' => ["ID" => "asc"],
            'filter' => ["!UF_DEPARTMENT" => false],
        ]);

        $users_array=[];
        while ($arUser = $res->fetch()) {
            $users_array[$arUser['ID']]=$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'];
        }
        //найдем всех РП
        $group_rp=MyHelper::getUserGroupByCode("rp_group");
        $users_array_rp=[];
        $res = Bitrix\Main\UserTable::getList([
            'select' => ["ID", 'XML_ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'ACTIVE', 'WORK_POSITION', "UF_DEPARTMENT"],
            'order' => ["ID" => "asc"],
            'filter' => ["!UF_DEPARTMENT" => false, "Bitrix\Main\UserGroupTable:USER.GROUP_ID"=>$group_rp],
        ]);

        while ($arUser = $res->fetch()) {
            $users_array_rp[$arUser['ID']]=$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'];
        }

        $date_start_for_table=$el['UF_DATE_START']->format('Y-m-d');
        $date_end_for_table=$el['UF_DATE_END']->format('Y-m-d');

        $date_start=$el['UF_DATE_START']->format('d.m.Y');
        $date_end=$el['UF_DATE_END']->format('d.m.Y');

        $rp_id=$el['UF_RP'];
        $n_dogovor=$el['UF_DOGOVOR'];
        $responsible_array=(array)json_decode($el['UF_RESPONSIBLE']);

        $responsible_array_new=[];
        foreach ($responsible_array as $key=>$item) {
            $responsible_array_new[$item]=$users_array[$item];
        }
        $responsible_array=$responsible_array_new;
        $DATA_MAIN=json_decode($el['UF_DATA'],true);
        $dbRes = CCrmDeal::GetList(
            array("NAME", "ID"),
            array(
                "ID" => $_REQUEST['DEAL_ID'],
            ),
            array()
        );
        if ($arRes = $dbRes->Fetch()) {
            $DEAL_ID=$arRes['ID'];
            $DEAL_NAME=$arRes['TITLE'];
            $deals_dogs=$arRes['UF_N_DOG_GER'];
        }

        echo "<input type='hidden' class='deal-id-this' value='".$el['UF_DEAL']."'>";
        echo "<input type='hidden' class='date-start-this' value='".$el['UF_DATE_START']->format('d.m.Y')."'>";
        echo "<input type='hidden' class='date-end-this' value='".$el['UF_DATE_END']->format('d.m.Y')."'>";
    } else {
        die('Для этой сделки таблицы нет!');
    };

    $end=new DateTime($date_end_for_table);
    $end = $end->modify( '+1 day' );
    $period = new DatePeriod(
        new DateTime($date_start_for_table),
        new DateInterval('P1D'),
        $end
    );


    $month_rus=[
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];


//массив по объекту
    $time_obj=[];
    $month=[];
    $Years=[];
    foreach ($period as $key => $value) {
        $time_obj[date_format($value, 'n')."-".date_format($value, 'Y')][]=date_format($value, 'j');
    }

    foreach ($time_obj as $key => $item) {
        $key_array=explode('-', $key);
        $month[]=$key_array[0];
        $Years[]=$key_array[1];
    }
    $Years=array_unique($Years);

    //Получаем данные из справочника главного ID=5 для герметизации заменяем статическими значениями
    /*$array_main_ref=[];
    $array_main_ref_header=[];
    $array_main_ref_dop=[];
    $entity_data_class = MyHelper::getHlClassByName('RefBookMain');
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        //'filter' => array('!UF_NAME'=>false)
    ));
    while($el = $rsData->fetch()){
        $array_main_ref[$el['ID']]=$el;
        $array_main_ref_header[$el['ID']]=$el;
        $array_main_ref_dop[$el['ID']]=$el;
    }*/


    // выбираем монтажников из ИБ
    $IBLOCK_MONT=MyHelper::getIblockId("installers");
    $users_mont=[];
    $res = \CIBlockElement::GetList($sort['sort'], ['IBLOCK_ID'=>$IBLOCK_MONT, 'ACTIVE'=> 'Y'], false, false, ['NAME', 'ID']);
    while($row = $res->GetNext()) {
        $users_mont[$row['ID']]=$row['NAME'];
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

    $array_main_ref_header=[
        1 => [ "UF_NAME"=> "Запенивание межпанельного шва в составе комплекса работ по герметизации межпанельного шва", "UF_TYPE" => "шт."],
        2 => [ "UF_NAME"=>"Демонтаж излишков пены, монтаж вилатерма в составе комплекса работ по герметизации межпанельного шва", "UF_TYPE" => "шт."],
        3 => [ "UF_NAME"=>"Нанесение герметика в составе комплекса работ по герметизации межпанельного шва", "UF_TYPE" => "шт."],
        4 => [ "UF_NAME"=>"Монтаж деформационного шва", "UF_TYPE" => "шт."],
        5 => [ "UF_NAME"=>"Герметизация швов балкона", "UF_TYPE" => "шт."],
        6 => [ "UF_NAME"=>"Герметизация узла сопряжения оконного блока с наружной панелью", "UF_TYPE" => "шт."],
        7 => [ "UF_NAME"=>"Герметизация вывода фреонопровода на фасад/отверстия под фреонопровод на фасаде", "UF_TYPE" => "шт."],
        8 => [ "UF_NAME"=>"Монтаж декоративных корзин под кондиционеры", "UF_TYPE" => "шт."],
        9 => [ "UF_NAME"=>"Покраска участка фасада в месте демонтажа плитки", "UF_TYPE" => "шт."],
        10 => [ "UF_NAME"=>"Демонтаж керамической плитки", "UF_TYPE" => "шт."],
        11 => [ "UF_NAME"=>"Монтаж керамической плитки", "UF_TYPE" => "шт."],
        12 => [ "UF_NAME"=>"Помывка фасада водой", "UF_TYPE" => "шт."],
        13 => [ "UF_NAME"=>"Помывка фасада спец. средствами", "UF_TYPE" => "шт."],
        14 => [ "UF_NAME"=>"Помывка окон", "UF_TYPE" => "шт."],
    ];

    //соберем все часы монтажников по данной сделки
    $entity_data_class = MyHelper::getHlClassByName('TimeWorkMont');
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        'filter' => ["UF_DEAL"=> $DEAL_ID]
    ));

    $serv_time_ar=[];
    //посчитаем сумму по услуги
    $serv_sum_work_item=[];
    while($el = $rsData->fetch()){
        //$serv_time_ar[$el['UF_USER']][$el['UF_DATE']->format('Y-m-d')]=$el;
        $serv_time_ar[$el['UF_WORK']][$el['UF_USER']][$el['UF_DATE']->format('j-n-Y')]=$el;
        $serv_sum_work_item[$el['UF_WORK']][]=$el['UF_WIDTH'];
    }

    //Ролевая модель
    //Администратор
    //РП
    // РП может изменить данные номер договора, ответвенных лиц, фактическую дату начала и завершения работ
    $rp_roll=false;
    $admin_roll=false;
    $responsible_roll=false;
    if ($USER_ID==$rp_id) {$rp_roll=true;}
    if ($USER->IsAdmin()) {$admin_roll=true;}
    if ($responsible_array[$USER_ID]) {$responsible_roll=true;}


    echo "<div class='my-debug'>Админ <br/>";
    dump($admin_roll);
    echo "РП <br/>";
    dump($rp_roll);
    echo "Ответвенный <br/>";
    dump($responsible_roll);
    echo "</div>";

    $readonly_model="";
    $respons_model="";

    if ($responsible_roll) {
        $respons_model="readonly";
    }

    //Ответсвенные лица и РП могут наполнять таблицу
    if (!$rp_roll && !$responsible_roll) {
        $readonly_model="readonly";
    }

    if ($admin_roll) {
        $readonly_model="";
    }

    $readonly_model_for_respons="";
    if ($responsible_roll || $rp_roll) {
        $readonly_model_for_respons="readonly";
    }

    //блок просчет дат в зависимоти от времени суток
    $hour=date('G');
    $date_for_save=date("d.m.Y");
    if ($hour>=17) $date_for_save=date("d.m.Y");
    if ($hour<12) $date_for_save=date("d.m.Y", time()-3600*20);

    ?>
    <?/*<div class="test_debug"> </div>*/?>

    <input type="hidden" class="deal-id" value="<?=$DEAL_ID?>">
    <input type="hidden" class="card-id" value="<?=$EL_ID?>">
    <?if ($admin_roll || $rp_roll){?>
        <table class="card-add" style="margin-bottom: 20px;">
            <tr>
                <td>Объект </td>
                <td><a href="/crm/deal/details/<?=$DEAL_ID?>/"><?=$DEAL_NAME?></a>  <input type="hidden" class="deal-id" value="<?=$DEAL_ID?>">
                    <?/*if ($_REQUEST['DS']>0):?>
                        (ДС-<?=$array_ds_key[$_REQUEST['DS']]?>)
                    <?endif;*/?>
                </td>
            </tr>
            <tr>
                <td>Номер договора</td>
                <td><input type="text" class="input-text-main number-dogovor" value="<?=$n_dogovor?>"></td>
            </tr>
            <tr>
                <td>Ответственные лица</td>
                <td>
                    <select multiple class="slim-select2 users-responsible">
                        <?foreach ($users_array as $key => $user):?>
                            <option <?if ($responsible_array[$key]) echo "selected"?> value="<?=$key?>"><?=$user?> </option>
                        <?endforeach;?>
                    </select>
            </tr>
            <tr>
                <td>РП </td>
                <td><?=$users_array[$rp_id]?>  <input type="hidden" class="user-rp" value="<?=$rp_id;?>"></td>
            </tr>
            <tr>
                <td>Факт. дата начала работ </td>
                <?/*<td><?=$date_start?></td>*/?>
                <td><input type="date" class="start-work" value="<?=$date_start_for_table?>"> </td>
            </tr>
            <tr>
                <td>Факт. дата завершения работ </td>
                <td><input type="date" class="end-work" value="<?=$date_end_for_table?>"></td>
                <?/*<td><?=$date_end?></td>*/?>
            </tr>
        </table>

        <div class="button-footer" style="margin-bottom: 20px;">
            <input type="button" value="Изменить данные" data-id="<?=$EL_ID?>" class="table-head-change ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
        </div>

    <?} else {?>
        <table class="card-add" style="margin-bottom: 20px;">
            <tr>
                <td>Объект </td>
                <td><?=$DEAL_NAME?>
                </td>
            </tr>
            <tr>
                <td>Номер договора</td>
                <td><?=$n_dogovor?></td>
            </tr>
            <tr>
                <td>Ответственные лица</td>
                <td><?=implode(", ", $responsible_array)?> </td>
            </tr>
            <tr>
                <td>РП </td>
                <td><?=$users_array[$rp_id]?></td>
            </tr>
            <tr>
                <td>Факт. дата начала работ </td>
                <td><?=$date_start?></td>
            </tr>
            <tr>
                <td>Факт. дата завершения работ </td>
                <td><?=$date_end?></td>
            </tr>
        </table>
    <?}?>




    <div>
        <?if ($admin_roll || $rp_roll) {?>
            <input type="button" href="#modal-user"  style="margin-bottom: 20px; float: left;" class="add-work-time ui-btn ui-btn-sm ui-btn-light-border ui-btn-round" value="Добавить работу">
        <?}?>

        <?if ($responsible_roll && ($hour>=17 || $hour<12)) {?>
            <input type="button" href="#modal-user"  style="margin-bottom: 20px; float: left;" class="add-work-time ui-btn ui-btn-sm ui-btn-light-border ui-btn-round" value="Добавить работу">
        <?}?>
        <?if (count($serv_time_ar)>0) {?>
        <div class="report-excel" style="float: right;">
            <button name="report-excel" value="report-excel" data-id="<?=$EL_ID?>" data-deal="<?=$DEAL_ID?>" style="border: none; cursor: pointer;" class="download-excel-button js-report-excel"></button>
        </div>
        <?}?>
        <div style="clear: both;"> </div>
    </div>
    <?//}?>
    <div style="clear: both;"> </div>

    <?if ($admin_roll) {
        $readonly_model="";
        $readonly_model_for_respons="";
    }?>

    <?//блокируем возможность вноса в ячейки услуг для РП
    if ($rp_roll) {    $rp_model="readonly";}

    if ($responsible_roll) {
        $readonly_model="";
    }
    ?>


    <div <?if (count($serv_time_ar)==0) echo "style='display:none;'";?> class="table-placeholder"></div>
    <div <?if (count($serv_time_ar)==0) echo "style='display:none;'";?> class="table-scroll state-hidden">
        <button class="table-scroll__button state-active" data-type="next">
            <img src="data:image/svg+xml;charset=US-ASCII,%0A%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%2042%2081%22%3E%3Cpath%20fill%3D%22%23535c69%22%20opacity%3D%220.3%22%20d%3D%22M40.024%2C0H42a0%2C0%2C0%2C0%2C1%2C0%2C0V81a0%2C0%2C0%2C0%2C1%2C0%2C0H40.977A40.977%2C40.977%2C0%2C0%2C1%2C0%2C40.024v0A40.024%2C40.024%2C0%2C0%2C1%2C40.024%2C0Z%22/%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M20.2%2C31.91l8.014%2C8.576L20.2%2C49.061a0.762%2C0.762%2C0%2C0%2C0%2C0%2C1.026l1.563%2C1.672a0.647%2C0.647%2C0%2C0%2C0%2C.958%2C0l8.014-8.576h0L32.776%2C41a0.762%2C0.762%2C0%2C0%2C0%2C0-1.025L22.72%2C29.212a0.647%2C0.647%2C0%2C0%2C0-.958%2C0L20.2%2C30.885A0.762%2C0.762%2C0%2C0%2C0%2C20.2%2C31.91Z%22/%3E%3C/svg%3E%0A">
        </button>
        <div class="table-scroll_fixed">
            <button class="table-scroll__button" data-type="prev">
                <img src="data:image/svg+xml;charset=US-ASCII,%0A%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%2042%2081%22%3E%3Cpath%20fill%3D%22%23535c69%22%20opacity%3D%220.3%22%20d%3D%22M40.024%2C0H42a0%2C0%2C0%2C0%2C1%2C0%2C0V81a0%2C0%2C0%2C0%2C1%2C0%2C0H40.977A40.977%2C40.977%2C0%2C0%2C1%2C0%2C40.024v0A40.024%2C40.024%2C0%2C0%2C1%2C40.024%2C0Z%22/%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M20.2%2C31.91l8.014%2C8.576L20.2%2C49.061a0.762%2C0.762%2C0%2C0%2C0%2C0%2C1.026l1.563%2C1.672a0.647%2C0.647%2C0%2C0%2C0%2C.958%2C0l8.014-8.576h0L32.776%2C41a0.762%2C0.762%2C0%2C0%2C0%2C0-1.025L22.72%2C29.212a0.647%2C0.647%2C0%2C0%2C0-.958%2C0L20.2%2C30.885A0.762%2C0.762%2C0%2C0%2C0%2C20.2%2C31.91Z%22/%3E%3C/svg%3E%0A">
            </button>
            <table class="limited-item">
                <thead data-sticky>
                <tr>
                    <td rowspan="2">№ п/п</td>
                    <td rowspan="2">Наименование  работ </td>
                    <td rowspan="2">Ед. изм.</td>
                    <td rowspan="2">Общий объем</td>
                    <td rowspan="2">Выполненный <br/> объем</td>
                    <td rowspan="2">Остаток</td>
                </tr>
                </thead>
                <?$nn=1; foreach ($serv_time_ar as $key_name=> $name):

                    $sum_serv_main=array_sum($serv_sum_work_item[$key_name]);
                    $vol_all=$DATA_MAIN[$key_name]['vol-all'];
                    $ostatok=$vol_all-$sum_serv_main; ?>
                    <tr data-id="<?=$key_name?>" class="type-string-main">
                        <td><?=$nn?> </td>
                        <td>
                            <div class="block-sum-orr-name-serv">
                                <span class="togle-users"> + </span>
                                <?=$array_main_ref_header[$key_name]['UF_NAME']?>
                            </div>
                            <ul class="block-users-main">
                                <?foreach ($name as $key_user=>$time_user){?>
                                    <li><?=$users_mont[$key_user]?> </li>
                                <?}?>
                            </ul>
                        </td>
                        <td> <?=$array_main_ref_header[$key_name]['UF_TYPE']?> </td>  <?//еденицы измерения?>
                        <? // общий объем?>      <td><input class="input-number vol-all" min="0" <?=$respons_model?> <?=$readonly_model?> type="number" size="3" value="<? if ($vol_all) echo $vol_all; else echo "0"; ?>"></td>
                        <td><input class="input-number vol-all-use" min="0" readonly type="number" size="3" value="<? if ($sum_serv_main) echo $sum_serv_main; else echo "0"; ?>"></td>
                        <? // Остаток?>          <td class="<?if ($ostatok<0) echo "red-block-table"?>"><input class="input-number vol-balance" readonly min="0" type="number" size="3" value="<? if ($ostatok) echo $ostatok; else echo "0"; ?>"></td>
                    </tr>
                    <?$nn++;endforeach;?>
                </thead>
            </table>
        </div>
        <div class="table-scroll_scrolled">
            <table class="limited-item">
                <thead data-sticky>
                <tr>
                    <?foreach ($time_obj as $key => $item){
                        $month_and_year=explode("-",$key);
                        ?>
                        <td colspan="<?=count($time_obj[$key])?>"><?=$month_rus[$month_and_year[0]]?> <? if(count($Years)>1) echo $month_and_year[1]?> <div class="hide-month" data-id="<?=$key?>">Свернуть</div></td>
                        <td rowspan="2">Итог за <?=$month_rus[$month_and_year[0]]?>  </td>
                    <?}?>
                </tr>
                <tr>
                    <?foreach ($time_obj as $key => $item){?>
                        <?foreach ($item as $day){?>
                            <td class="td-head-show" data-id="<?=$key?>"><?=$day?></td>
                        <?}?>
                    <?}?>
                </tr>
                </thead>
                <?$nn=1; foreach ($serv_time_ar as $key_name=> $name):
                    //$data_row_array=(array)$name; $find_ds_ar=explode("_",$key_name);?>
                    <tr data-id="<?=$key_name?>" class="type-string">
                        <?foreach ($time_obj as $key => $item){
                            $key_array_time=explode("-", $key);
                            $sum_month=0;
                            ?>
                            <?foreach ($item as $day){
                                ?>

                                <td class="td-head-show" data-id="<?=$key?>">

                                    <?
                                    $date_day=date("d.m.Y", mktime(0,0,0,$key_array_time[0],$day,$key_array_time[1]));
                                    //считаем общую сумму за день
                                    $sum_day=0;
                                    foreach ($name as $key_user=>$time_user){
                                        $sum_day+=$serv_time_ar[$key_name][$key_user][$day."-".$key]['UF_WIDTH'];
                                    }
                                    //суммируем месяц
                                    $sum_month+=$sum_day;
                                    ?>

                                    <div data-date="<?=$date_day?>" data-serv="<?=$key_name?>" <?if ($sum_day>0 && ($admin_roll || $rp_roll)) echo "href='#modal-user-work'";?> class="block-sum-orr-name-serv <?if ($sum_day>0 && ($admin_roll || $rp_roll)) echo "show-work-time";?>"> <?if ($sum_day>0) echo $sum_day; //тут будет класс для возможности редактирования ответвенному?> </div>
                                    <ul class="block-users-days">
                                        <?foreach ($name as $key_user=>$time_user){
                                            $work_i=$serv_time_ar[$key_name][$key_user][$day."-".$key]['UF_WIDTH'];
                                            ?>
                                            <li><?if ($work_i) echo $work_i; elseif ($sum_day>0 && !$work_i) echo "0";?> </li>
                                        <?}?>
                                    </ul>
                                </td>
                            <?}?>
                            <td class="row-head-hide" data-id="<?=$key?>" colspan="<?=count($time_obj[$key])?>"> </td>
                            <td style="background: #7cb5ec;">
                                <input class="input-number item-month-all" readonly data-month="<?=$key_name.'-'.$key?>" min="0" type="number" size="3" value="<?=$sum_month?>">
                            </td>
                        <?}?>
                    </tr>
                    <?$nn++;endforeach;?>
                </thead>
            </table>
        </div>
    </div>

    <?if (count($serv_time_ar)>0) :?>
        <?//либо РП либо ответсвенный
        if ($rp_roll || $admin_roll) {?>
            <div class="button-footer">
                <input type="button" data-id="<?=$EL_ID?>" value="Сохранить" class="save-table ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
            </div>
        <?}?>
    <?endif;?>

    <?
    // ТУТ БУДЕМ ЧИТАТЬ ИЗ КУКИ
    $id_cookie=$_REQUEST['DEAL_ID'];
    if ($_REQUEST['DS']) {
        $id_cookie=$_REQUEST['DEAL_ID']."_".$_REQUEST['DS'];
    }
    //dump($_COOKIE['LIMITED_COLUM_'.$id_cookie]);
    $user_filter=explode(",", $_COOKIE['LIMITED_COLUM_'.$id_cookie]);?>
    <script>
        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        $(function(){
            $(document).ready(function(){
                <?foreach ($user_filter as $hide) {?>
                $('.hide-month[data-id="<?=$hide?>"]').click();
                <?}?>
            });

            $('.hide-month').on("click", function () {
                var id=$(this).data('id');
                var cook=getCookie('LIMITED_COLUM_<?=$id_cookie?>');
                if (cook) {
                    if (cook.indexOf( id ) != -1) {
                        //console.log('Такой айдишник у куки уже есть');
                    } else {
                        //console.log('Айдишник надо добавить в куку');
                        var ar_cook=cook.split(',');
                        ar_cook.push(id);
                        document.cookie = "LIMITED_COLUM_<?=$id_cookie?>="+ar_cook.toString();
                    }
                } else {
                    document.cookie = "LIMITED_COLUM_<?=$id_cookie?>="+id;
                }
            });

            $(document).on("click", ".show-month", function () {
                var id=$(this).data('id');
                var cook=getCookie('LIMITED_COLUM_<?=$id_cookie?>');
                if (cook) {
                    console.log('KUKA opredelena i nado udalit id');
                    var ar_cook=cook.split(',');
                    var index = ar_cook.indexOf(id);
                    if (index > -1) {
                        ar_cook.splice(index, 1);
                    }
                    document.cookie = "LIMITED_COLUM_<?=$id_cookie?>="+ar_cook.toString();
                }
            });

            $(".add-work-time").fancybox({
                margin: 0,
                padding: 20,
                maxWidth: 600,
                //width: 500,
                autoScale: true,
                transitionIn: 'none',
                transitionOut: 'none',
                type: 'inline',
                helpers: {
                    overlay: {
                        locked: false
                    }
                }
            });


            $(".show-work-time").fancybox({
                margin: 0,
                padding: 20,
                maxWidth: 600,
                //width: 500,
                autoScale: true,
                transitionIn: 'none',
                transitionOut: 'none',
                type: 'inline',
                helpers: {
                    overlay: {
                        locked: false
                    }
                }
            });

            //составляем список монтажников для js
            var arrmont = new Map([
                <?foreach ($users_mont as $key=>$fio):?>
                [<?=$key?>, "<?=$fio?>"],
                <?endforeach;?>
            ]);

            const user_select=new SlimSelect({
                select: ".slim-mont-user",
                showSearch: true,
                placeholder: 'Выберите монтажника',
                beforeOpen: function beforeOpen() {}
            });

            //выбор типа работы
            new SlimSelect({
               select: ".slim-type-work",
               showSearch: true,
               placeholder: 'Выберите из списка',
               beforeOpen: function beforeOpen() {}
           });

            //выбор РП
            new SlimSelect({
                select: ".slim-mont-rp-user",
                showSearch: true,
                placeholder: 'Выберите из списка',
                beforeOpen: function beforeOpen() {}
            });

            //поиск монтажников
            $(document).on("change", ".slim-mont-rp-user", function() {
                var id=$(this).val();
                var id_elem=$('.day-time').val();
                if (id) {
                    $.post(
                        '/local/tools/ajax/germet/get_mont_for_rp.php',
                        {id:id, id_elem: id_elem},
                    )
                        .done(function(data) {
                            console.log(data);
                            if (data=="error") {
                                user_select.setData([]);
                                $('.montag-us').hide();
                            } else {
                                var json_res=JSON.parse(data);
                                console.log(json_res);
                                user_select.setData(json_res);
                                $('.montag-us').show();
                            }
                        });
                }
            });
            var slimtype=$('#mont-type-time').html();
            var dogtype=$('#deals-dogovors').html();

            $(document).on("click", ".add-new-mont", function(e) {
                var val_us=$('.slim-mont-user').val();
                if (val_us==null) {
                    //$('.users-block-table').html("");
                } else {
                    if (val_us.length){
                        val_us.forEach(function(item, i, arr) {
                            if ($('.item-mont[data-id="'+item+'"]').length) {
                                //console.log('такой монтажник уже есть');
                            } else {
                                $('#modal-user .users-block-table').append("<div class='item-mont' data-id='"+item+"'><div class='mont-fio'> "+arrmont.get(Number(item))+"</div><div class='mont-dog'>"+dogtype+"</div><div class='mont-width'><input class='input-number-check' type='number'></div><div class='mont-work'>"+slimtype+"</div><div class='mont-close'>X</div> </div>");
                            }

                        });
                    }
                    $('.slim-mont-user .ss-value-delete').click();
                }
            });

            $(document).on("click", ".mont-close", function() {
                $(this).closest('.item-mont').remove();
            });

            //добавляем монтажников по дню
            $(document).on("click", ".show-work-time", function() {
                var date=$(this).data('date');
                var serv=$(this).data('serv');
                var deal=$('.deal-id').val();
                $('#modal-user-work .day-time-save').val(date);
                var data={
                    date: date,
                    serv: serv,
                    deal: deal
                };
                $.post(
                    '/local/tools/ajax/germet/show_work_time.php',
                    data,
                )
                    .done(function(data) {
                        $('#modal-user-work .users-block-table').html(data);
                    });
            });


            $(document).on("click", ".save-mont-all", function() {
                // тут будет аякс который будет собирать необходимые данные для сохранения монтажников и передавать в скрипт
                // так же подсчитывать количество монтажников и вставлять в ячейку
                var deal=$('.deal-id').val();
                var card=$('.card-id').val();

                var type_work=$('.slim-type-work').val();
                var id_elem=$('.day-time').val();
                //есть что сохранять
                var mont = {};
                if ($('#modal-user .item-mont').length){
                    $('#modal-user .item-mont').each(function(index, element){
                        var id_user=$(this).data('id');
                        var pos=$(this).find('.mont-type-time').val();
                        var work=$(this).find('.mont-width input').val();
                        var dog=$(this).find('.deals-dogs option:selected').text();
                        mont[id_user]={type: pos, width: work, dog: dog};
                    });
                    var data={
                        mont: mont,
                        deal: deal,
                        card: card,
                        id_elem: id_elem,
                        type_work: type_work
                    };

                    console.log(data);
                    $.post(
                        '/local/tools/ajax/germet/add_time_mont.php',
                        data,
                    )
                        .done(function(data) {

                            console.log(data);
                            if (data=="ok") {
                                //$('.item-month-user[data-id="'+id_elem+'"]').val(col);
                                $('.fancybox-close-small').click();
                                document.location.reload(true);
                            }
                        });

                } else {
                    //нечего сохранять
                }
            });

            //будем обновлять часы монтажников
            $(document).on("click", ".save-mont-day", function() {
                // тут будет аякс который будет собирать необходимые данные для сохранения монтажников и передавать в скрипт
                // так же подсчитывать количество монтажников и вставлять в ячейку
                var deal=$('.deal-id').val();
                var card=$('.card-id').val();
                var id_elem=$('.day-time-save').val();
                //есть что сохранять
                var mont = {};
                if ($('#modal-user-work .item-mont').length){
                    $('#modal-user-work .item-mont').each(function(index, element){
                        var id_user=$(this).data('id');
                        var pos=$(this).find('.mont-type-time').val();
                        var work=$(this).find('.mont-width input').val();
                        var dog=$(this).find('.deals-dogs option:selected').text();
                        mont[id_user]={type: pos, width: work, dog: dog};
                    });
                    var data={
                        mont: mont,
                        deal: deal,
                        card: card,
                        id_elem: id_elem,
                    };

                    console.log(data);
                    $.post(
                        '/local/tools/ajax/germet/update_time_mont.php',
                        data,
                    )
                        .done(function(data) {
                            if (data=="ok") {
                                $('.fancybox-close-small').click();
                                document.location.reload(true);
                            }
                        });

                } else {
                    //нечего сохранять
                }
            });



            //тут будем показывать и скрывать пользователей в виде работ
            $(document).on("click", ".togle-users", function() {
                var parent_tr=$(this).closest('tr');
                var id_serv=parent_tr.data('id');
                console.log(id_serv);
                if ($(this).hasClass('minus')) {
                    $(this).removeClass('minus');
                    $(this).text('+');
                    $('.type-string-main[data-id="'+id_serv+'"]').find('.block-users-main').hide();
                    $('.type-string[data-id="'+id_serv+'"]').find('.block-users-days').hide();
                } else {
                    $(this).addClass('minus');
                    $(this).text('–');
                    $('.type-string-main[data-id="'+id_serv+'"]').find('.block-users-main').show();
                    $('.type-string[data-id="'+id_serv+'"]').find('.block-users-days').show();
                }

            });

            $(".vol-all").on("change", function() {
                var parent_tr=$(this).closest('tr');
                var id_dir=parent_tr.data('id');
                var vol_all=$(this).val();
                parent_tr.find('.vol-balance').val(vol_all-Number($('.type-string-main[data-id="'+id_dir+'"]').find('.vol-all-use').val()));
            });

            //при изменении количества человек
            $(document).on('click','.save-table', function(){
                var id=$(this).data('id');
                var data_main=makeData();
                var deal_id=$('.deal-id-this').val();
                var start_date=$('.date-start-this').val();
                var end_date=$('.date-end-this').val();
                $.ajax({
                    method: "POST",
                    url: '/local/tools/ajax/save_data_carts.php',
                    data: {id: id, data: data_main, deal_id: deal_id, start_date: start_date, end_date:end_date},
                    success: function(res){
                        console.log(res);
                        window.scrollTo(0, 0);
                        document.location.reload(true);
                    }
                });
            });

            //функция сбора данных
            function makeData(){
                var data_vol={};
                $('.type-string').each(function(){
                    var id_dir=$(this).data('id');
                    data_vol[id_dir]={};
                    data_vol[id_dir]['vol-all']=Number($('.type-string-main[data-id="'+id_dir+'"]').find('.vol-all').val());
                });
                return data_vol;
            }

            $(document).on('click', ".hide-month", function(){
                var id=$(this).data('id');
                $(this).parent().attr("rowspan","2");
                $('.td-head-show[data-id="'+id+'"]').hide();
                $('.row-head-hide[data-id="'+id+'"]').show();
                $(this).removeClass('hide-month');
                $(this).addClass('show-month');
                $(this).text('Развернуть');
            });

            $(document).on('click', ".show-month", function(){
                var id=$(this).data('id');
                $(this).parent().removeAttr("rowspan");
                $('.td-head-show[data-id="'+id+'"]').show();
                $('.row-head-hide[data-id="'+id+'"]').hide();
                $(this).removeClass('show-month');
                $(this).addClass('hide-month');
                $(this).text('Свернуть');
            });

            <? //скрипты на сохранения полей таблицы для РП и Админа и ответвенного
            if ($admin_roll || $rp_roll){?>
            new SlimSelect({
                select: ".slim-select2",
                showSearch: true,
                placeholder: 'Выберите из списка',
                beforeOpen: function beforeOpen() {}
            });

            $(document).on('click', '.table-head-change', function(){
                var id=$(this).data('id');
                var deal_id=$('.deal-id').val();
                var start_date=$('.start-work').val();
                var end_date=$('.end-work').val();
                var users_responsible=$('.users-responsible').val();
                var user_rp=$('.user-rp').val();
                var dogovor=$('.number-dogovor').val();
                if (id && start_date && end_date && user_rp && users_responsible) {
                    console.log('Таблицу можно обновлять');
                    $.ajax({
                        method: "POST",
                        url: '/local/tools/ajax/card_update.php',
                        data: {id: id, deal_id: deal_id, users_responsible: users_responsible, user_rp:user_rp, start_date: start_date, end_date:end_date, dogovor: dogovor},
                        success: function(res){
                            console.log("Данные успешно обновленны!");
                            document.location.reload(true);
                        }
                    });
                } else {
                    console.log('Не все поля заполнены');
                }
            });

            /*$(document).on('click', '.input-number', function(){
                //console.log($(this).val());
                //console.log($(this).attr('readonly'));
                if ($(this).val()==0 && $(this).attr('readonly')!='readonly') {
                    $(this).val('');
                }
            });*/

            <?}?>


            $(document).on("click", ".js-report-excel", function(e) {
                e.preventDefault();
                var id=$(this).data('id');
                var deal=$('.deal-id').val();
                var data={
                    id: id,
                    deal: deal
                };
                console.log(data);
                $.post(
                    '/local/tools/ajax/germet/exel_make_table.php',
                    data,
                )
                    .done(function(data) {
                        console.log(data);
                        if(data != "") {
                            location.href = data;
                        } else {
                            alert("Не удалось скачать файл. Попробуйте позже.");
                        }
                    });
                return false;
            });
        });
    </script>

    <div id="mont-type-time" style="display: none;">
        <select class="slim-mont-type-time mont-type-time">
            <?foreach ($arr_type_work as $id=>$name):?>
                <option value="<?=$id?>"><?=$name?></option>
            <?endforeach;?>
        </select>
    </div>


    <div id="deals-dogovors" style="display: none;">
        <select class="deals-dogs">
            <?foreach ($deals_dogs as $id=>$name):?>
                <option value="<?=$id?>"><?=$name?></option>
            <?endforeach;?>
        </select>
    </div>

    <div id="modal-user" style="display: none;">
        <div style="width: 600px; min-height: 500px;">
            <input type="text" class="day-time" readonly value="<?=$date_for_save?>">
            <div class="use-work" style="margin-bottom: 20px;">
                Выберите работу: <select style="width: 50%;" class="slim-type-work">
                    <?foreach ($array_main_ref_header as $key=> $item):?>
                        <option value="<?=$key?>"><?=$item['UF_NAME']?> </option>
                    <?endforeach;?>
                </select>
            </div>

            <div class="montag-rp" style="margin-bottom: 20px;">
                Выберите РП монтажников: <select style="width: 50%;" class="slim-mont-rp-user chose-mont-rp-user">
                    <option value="0">Не выбрано</option>
                    <?foreach ($users_array_rp as $key=> $name):?>
                        <option value="<?=$key?>"><?=$name?></option>
                    <?endforeach;?>
                </select>
            </div>

            <div class="montag-us" style="display: none;">
                <select style="width: 50%;" class="slim-mont-user chose-mont-user" multiple>
                    <?/*<option value="0">Не выбрано</option>*/?>
                    <?foreach ($users_mont as $key=> $name):?>
                        <option value="<?=$key?>"><?=$name?></option>
                    <?endforeach;?>
                </select>
                <input type="hidden" class="mont_elem_block">
                <input type="button" style="width: 45%;" class="add-new-mont ui-btn ui-btn-sm ui-btn-light-border ui-btn-round" value="Добавить монтажников">
            </div>

            <div class="users-block-table">

            </div>

            <div class="button-footer">
                <input type="button" data-id="1" value="Сохранить" class="save-mont-all ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
            </div>
        </div>
    </div>


    <div id="modal-user-work" style="display: none;">
        <div style="width: 650px; min-height: 500px;">
            <input type="text" class="day-time-save" readonly value="">
            <div class="users-block-table">
                сюда будем грузить данные по монтажникам
            </div>

            <div class="button-footer">
                <input type="button" data-id="1" value="Обновить" class="save-mont-day ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
            </div>

        </div>
    </div>
<?} else { echo "Раздел не доступен. Не указан ID объекта";}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>