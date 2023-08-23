<?

/**
/* Групповая печать заказов
/* Шаблон для печати /bitrix/admin/reports/user_form.php
/* Вывод шаблона на печать /bitrix/admin/medi_print_orders.php
**/

AddEventHandler("main", "OnAdminListDisplay", "OrdersMultiPrintDisplay");
function OrdersMultiPrintDisplay(&$list)
{
    // Добавляем групповое действие в select в списке заказов
    if($list->table_id == "tbl_sale_order")
        $list->arActions["medi_multi_print_orders"] = "Печать выбранных заказов";
}
// Обработка действия вывода на печать
AddEventHandler("main", "OnBeforeProlog", "OrdersMultiPrintAction");
function OrdersMultiPrintAction()
{
    if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "medi_multi_print_orders"
        && is_array($_POST["ID"]) && $GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order.php")
    {
        if($GLOBALS["APPLICATION"]->GetGroupRight("sale") != "D" && check_bitrix_sessid())
        {
             ?>
             <script type="text/javascript">
             window.open('/bitrix/admin/medi_print_orders.php?ORDER_ID=<?=implode("|", $_POST['ID'])?>&PROPS_ENABLE=Y&SHOW_ALL=Y', '_blank');
             </script>
             <?
        }
    }
}


AddEventHandler("main", "OnBuildGlobalMenu", "MediRegionsMenu");
function MediRegionsMenu(&$adminMenu, &$moduleMenu){
      $moduleMenu[] = array(
         "parent_menu" => "global_menu_settings",
         "section" => "Регионы medi",
         "sort"        => 2000,                    // сортировка пункта меню
         "url"         => "/bitrix/admin/medi.regions.php?lang=".LANG,  // ссылка на пункте меню
         "text"        => 'Регионы medi',       // текст пункта меню
         "title"       => 'Регионы medi', // текст всплывающей подсказки
         "icon"        => "fileman_menu_icon", // малая иконка
         "page_icon"   => "form_page_icon", // большая иконка
         "items_id"    => "menu_regions",  // идентификатор ветви
         "items"       => array()          // остальные уровни меню сформируем ниже.
            );
}
