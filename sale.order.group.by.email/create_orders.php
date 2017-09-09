<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__. '/../../../../');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Sale\{Order,Basket,Delivery,Paysystem};

if (!\Bitrix\Main\Loader::includeModule('sale')) {
    die('No "sale" module installed' . PHP_EOL);
}

function getEmail()
{
    static $domains;
    if (!$domains) {
        $domains = [
            'mail.ru',
            'gmail.com',
            'yandex.ru'
        ];
    }
    return strtolower(randString(rand(5,8))) . '@'
        . $domains[rand(0, sizeof($domains) - 1)];
}

$products_ids = [
    188,
    192,
    197,
    230,
    240
];

/* As we have only one user here */
$user_id = 1;
/* Site_id of a default site */
$site_id = 's1';
/* Default currency code */
$currency_code = 'RUB';
$person_type_id = 1;
/* Доставка курьером */
$delivery_id = 1;
/* Наличные курьеру */
$paysystem_id = 1;

$payer_name = 'James Bond';

$orders_limit = 10;
$i = 0;
$cou = 0;
while ($i++ < $orders_limit) {
    $order = Order::create(
        $site_id,
        $user_id,
        $currency_code
    );

    $order->setPersonTypeId($person_type_id);

    /* Add random product to basket */
    $basket = Basket::create($site_id);
    $item = $basket->createItem(
        'catalog',
        $products_ids[rand(0, sizeof($products_ids) - 1)]
    );
    $item->setFields([
        'QUANTITY' => 1,
        'CURRENCY' => $currency_code,
        'LID' => $site_id,
        'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
    ]);
    $order->setBasket($basket);

    /* Add delivery to order */
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $service = Delivery\Services\Manager::getById($delivery_id);
    $shipment->setFields([
        'DELIVERY_ID' => $service['ID'],
        'DELIVERY_NAME' => $service['NAME'],
    ]);
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    $shipmentItem = $shipmentItemCollection->createItem($item);
    $shipmentItem->setQuantity($item->getQuantity());

    /* Add payments system to order */
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection->createItem();
    $paySystemService = PaySystem\Manager::getObjectById($paysystem_id);
    $payment->setFields(array(
        'PAY_SYSTEM_ID' => $paySystemService->getField('PAY_SYSTEM_ID'),
        'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
    ));

    /* Set order properties */
    $propertyCollection = $order->getPropertyCollection();
    $emailProp = $propertyCollection->getUserEmail();
    $emailProp->setValue(getEmail());
    $nameProp = $propertyCollection->getPayerName();
    $nameProp->setValue($payer_name);

    /* Save order */
    $order->doFinalAction(true);
    if ($order->save()) {
        $cou++;
    } else {
        echo '/!\ Fail to save new order' . PHP_EOL;
    }
}

echo $cou . ' orders created' . PHP_EOL;
