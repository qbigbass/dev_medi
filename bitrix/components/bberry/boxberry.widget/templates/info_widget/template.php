<?
CBoxberry::addWidgetJs();
CBoxberry::initApi();
$getKey = CBoxberry::getKeyIntegration();
$key = $getKey['key'] ?? '';
?>

<div id="boxberry_widget"></div>
<script>
    boxberry.openOnPage('boxberry_widget');
    boxberry.open('', '<?=$key?>', '', '', '', 100);
</script>