<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
use Bitrix\Main\Page;
Page\Asset::getInstance()->addJs('/dist/libs/slimselect.min.js');
Page\Asset::getInstance()->addCss('/dist/libs/slimselect.min.css');
Page\Asset::getInstance()->addCss('/dist/css/refbook.css');
CJSCore::Init(array('jquery2'));
$APPLICATION->SetTitle('Создание таблицы');
CModule::IncludeModule('crm');
global $USER;
$USER_ID=$USER->GetID();
if ($_REQUEST['DEAL_ID']) {
    //тут нужна проверка на дурака, что бы 2 раза не мог пользователь создать карту для объекта
    $entity_data_class = MyHelper::getHlClassByName('LimitedCardItem');
    $filter_first=array('UF_DEAL'=>$_REQUEST['DEAL_ID']);
    $rsData = $entity_data_class::getList(array(
        'order' => array('ID'=>'ASC'),
        'select' => array('*'),
        'filter' => $filter_first
    ));

    if($el = $rsData->fetch()){
        echo "Сводная таблица для данной сделки создана!";
    } else {

        $res = Bitrix\Main\UserTable::getList([
            'select' => ["ID", 'XML_ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'ACTIVE', 'WORK_POSITION', "UF_DEPARTMENT"],
            'order' => ["ID" => "asc"],
            'filter' => ["!UF_DEPARTMENT" => false],
        ]);

        $users_array=[];

        while ($arUser = $res->fetch()) {
            //if ($arUser['ID']!=$USER_ID) {
                $users_array[$arUser['ID']]=$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'];
            //}
        }

        $dbRes = CCrmDeal::GetList(
            array("NAME", "ID"),
            array(
                "ID" => $_REQUEST['DEAL_ID'],
            ),
            array()
        );
        if ($arRes = $dbRes->Fetch()) {
            $DEAL_ID = $arRes['ID'];
            $DEAL_NAME = $arRes['TITLE'];
            ?>
            <table class="card-add">
                <tr>
                    <td>Объект</td>
                    <td><?= $DEAL_NAME ?> <input type="hidden" class="deal-id" value="<?= $DEAL_ID ?>"></td>
                </tr>
                <tr>
                    <td>Дата начала работ (факт)</td>
                    <td><input type="date" class="start-work"></td>
                </tr>
                <tr>
                    <td>Дата завершения работ (факт)</td>
                    <td><input type="date" class="end-work"></td>
                </tr>
                <tr>
                    <td>Ответственные лица за заполнение таблицы</td>
                    <td>
                        <select multiple class="slim-select users-responsible">
                            <?
                            foreach ($users_array as $key => $user): ?>
                                <option value="<?= $key ?>"><?= $user ?> </option>
                            <?endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>РП</td>
                    <td><?= $USER->GetFullName(); ?> <input type="hidden" class="user-rp"
                                                            value="<?= $USER->GetId(); ?>"></td>
                </tr>
            </table>

            <div class="button-footer">
                <div class="block-error"> </div>

                <input type="button" value="Создать"
                       class="card-create ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
            </div>

            <?php
        }
    }

} else {
    die("ID объекта не указан!!!");
}

?>

<script>
    $(function(){
        new SlimSelect({
            select: ".slim-select",
            showSearch: true,
            placeholder: 'Начните вводить имя или фамилию',
            beforeOpen: function beforeOpen() {}
        });

        $(document).on('click', '.card-create', function(){
            var deal_id=$('.deal-id').val();
            var start_date=$('.start-work').val();
            var end_date=$('.end-work').val();
            var users_responsible=$('.users-responsible').val();
            var user_rp=$('.user-rp').val();

            if (!start_date) {
                $('.start-work').css("border-color", "red");
            } else {
                $('.start-work').css("border-color", "#c6cdd3");
            }

            if (!end_date) {
                $('.end-work').css("border-color", "red");
            } else {
                $('.end-work').css("border-color", "#c6cdd3");
            }

            if (!users_responsible) {
                $('.users-responsible .ss-multi-selected').css("border-color", "red");
            } else {
                $('.users-responsible .ss-multi-selected').css("border-color", "#dedede");
            }

            if (start_date && end_date && user_rp && users_responsible) {
                console.log('Карту можно создавать');
                //возможно тут добавит новый скрипт для герметизации отдельно, пока в общем hb хранить будем
                $.ajax({
                    url: '/local/tools/ajax/germet/card_create.php',
                    data: {deal_id: deal_id,users_responsible: users_responsible, user_rp:user_rp, start_date: start_date, end_date:end_date},
                    success: function(res){
                        var result=JSON.parse(res);
                        if (result.error) {
                            $('.block-error').text(result.error);
                        } else {
                            window.location.href="/germet/item.php?DEAL_ID="+result.deal_id;
                        }
                    }
                });
            } else {
                console.log('Не все поля заполнены');
            }
        });
    });
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
