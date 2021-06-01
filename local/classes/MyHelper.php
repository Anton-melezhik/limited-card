<?php
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;
/**
 * Class Helper
 */
class MyHelper
{
    const DEFAULT_DATE_FORMAT = 'd F Y';
    /**
     * @param $name
     * @param null $hlData
     * @return \Bitrix\Main\ORM\Data\DataManager
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHlClassByName($name, &$hlData=null)
    {
        $hlData = self::getHlDataByName($name);
        return HighloadBlockTable::compileEntity($hlData)->getDataClass();
    }

    public static function getUserGroupByCode($code)
    {
        $groups=\CGroup::GetList(
            ($by = "c_sort"),
            ($order = "desc"),
            array("STRING_ID" => $code)
        );
        if ($group = $groups->fetch()) {
            return $group['ID'];
        }
        else {
            return '';
        }

    }


    /**
     * @param $iblockCode
     * @return bool
     * Возвращает ID инфоблока по символьному идентификатору
     */
    function getIblockId($iblockCode) {
        $res = CIBlock::GetList([], ['code' => $iblockCode]);
        if ($ar_res = $res->Fetch()) {
            return $ar_res['ID'];
        } else {
            return false;
        }
    }

    public static function getIBIdByCode($code, $type=null,$siteId=null)
    {
        self::includeModules('iblock');
        $filter = [
            'CODE' => $code,
        ];
        if (!empty($type)) {
            $filter['IBLOCK_TYPE_ID'] = $type;
        }
        if (!empty($type)) {
            $filter['LID'] = $siteId;
        }
        $ibData = IblockTable::getList([
            'filter' => $filter,
            'select' => [
                'ID',
            ],
        ])->fetch();
        if (empty($ibData)) {
            throw new Exception(sprintf('Информационный блок с кодом "%s" не найден', $code));
        }
        return $ibData['ID'];
    }

    public static function getHlDataByName($name)
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new Exception('Module highLoadBlock not installed');
        }
        $hlBLock = HighloadBlockTable::getList([
            'filter' => [
                'NAME' => $name,
            ],
        ])->fetch();
        if ($hlBLock) {
            return $hlBLock;
        }
        throw new Exception(sprintf('HighLoadBlock %s not found', $name));
    }

    public static function formatSalary($salary)
    {
        if (is_numeric($salary)) {
            return number_format($salary, 0, '.', ' ');
        }
        return 0;
    }

    public static function plural($num, $s1, $s2, $s5)
    {
        return sprintf('%d %s', $num, (new Declension($s1, $s2, $s5))->get($num));
    }

    public static function pluralYears($num)
    {
        return self::plural($num, 'год', 'года', 'лет');
    }

    public static function pluralDays($num)
    {
        return self::plural($num, 'день', 'дня', 'дней');
    }

    public static function pluralMonths($num)
    {
        return self::plural($num, 'месяц', 'месяца', 'месяцев');
    }

    public static function makeExcelReport($report_type, $spreadsheet)
    {
        require_once $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
        $filename = $report_type . date("d-m-Y_h:i:s") . '.xlsx'; //в файл
        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/upload/reports/' . $report_type .'/'. $filename;
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);
        while (@ob_end_clean()) ;
        $arFile = CFile::MakeFileArray($filepath);
        return $arFile;
    }

    public function curlCalendar()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.calend.ru/work/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla 4.0");
        $result = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/<td class="(day[^"]*)" day="([0-9]+)" month="([0-9]+)">/', $result, $matches);

        $year = date('Y');

        if (count($matches[3]) > 0) {

            $calend = [];

            foreach($matches[3] as $i => $month) {

                if (!isset($calend[$year])){
                    $calend[$year] = [];
                }

                if (!isset($calend[$year][$month])) {
                    $calend[$year][$month] = [];
                }

                $calend[$year][$month][$matches[2][$i]] = ($matches[1][$i] == 'day col5'? true : false);

                if($month == 12 && $matches[2][$i] == 31){
                    $year++;
                }

            }

        }
        return $calend;
    }

    /**
     * сохраняет производственный календарь в базу данных
     *
     * @return boolean
     */
    public function saveProductionCalendar($year = '')
    {

        $strDatesProdCalendar = '';
        $year = strlen($year) ? $year : date('Y');
        $calend = self::curlCalendar();

        if(!is_array($calend) || empty($calend)){
            return false;
        }

        $optionCalendar = COption::GetOptionString('ART', 'holidays_weekends', '');
        $optionCalendar = strlen($optionCalendar) ? json_decode($optionCalendar, true) : [];
        $optionCalendar[$year] = [];

        foreach($calend[$year] as $month => $days){

            foreach($days as $day => $isWeekend){

                if($isWeekend){

                    if(!$optionCalendar[$year][$month]){
                        $optionCalendar[$year][$month] = [];
                    }

                    $optionCalendar[$year][$month][] = $day;

                }

            }

        }

        COption::SetOptionString('ART', 'holidays_weekends', json_encode($optionCalendar));
    }
}