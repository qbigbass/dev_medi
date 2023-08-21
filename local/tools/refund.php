<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возвраты");

require __DIR__ . '/kassa/autoload.php';

use YooKassa\Client;

$client = new Client();
$client->setAuth('695473', 'live_m9LF_gRI-pj6Zi5tM_VR-jcgh7kxTAs6N2VU84BvOxA');




?>
<style>
.tool_notice {padding: 1em;margin-bottom: 2em;}
.tool_notice.error {
    border: 1px solid #e20074;
    background: #fff5fa;
    color: #e20074;
}
.tool_notice.alert {
    border: 1px solid #38e200;
    background: #f7fff5;
    color: #03e200;
}
</style>
<div class="container">
    <?
    if (isset($_SESSION['tools']['alert'])  || isset($_SESSION['tools']['error']) ){?>
    <div class=" tool_notice <?=($_SESSION['tools']['alert'] ? 'alert' : 'error')?>">
        <?=$_SESSION['tools']['alert']?>
        <?=$_SESSION['tools']['error']?>
    </div>
    <?
    unset($_SESSION['tools']['alert']);
    unset($_SESSION['tools']['error']);
    }?>

    <form method="post" action="">
    <div class="row">
        <div class="eight columns">

            <label for="order-form__paymentId" class="order-form__label--truncate">Id транзакции:</label>
            <input  class="u-full-width" id="order-form__paymentId" type="text"  name="paymentId" value="<?=$_SESSION['tools']['refund']['paymentId']?>">
        </div>
    </div>
    <div class="row">
        <div class="two columns">
            <label for="order-form__amount" class="order-form__label--truncate">Общая сумма возврата:</label>
            <input  class="u-full-width" id="order-form__amount" type="text"  name="amount" value="<?=$_SESSION['tools']['refund']['amount']?>">
        </div>
        <div class="three columns">
            <label for="order-form__phone" class="order-form__label--truncate">Телефон для чека:</label>
            <input  class="u-full-width" id="order-form__phone" type="text"  maxlength="12" name="phone" placeholder="+7XXXXXXXXXX"value="<?=$_SESSION['tools']['refund']['phone']?>">
        </div>
        <div class="one columns">
        или </div>
        <div class="three columns">
            <label for="order-form__email" class="order-form__label--truncate">Email для чека:</label>
            <input  class="u-full-width" id="order-form__email" type="text"  name="email" value="<?=$_SESSION['tools']['refund']['email']?>">
        </div>
    </div>
    <div class="row">
        <div class="ten columns">
            <b>Список наименований по чеку возврата:</b><br/><br/>
        </div>
    </div>
    <?for($i=0;$i<3;$i++):?>
    <div class="row">
        <div class="four columns">
            <label for="order-form__item_<?=$i?>" class="order-form__label--truncate">Товар:</label>
            <input  class="u-full-width" id="order-form__item_<?=$i?>" type="text"  name="item_desc[<?=$i?>]" value="<?=$_SESSION['tools']['refund']['item_desc'][$i]?>">
        </div>
        <div class="three columns">
            <label for="order-form__item_amount_<?=$i?>" class="order-form__label--truncate">Сумма возврата по товару:</label>
            <input  class="u-full-width" id="order-form__item_amount_<?=$i?>" type="text"  name="item_amount[<?=$i?>]" value="<?=$_SESSION['tools']['refund']['item_amount'][$i]?>">
        </div>
        <div class="three columns">
            <label for="order-form__item_tax_<?=$i?>" class="order-form__label--truncate">Налог:</label>
            <select class="u-full-width" id="order-form__tax_<?=$i?>" type="text"  name="item_tax[<?=$i?>]" >
                <option value="1" <?=($_SESSION['tools']['refund']['item_tax'][$i] == '1' ? 'selected="selected"':'')?>>без НДС</option>
                <option value="2" <?=($_SESSION['tools']['refund']['item_tax'][$i] == '2' ? 'selected="selected"':'')?>>НДС 0%</option>
                <option value="3" <?=($_SESSION['tools']['refund']['item_tax'][$i] == '3' ? 'selected="selected"':'')?>>НДС 10%</option>
                <option value="4" <?=($_SESSION['tools']['refund']['item_tax'][$i] == '4' ? 'selected="selected"':'')?>>НДС 20%</option>
            </select>
        </div>
    </div>
    <?endfor;?>
    <div class="row">
        <div class="ten columns">
            <label for="order-form__dateconfirm" class="order-form__label--truncate">Подтверждение:</label>
            <input  class="u-full-width" id="order-form__dateconfirm" type="text"  name="datecheck" value="" placeholder="<?=date("Hdm")?>">
        </div>
    </div>
    <div class="row">
        <div class="ten columns">
            <input type="hidden" name="action" value="refund"/>
            <input type="submit" class="button button-primary" name="refund" onsubmit="return confirm('Подтвердите возврат');" value="Отправить"/>
        </div>
    </div>
    </form>
