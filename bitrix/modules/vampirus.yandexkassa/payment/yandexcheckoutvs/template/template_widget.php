<?
	use Bitrix\Main\Localization\Loc;
	\Bitrix\Main\Page\Asset::getInstance()->addJs("https://yookassa.ru/checkout-widget/v1/checkout-widget.js");
	Loc::loadMessages(__FILE__);

	$sum = roundEx($params['SUM'], 2);
?>

<div class="sale-paysystem-wrapper">
	<div id="vampirus-yookassa-payment-form"></div>
	<script>
  //Инициализация виджета. Все параметры обязательные.
  const checkout = new window.YooMoneyCheckoutWidget({
      confirmation_token: '<?=$params['TOKEN']?>', //Токен, который перед проведением оплаты нужно получить от ЮKassa
      return_url: '<?=$params['RETURN_URL']?>', //Ссылка на страницу завершения оплаты, это может быть любая ваша страница
      <?php if ($params['TYPE']):?>
       customization: {
        payment_methods: ['<?=$params['TYPE']?>']
      },
      <?php endif;?>
      error_callback: function(error) {
          console.log(error)
      }
  });

  //Отображение платежной формы в контейнере
  checkout.render('vampirus-yookassa-payment-form');
  </script>

</div>