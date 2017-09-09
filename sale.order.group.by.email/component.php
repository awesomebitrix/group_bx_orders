<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['ADMINS_OLNY'] = !empty($arParams['ADMINS_OLNY']) && 'Y' == $arParams['ADMINS_OLNY'];
if ($arParams['ADMINS_OLNY'] && !$USER->IsAdmin()) {
    /* Или можно показывать 403-ю */
    ShowError(GetMessage('UM_ORG_ACCESS_DENIED'));
    return;
}

if (!\Bitrix\Main\Loader::includeModule('sale')) {
    ShowError(GetMessage('UM_ORG_NO_SALE_MODULE'));
    return;
}

if (!isset($arParams['CACHE_TIME'])) {
    $arParams['CACHE_TIME'] = 600;
}

$arParams['PROPERTY_CODE'] = trim($arParams['PROPERTY_CODE']);
if (!$arParams['PROPERTY_CODE']) {
    $arParams['PROPERTY_CODE'] = 'EMAIL';
}

if ($this->StartResultCache()) {
    $arResult['ITEMS'] = [];
    $dbItems = \Bitrix\Sale\Order::getList([
        'filter' => [
            /* мало ли, вдруг тут многосайтовость */
            '=LID' => SITE_ID,
            'PROPERTY.CODE' => $arParams['PROPERTY_CODE'],
        ],
        'runtime' => [
            new Bitrix\Main\Entity\ExpressionField(
                'DOMAIN',
                'SUBSTRING_INDEX(%s, "@", -1)',
                'PROPERTY.VALUE'
            ),
            new Bitrix\Main\Entity\ExpressionField(
                'DOMAIN_CNT',
                'COUNT(%s)',
                'DOMAIN'
            )
        ],
        'select' => ['DOMAIN', 'DOMAIN_CNT'],
        'group' => ['DOMAIN'],
        /* сортировка тоже работает */
        'order' => ['DOMAIN_CNT' => 'ASC'],
    ]);
    while ($row = $dbItems->Fetch()) {
        $arResult['ITEMS'][] = $row;
    }

    /**
     * Если данный функционал будет невероятно востребован и потребует
     * оптимизации, то можно создать новое свойство заказа типа "Домен",
     * при оформлении заказа (скорее всего на событии) заполнять это
     * поле доменом электронной почты, тогда runtime-поле DOMAIN можно
     * заменить на код свойства и работать с уже готовыми значениями
     */

    $this->IncludeComponentTemplate();
}
