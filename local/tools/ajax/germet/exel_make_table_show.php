<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('crm');
global $USER;
$USER_ID=$USER->GetID();
if ($_REQUEST['id']>0) {
    $DEAL_ID=$_REQUEST['deal'];
    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');

    $filter_first = array("ID" => $_REQUEST['id']);

    $rsData = $entity_data_class::getList(array(
        'order' => array('ID' => 'ASC'),
        'select' => array('*'),
        'filter' => $filter_first
    ));
    if ($el = $rsData->fetch()) {
        //Номер элемента в справочнике HB
        $EL_ID = $el['ID'];
        $date_start_for_table = $el['UF_DATE_START']->format('Y-m-d');
        $date_end_for_table = $el['UF_DATE_END']->format('Y-m-d');

        $date_start = $el['UF_DATE_START']->format('d.m.Y');
        $date_end = $el['UF_DATE_END']->format('d.m.Y');

        $rp_id = $el['UF_RP'];
        $n_dogovor = $el['UF_DOGOVOR'];
        $responsible_array = (array)json_decode($el['UF_RESPONSIBLE']);

        $DATA_MAIN=json_decode($el['UF_DATA'],true);


        $end = new DateTime($date_end_for_table);
        $end = $end->modify('+1 day');
        $period = new DatePeriod(
            new DateTime($date_start_for_table),
            new DateInterval('P1D'),
            $end
        );


        $month_rus = [
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
        $time_obj = [];
        $month = [];
        $Years = [];
        foreach ($period as $key => $value) {
            $time_obj[date_format($value, 'n') . "-" . date_format($value, 'Y')][] = date_format($value, 'j');
        }

        foreach ($time_obj as $key => $item) {
            $key_array = explode('-', $key);
            $month[] = $key_array[0];
            $Years[] = $key_array[1];
        }

        $Years = array_unique($Years);


        //Получаем данные из справочника главного ID=5
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

    }
}
    $result='<table class="limited-item" border="1">
        <thead>
        <tr>
            <td rowspan="2">№ п/п</td>
            <td rowspan="2">Наименование  работ </td>
            <td rowspan="2">Ед. изм.</td>
            <td rowspan="2">Общий объем</td>
            <td rowspan="2">Выполненный объем</td>
            <td rowspan="2">Остаток</td>';
            foreach ($time_obj as $key => $item){
                $month_and_year=explode("-",$key);

                $result.='<td colspan="'.count($time_obj[$key]).'">'.$month_rus[$month_and_year[0]].' ';
                if(count($Years)>1) $result.=$month_and_year[1];
                $result.='</td>';
                $result.='<td rowspan="2">Итог за '.$month_rus[$month_and_year[0]].'</td>';
            }
        $result.='</tr>';
        $result.='<tr>';
            foreach ($time_obj as $key => $item){
                foreach ($item as $day){
                    $result.='<td class="td-head-show" data-id="'.$key.'">'.$day.'</td>';
                }
            }
        $result.='</tr>';
        $nn=1;
        foreach ($serv_time_ar as $key_name=> $name):
            $sum_serv_main=array_sum($serv_sum_work_item[$key_name]);
            $vol_all=$DATA_MAIN[$key_name]['vol-all'];
            $ostatok=$vol_all-$sum_serv_main;

            $result.='<tr data-id="'.$key_name.'" class="type-string">';
            $result.='<td>'.$nn.'</td>';
            $result.='<td>'.$array_main_ref_header[$key_name]['UF_NAME'].'</td>';
            $result.='<td>'.$array_main_ref_header[$key_name]['UF_TYPE'].'</td>';
                $red1='';
                $red2='';
                if ($ostatok<0) $red1='style="color:red; background: #FFA1DA;"';
                $result.='<td>'.number_format($vol_all, 2, ',', '').'</td>';
                $result.='<td>'.number_format($sum_serv_main, 2, ',', '').'</td>';
                $result.='<td '.$red1.'>'.number_format($ostatok, 2, ',', '').'</td>';
                foreach ($time_obj as $key => $item){
                    $sum_month=0;
                    foreach ($item as $day){
                        $sum_day=0;
                        foreach ($name as $key_user=>$time_user){
                            $sum_day+=$serv_time_ar[$key_name][$key_user][$day."-".$key]['UF_WIDTH'];
                        }
                        $sum_month+=$sum_day;
                        if ($sum_day>0) $res_num=number_format($sum_day, 2, ',', '');
                        else $res_num='';
                        $result.='<td>'.$res_num.'</td>';
                        //$result.='<td>'.$data_row_array[$key_name.'-'.$key.'-'.$day].'</td>';
                    }
                    $result.='<td style="background: #7cb5ec;">'.number_format($sum_month, 2, ',', '').' </td>';
                }
            $result.='</tr>';
            $nn++;
            endforeach;
        $result.='</thead>
    </table>';


        echo $result;
/*$path = $_SERVER['DOCUMENT_ROOT'] . '/upload/reports/'.$EL_ID.'_table_*.xls';
foreach (glob($path) as $file) {
    @unlink($file);
}
$file = $_SERVER['DOCUMENT_ROOT'] . '/upload/reports/'.$EL_ID.'_table_'.date("d-m-Y_h:i:s").'.xls';
$fp = fopen($file, "w+");
fwrite($fp, generateExcelTemplate($result, $table_sum));
fclose($fp);
while(@ob_end_clean());
if (file_exists($file)) {
    // вернуть путь без document_root
    echo str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
} else {
    echo "";
}

function generateExcelTemplate($table){
    ob_start(); ?>
    <!DOCTYPE>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <meta name="report" content="">
    </head>
    <body>
    <?=$table?>
    </body>
    </html>
    <?
    return ob_get_clean();
}

*/


