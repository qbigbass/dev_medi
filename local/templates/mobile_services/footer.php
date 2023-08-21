<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
</main>
<? // Holder для бокового меню
if ($USER->IsAuthorized()):
    $groups = $USER->GetUserGroupArray();
    ?>
<div id="sidr">
    <ul>

        <?if (in_array(1, $groups)){?>
        <li><a href="/local/tools/order_status.php">Статусы заказов</a></li>
        <?}?>
        <?if (in_array(1, $groups) || in_array(9, $groups)  ){?>
        <li><a href="/local/tools/refund.php">Возвраты</a></li>
        <?}?>
        <?if (in_array(1, $groups) || in_array(8, $groups) ){?>
        <li><a href="/local/tools/marks.php">Разметка для акций</a></li>
        <li><a href="/local/tools/shoes_upd.php">MRObuv update</a></li>
        <?}?>

        <li><a href="/?logout=yes">Выход</a></li>
    </ul>
</div>
<?endif;?>
</body>
</html>
