<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Application;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Crm\Service;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);

class RequestAdd extends CBitrixComponent implements Controllerable
{
    private function prepComponent()
    {
        Loader::includeModule('crm');
        Loader::includeModule('iblock');
        Loader::includeModule('bizproc');
        //Loader::includeModule('tasks');
        //Loader::includeModule('socialnetwork');
    }

    private function getDeal($id)
    {
        $arFilter=[
            "ID" => $id,
            "CHECK_PERMISSIONS" => 'N'];
        $rsDeal = CCrmDeal::GetList(
            array('ID' => 'ASC'),
            $arFilter,
            array("ID", "TITLE"),
            false
        );
        if ($arDeal = $rsDeal->GetNext()) {
            return $arDeal;
        }
    }

    public function getBudzhets(){
        $IBLOCK_ID=ArtPobedaHelper::getIblockId("budzhet_v");
        $b_arr=[];
        $filter=[
            "IBLOCK_ID"=>$IBLOCK_ID,
            "ACTIVE"=> 'Y',
            "PROPERTY_STATUS_VALUE" => "Активный"
        ];
        $rsElem = \CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            $filter,
            false,
            false,
            ["ID", "PROPERTY_DEAL", 'NAME', 'CREATED_BY', 'MODIFIED_BY', 'TIMESTAMP_X_UNIX', 'DATE_CREATE_UNIX']
        );
        while ($arElem = $rsElem->Fetch()) {
            $b_arr[]=$arElem;
        }

