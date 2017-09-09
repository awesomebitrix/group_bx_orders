<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!empty($arResult['ITEMS'])) {?>
<h2><?=GetMessage('UM_ORG_RESULTS_HEADER')?></h2>
<table style="border:1px solid #f5f5f5; text-align:right;">
    <thead>
        <tr>
            <td><?=GetMessage('UM_ORG_RESULTS_DOMAIN')?></td>
            <td><?=GetMessage('UM_ORG_RESULTS_COUNTER')?></td>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($arResult['ITEMS'] as $item) {?>
        <tr>
            <td><?=$item['DOMAIN']?> </td>
            <td><?=$item['DOMAIN_CNT']?></td>
        </tr>
<?php
    }?>
    </tbody>
</table>
<?php
} else {?>
<p><?=GetMessage('UM_ORG_NO_RESULTS')?></p>
<?php
}
