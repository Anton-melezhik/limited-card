<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page;
Page\Asset::getInstance()->addJs('/dist/libs/slimselect.min.js');
Page\Asset::getInstance()->addCss('/dist/libs/slimselect.min.css');
Page\Asset::getInstance()->addCss('/dist/css/refbook.css');
/*if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/.default/components/bitrix/main.ui.grid/.default/style.css')) {
    Page\Asset::getInstance()->addCss('/local/templates/.default/components/bitrix/main.ui.grid/.default/style.css');
} else {
    Page\Asset::getInstance()->addCss('/bitrix/components/bitrix/main.ui.grid/templates/.default/style.css');
}*/
//Page\Asset::getInstance()->addCss($templateFolder . '/tingle.min.css');
//Page\Asset::getInstance()->addJs($templateFolder . '/tingle.min.js');
CJSCore::Init(array('jquery2',"date"));
?>
<div style="min-height: 700px;">

    <p>Выберите бюджет:</p>
    <select  class="slim-select BUDZHET">
        <option value="0">Не выбрано</option>
        <?foreach ($arResult['BUDZHET'] as  $arr) {?>
            <option value="<?=$arr['ID']?>"><?=$arr['NAME']?></option>
        <?}?>
    </select>



    <div class="block-tovars">


    </div>

    <p>Из бюджета:</p>
    <input type="radio" id="contactChoice1"
             name="from_b" value="Y">
    <label for="contactChoice1">Да</label>

    <input type="radio" id="contactChoice2"
           name="from_b" value="N">
    <label for="contactChoice2">Нет</label>

    <div class="tovars-no-bud" style="display: none;">


        <p>Выберите раздел каталога:</p>
        <select class="search-elements">
            <option value="0">Не выбрано</option>
            <?foreach ($arResult['SECTIONS'] as $k=>$i):?>
                <option value="<?=$k?>"><?=$i?></option>
            <?endforeach;?>
        </select>
        <br><br>
        <select multiple class="catalog-elements">
        </select>
        <div class="button-footer" style="margin-bottom: 20px;">
            <input type="button" style="display: none;" value="Добавить товар" class="add-tovar ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
        </div>

        <table class="elements-table" data-id="<?=$_REQUEST['ID']?>">
            <thead>
            <tr>
                <td>Название </td>
                <td>Цена </td>
                <td>Количество </td>
                <td>Сумма</td>
            </tr>
            </thead>
            <tbody>
            <tfoot>
            <tr>
                <td colspan="3"> Итого </td>
                <td class="sum-all-budzhet"><?=number_format($sum_all, 2, '.', '')?> </td>
            </tr>
            </tfoot>
        </table>
    </div>


    <div class="button-footer">
        <div class="block-error"> </div>
        <div class="block-good"> </div>
        <input type="submit" value="Создать заявку" class="request-create ui-btn ui-btn-sm ui-btn-light-border ui-btn-round">
    </div>
</div>