</div>
<?

// возврат
if (isset($_POST['action']) && $_POST['action'] == 'refund'){
    if ($_REQUEST['datecheck'] == date("Hdm")){

        $_SESSION['tools']['refund'] = $_POST;
        $paymentId = trim($_POST['paymentId']);
        $full_amount = str_replace(",",".", $_POST['amount']);
        $amount = floatval($full_amount);

        if ($paymentId == '' )
        {
            $_SESSION['tools']['error'] = 'Не указан Id транзакции';
            LocalRedirect("/local/tools/refund.php");die;
        }

        $customer = [];
        if (trim($_REQUEST['phone']) == '' && trim($_REQUEST['email']) == '' )
        {
            $_SESSION['tools']['error'] = 'Не указан телефон или email';
            LocalRedirect("/local/tools/refund.php");die;
        }
        else
        {
            if (trim($_REQUEST['phone'])) $customer['phone'] = trim($_REQUEST['phone']);
            if (trim($_REQUEST['email'])) $customer['email'] = trim($_REQUEST['email']);
        }

        $items = [];
        $all_amount = 0;
        if (!empty($_REQUEST['item_desc'])) {
            foreach($_REQUEST['item_desc'] as $ik =>$iv)
            {
                if (trim($iv) != '')
                {
                    $item_amount = str_replace(",",".", $_REQUEST['item_amount'][$ik]);
                    $items[] = [
                        'description' => $iv,
                        'quantity' => '1',
                        'amount' => array(
                            'value' => floatval($item_amount),
                            'currency' => 'RUB',
                        ),
                        'vat_code' => $_REQUEST['item_tax'][$ik]
                    ];
                    $all_amount+= floatval($item_amount);
                }
            }
        }
        else {
            $_SESSION['tools']['error'] = 'Не указан перечень товаров';
            LocalRedirect("/local/tools/refund.php");die;
        }

        if ($all_amount != $amount)
        {
            $_SESSION['tools']['error'] = 'Общая сумма не совпадает с суммой всех товаров ('.$amount.' != '.$all_amount.' )';
            LocalRedirect("/local/tools/refund.php");die;
        }

        if (!empty($customer) && !empty($items)){
            $refund = [
                'payment_id' => $paymentId,
                'amount' => [
                    'value' => $amount,
                    'currency' => 'RUB',
                ],
                'receipt' => [
                    'customer'=> $customer,
                    'items' => $items
                ]
            ];
        }



        $payment = $client->getPaymentInfo($paymentId);



        $idempotenceKey = uniqid('', true);

        $response = $client->createRefund(
          $refund,
          $idempotenceKey
        );

        ob_start();
        var_dump($response);

        $alert_text = ob_get_clean();


        $_SESSION['tools']['alert'] = $alert_text ;

        unset($_SESSION['tools']['refund']);
        LocalRedirect("/local/tools/refund.php");die;
    }
    else {


        $_SESSION['tools']['error'] = 'Не верно введено число-подтверждение';
        LocalRedirect("/local/tools/refund.php");die;

    }
}
/*
1 — без НДС;

2 — ставка НДС 0%;

3 — ставка 10%;

4 — ставка 20%;


'items' => array(
    array(
        'description' => 'Скидка 20% Мужские компрессионные шорты CEP для занятий спортом базовые (C4UM - M - 5)',
        'quantity' => '1',
        'amount' => array(
            'value' => '1040.00',
            'currency' => 'RUB',
        ),
        'vat_code' => 3
    ),
    array(
        'description' => 'Доставка Boxberry (Самовывоз из пункта выдачи)',
        'quantity' => '1',
        'amount' => array(
            'value' => '42.00',
            'currency' => 'RUB',
        ),
        'vat_code' => 1
    )
)
*/


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
