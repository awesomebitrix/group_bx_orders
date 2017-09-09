<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "PROPERTY_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("UM_ORG_PROPERTY_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => "EMAIL",
        ),
        "ADMINS_OLNY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("UM_ORG_PARAM_ADMINS_ONLY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "CACHE_TIME"  => array("DEFAULT" => 600),
    )
);
