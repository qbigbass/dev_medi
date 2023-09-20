<?
	use Bitrix\Main\Localization\Loc;
	\Bitrix\Main\Page\Asset::getInstance()->addJs("https://yookassa.ru/checkout-widget/v1/checkout-widget.js");
	Loc::loadMessages(__FILE__);

	$sum = roundEx($params['SUM'], 2);
?>

<div class="sale-paysystem-wrapper">
	<div id="vampirus-yookassa-payment-form"></div>
	<script>
  //������������� �������. ��� ��������� ������������.
  const checkout = new window.YooMoneyCheckoutWidget({
      confirmation_token: '<?=$params['TOKEN']?>', //�����, ������� ����� ����������� ������ ����� �������� �� �Kassa
      return_url: '<?=$params['RETURN_URL']?>', //������ �� �������� ���������� ������, ��� ����� ���� ����� ���� ��������
      <?php if ($params['TYPE']):?>
       customization: {
        payment_methods: ['<?=$params['TYPE']?>']
      },
      <?php endif;?>
      error_callback: function(error) {
          console.log(error)
      }
  });

  //����������� ��������� ����� � ����������
  checkout.render('vampirus-yookassa-payment-form');
  </script>

</div>