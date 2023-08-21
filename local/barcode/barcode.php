<?

require_once($_SERVER['DOCUMENT_ROOT']."/local/barcode/BarcodeGenerator.php");
require_once($_SERVER['DOCUMENT_ROOT']."/local/barcode/BarcodeGeneratorPNG.php");

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php');

if (isset($_SESSION['barcode']))
{
    $code = $_SESSION['barcode'];
}
else
    die;

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
header("Content-type: image/png");
echo $generator->getBarcode($code, $generator::TYPE_EAN_13);
