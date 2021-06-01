<?
require_once($_SERVER["DOCUMENT_ROOT"] . '/local/classes/MyHelper.php');

function dump($d, $v = true)
{
    echo '<pre' . (!$v ? ' style="display:none;"' : '') . '>';
    print_r($d);
    echo '</pre>';
}

if (!function_exists('array_key_first')) {
    function array_key_first(array $array)
    {
        foreach ($array as $key => $value) {
            return $key;
        }
    }
}

if (!function_exists('array_key_last')) {
    function array_key_last(array $array)
    {
        end($array);
        return key($array);
    }
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


//Переименовать сделки в объекты
AddEventHandler("main", "OnEndBufferContent", "OnEndBufferContentHandler");

function OnEndBufferContentHandler(&$content)
{
//    dump($content);
    global $APPLICATION;
    if ((CSite::inDir(SITE_DIR . 'crm/deal/') || $APPLICATION->GetProperty('CRM_DEAL_PAGE_PAGE') == 'Y')) {
        $content = str_replace(
            ['Сделки', 'сделок', 'сделки', 'Сделка', 'сделке', 'сделку', 'сделка'],
            ['Объекты', 'Объектов', 'объекты', 'Объект', 'объекту', 'объект', 'объект'],
            $content
        );
    }
    if ((CSite::inDir(SITE_DIR . 'crm/'))) {
        $content = str_replace(
        //['Сделки', 'сделок', 'сделки', 'Сделка', 'сделке', 'сделку', 'сделка', 'Роботы и бизнес-процессы'],
        //['Заявки', 'заявок', 'заявки', 'Заявка', 'заявке', 'заявку', 'заявка', 'Уведомления и автоматизация'],
            ['Сделки', 'сделок', 'сделки', 'Сделка', 'сделке', 'сделку', 'сделка'],
            ['Объекты', 'Объектов', 'объекты', 'Объект', 'объекту', 'объект', 'объект'],
            $content
        );
    }
}

function transliterate($st)
{
    $st = strtr($st,
        "абвгдежзийклмнопрстуфыэАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ",
        "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE"
    );
    $st = strtr($st, array(
        'ё' => "yo", 'х' => "h", 'ц' => "ts", 'ч' => "ch", 'ш' => "sh",
        'щ' => "shch", 'ъ' => '', 'ь' => '', 'ю' => "yu", 'я' => "ya",
        'Ё' => "Yo", 'Х' => "H", 'Ц' => "Ts", 'Ч' => "Ch", 'Ш' => "Sh",
        'Щ' => "Shch", 'Ъ' => '', 'Ь' => '', 'Ю' => "Yu", 'Я' => "Ya",
    ));
    return $st;
}


if (!class_exists('CCRMBPViewClass')) {
    class CCRMBPViewClass
    {
        function CRMBPViewStartFunction()
        {
            include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/salt.crmbpview/include/crm_ent_inc.php");
        }
    }
}

?>