        return $b_arr;
    }

    public function requestCreateAction($post)
    {
        $USER_ID_CREATOR=154;
        $container=Service\Container::getInstance();
        $router=$container->getRouter();
        $factory=$container->getFactory(171);
        $entitiTypeId=\CCrmOwnerType::ResolveName(171);
        $item=$factory->createItem([]);
        $rsElem = \CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            ['ID' => $post['bud']],
            false,
            false,
            ['NAME', 'CREATED_BY', "IBLOCK_ID", 'MODIFIED_BY', 'TIMESTAMP_X_UNIX', 'DATE_CREATE_UNIX']
        );
        if ($arElem = $rsElem->Fetch()) {
            $ib=$arElem['IBLOCK_ID'];
            $name=$arElem['NAME'];
        }
        $res = CIBlockElement::GetProperty($ib, $post['bud'], "sort", "asc", array("CODE" => "DEAL"));
        if ($ob = $res->GetNext())
        {
            $deal = $ob['VALUE'];
        }

        $fields=[
            "TITLE"=> 'Заявка к бюджету '.$name,
            //"CATEGORY_ID" => 1,
            "UF_BUDGET" => $post['bud'],
            "UF_CRM_3_1631603373" => $deal,

        ];
        if ($post['chek']=='Y') {
            $fields['UF_CRM_3_1631004484']=true;
        } else {
            $fields['UF_CRM_3_1631004484']=false;
        }

        if ($post['products']) {
            $prods=$post['products'];
            $prods_new=[];
            $opportunity=0;
            foreach ($prods as $key => $itemp) {
                if ($itemp['QUANTITY']>0) {
                    $prods_new[]=$itemp;
                    $opportunity+=$itemp['PRICE']*$itemp['QUANTITY'];
                }
            }

            $fields['OPPORTUNITY']=$opportunity;
            $prod_add=$item->setProductRowsFromArrays($prods_new);
        }

        $item->setFromCompatibleData($fields);
        $result = $item->save();

        $id_req=$result->GetId();
        $bp_id=363; //id bp
        $arErrorsTmp=[];
        $res_bp=CBPDocument::StartWorkflow(
            $bp_id,
            array("crm","Bitrix\Crm\Integration\BizProc\Document\Dynamic", $entitiTypeId.'_'.$id_req),
            array(),
            $arErrorsTmp
        );

        return $id_req;
    }

    public function getIblockElement($id){
        if (!intval($id)) {
            $this->arResult['ERROR'] = 'Неверный ID бюджета';
            return;
        }
        //Получаем элемент инфоблока
        $rsElem = \CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            ['ID' => $id],
            false,
            false,
            ['NAME', 'CREATED_BY', "IBLOCK_ID", 'MODIFIED_BY', 'TIMESTAMP_X_UNIX', 'DATE_CREATE_UNIX']
        );
        if ($arElem = $rsElem->Fetch()) {
            $this->arResult['ELEM']=$arElem;
            $IBLOCK_ID=$arElem['IBLOCK_ID'];
            $this->arResult['IBLOCK_ID']=$IBLOCK_ID;
        }

        $db_props = CIBlockElement::GetProperty($IBLOCK_ID, $id, "sort", "asc", []);
        while($ar_props = $db_props->Fetch()){
            if ($ar_props['CODE']!="DATA") {
                $this->arResult['PROPS'][$ar_props['CODE']]['NAME']=$ar_props['NAME'];
                $this->arResult['PROPS'][$ar_props['CODE']]['CODE']=$ar_props['CODE'];
                $this->arResult['PROPS'][$ar_props['CODE']]['VALUE'][]=$ar_props['VALUE'];
            } else {
                $data_res=json_decode(htmlspecialchars_decode($ar_props['VALUE']), true);
                $cat_arr=[];
                $new_data_res=[];
                foreach ($data_res as $k_c=> $item_c) {
                    $new_data_res[$item_c['CAT_ID']][$k_c]=$item_c;
                    $cat_arr[$item_c['CAT_ID']]=$item_c['CAT_NAME'];
                }
                //$this->arResult['DATA']= json_decode($ar_props['VALUE'], true);
                $this->arResult['DATA']= $new_data_res;
                $this->arResult['DATA_CAT']= $cat_arr;
                if ( $this->arResult['DATA']) {
                    $sum_all=0;
                    foreach ($this->arResult['DATA'] as $cat_k => $items) {
                        foreach ($items as $el => $item) {
                            $sum_item=$item['PRICE']*$item['NUMBER'];
                            $sum_all+=$sum_item;
                        }
                        $this->arResult['SUM_ALL']=$sum_all;
                    }
                }
            }
            if ($ar_props['CODE']=="DEAL") {
                $this->arResult['DEAL'] = $this->getDeal($ar_props['VALUE']);
            }
        }
    }


    public function addTovarsAction($post)
    {
        //тут будет создаватся заявка, в которой и пойдет редирект на нее же
        $this->getIblockElement($post['bud']);
        $this->arResult['ELEMENTS'] = $this->GetElementsCat();
        $str='<h2>Товары бюджета: </h2><table class="elements-table">
                <thead>
                    <tr>   
                        <td>Название </td>
                        <td>Цена </td>
                        <td>Количество в бюджете </td>
                        <td>Количество для заявки</td>
                    </tr>
                </thead>
                <tbody>';
        $sum_all=0;
        foreach ($this->arResult['DATA'] as $el_c => $item_ar) {
            foreach ($item_ar as $el=> $item) {
                $sum_item=$item['PRICE']*$item['NUMBER'];
                $sum_all+=$sum_item;
                $str.='<tr class="catalog-row" data-id="'.$item['ID'].'">
                    <td>'.$this->arResult['ELEMENTS'][$item['ID']].'</td>
                    <td><input disabled class="price" type="number" min="0" size="3" value="'.$item['PRICE'].'"></td>
                    <td><input class="num-catalog-item" disabled type="number" min="0" size="3" value="'.$item['NUMBER'].'"></td>
                    <td>
                        <input class="num-catalog-item-comp" type="number" min="0" size="3" value="">
                    </td>
                </tr>';
            }}
        $str.='</tbody>
</table>';
        return $str;
    }


    public function GetElementsCat()
    {
        $IBLOCK_ID=ArtPobedaHelper::getIblockId("catalog");
        if ($this->arResult['DATA']){
            $elements=[];
            foreach ($this->arResult['DATA'] as $cat_k => $items) {
                $ar_el=array_keys($items);
                $res = \Bitrix\Iblock\ElementTable::getList([
                    'select' => ["ID", 'NAME'],
                    'order' => ["ID" => "asc"],
                    'filter' => ["IBLOCK_ID" => $IBLOCK_ID, "ID"=> $ar_el],
                ]);

                while ($el= $res->fetch()) {
                    $elements[$el['ID']]=$el['NAME'];
                }
            }
        }
        return $elements;
    }

    public function getSectionCatalog(){
        $IBLOCK_ID_CAT=ArtPobedaHelper::getIblockId("catalog");
        $res = \Bitrix\Iblock\SectionTable::getList([
            'select' => ["ID", 'NAME'],
            'order' => ["ID" => "asc"],
            'filter' => ["IBLOCK_ID" => $IBLOCK_ID_CAT, "DEPTH_LEVEL"=>1],
        ]);
        $sections=[];
        while ($sec = $res->fetch()) {
            $sections[$sec['ID']]=$sec['NAME'];
        }
        return $sections;
    }

    public function executeComponent()
    {
        $this->prepComponent();
        $this->arResult['SECTIONS'] = $this->getSectionCatalog();
        $this->arResult['BUDZHET'] = $this->getBudzhets();
        //$this->arResult['USERS'] = $this->getAllWorksUsers();
        $this->includeComponentTemplate();
    }

    public function configureActions(): array
    {
        $this->prepComponent();
        // TODO: Implement configureActions() method.
        return [];
    }
}
