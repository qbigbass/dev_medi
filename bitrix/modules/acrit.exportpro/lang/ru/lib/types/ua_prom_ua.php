<?
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_NAME"] = "Выгрузка на портал Prom.ua";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_NAME"] = "Название товара.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_OLDPRICE"] = "Если у товара есть скидка, в данном поле указывается цена без учета скидки.<br/> При наличии данного тега тег <price> является обязательным.";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_URL"] = "URL страницы товара.<br/>Максимальная длина URL — 512 символов.<br/>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_VENDOR"] = "Производитель. Не отображается в названии предложения";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_WARRANTY"] = "Число месяцев, на которые дается гарантия на товар. Необязательный элемент.";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_AVAILABLE_AREA"] = "Обязательное поле для участников программы: \"Честные цены\". Доступные значения: Склад, Заказ";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_AVAILABLE_AREA_DEFAULT_VALUE"] = "Склад";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";

$MESS["ACRIT_EXPORTPRO_UA_PROM_UA_FIELD_PARAM"] = "Характеристики товара";
$MESS["ACRIT_EXPORTPRO_TYPE_UA_PROM_UA_PORTAL_REQUIREMENTS"] = "http://support.prom.ua/documents/290";
$MESS["ACRIT_EXPORTPRO_TYPE_UA_PROM_UA_EXAMPLE"] = "
<item id=\"330\">
    <name>Motorola A1200</name>
    <categoryId>1001</categoryId>
    <price>1260</price>
    <bnprice>1300</bnprice>
    <url>http://url/catalog/1/1/330/</url>
    <image>http://url/images/image7402727981188804741.jpg</image>
    <vendor>Motorola</vendor>
    <description>GSM 900/1800/1900. Тип корпуса: раскладушка. Аккумулятор: 850 мАч. цветной TFT экран</description>
    <warranty>12</warranty>
</item>
";
?>