<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

if (!empty($_GET['goto'])) {
    CHTTP::SetStatus("500 Internal Server Error");
    die();
}
//include module
\Bitrix\Main\Loader::includeModule("dw.deluxe");
//get template settings
$arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();
extract($arTemplateSettings);
?>
<?
if (!empty($TEMPLATE_TOP_MENU_FIXED)) {
    $_SESSION["TOP_MENU_FIXED"] = $TEMPLATE_TOP_MENU_FIXED;
}
?>
<? if (isset($_GET['product'])
    || isset($_GET['yandex-source'])
    || isset($_GET['advert_id'])
) {
    LocalRedirect($APPLICATION->GetCurDir(), 0, '301 Moved permanently');
} ?>
<?
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" type="image/x-icon" href="//www.medi-salon.ru/favicon.ico"/>
    <link rel="icon" type="image/x-icon" href="//www.medi-salon.ru/favicon.ico"/>
    <link rel="icon" type="image/svg+xml" href="//www.medi-salon.ru/upload/images/logo_medi.svg"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="mailru-verification" content="f65f5a758a60e30f"/>
    <meta name="theme-color" content="#ed008c"/>
    <? if (!empty($_REQUEST['SORT'])
        || !empty($_REQUEST['sort'])
        || !empty($_REQUEST['SORT_TO'])
        || !empty($_REQUEST['utm_source'])
        || !empty($_REQUEST['utm_sourse'])
        || !empty($_REQUEST['utm_medium'])
        || !empty($_REQUEST['calltouch'])
        || !empty($_REQUEST['yclid'])
        || !empty($_REQUEST['_openstat'])
        || !empty($_REQUEST['action'])
        || !empty($_REQUEST['backurl'])
        || !empty($_REQUEST['index.php'])
        || !empty($_REQUEST['bxajaxid'])
        || !empty($_REQUEST['source'])
    ) {
        ?>
        <meta name="robots" content="noindex, nofollow"/>
        <meta name="googlebot" content="noindex"/><?
    } ?>
    <? if (!empty($_REQUEST['offerID'])) { ?>
        <meta name="googlebot" content="noindex"/>
        <?
    } ?>
    <? CJSCore::Init(array("fx", "ajax", "window", "popup", "date")); ?>
    <? global $USER;
    $nUserID = 0;
    $nUserID = $USER->GetID(); ?>
    <?
    $dedupl = 'other';
    if (isset($_REQUEST['utm_source']) && !empty(htmlspecialchars($_REQUEST['utm_source']))) {
        setcookie('client_utm_source', htmlspecialchars($_REQUEST['utm_source']), time() + 30 * 86400, "/");
        setcookie('client_utm_time', time(), time() + 30 * 86400, "/");
        setcookie("gdeslon_ru___arc_domain", "", time() - 86400, "/");
        setcookie("gdeslon_ru_user_id", "", time() - 86400, "/");
        unset($_COOKIE['gdeslon_ru_user_id']);
        unset($_COOKIE['gdeslon_ru___arc_domain']);
        $dedupl = htmlspecialchars($_REQUEST['utm_source']);

    } elseif (isset($_REQUEST['gsaid']) && !empty(htmlspecialchars($_REQUEST['gsaid']))) {
        setcookie('client_utm_source', "gdeslon", time() + 30 * 86400, "/");
        $dedupl = "gdeslon";
    } elseif (!empty($_COOKIE['client_utm_source'])) {
        $dedupl = $_COOKIE['client_utm_source'];
    } elseif (!empty($_COOKIE['gdeslon_ru___arc_domain'])) {
        $dedupl = $_COOKIE['gdeslon_ru___arc_domain'];
    }
    define("DEDUPLICATION", $dedupl);

    global $nUserEmail;
    global $nUserName;
    $nUserName = 'guest';
    $nUserEmail = '';
    if ($nUserID > 0):
        $nUserName = $USER->GetFullName();
        $nUserEmail = md5($USER->GetEmail());

        if (!isset($_COOKIE['medi_cos'])) {

            CModule::IncludeModule("sale");
            $arOrderFilter = array(
                "USER_ID" => $nUserID,
                "LID" => SITE_ID,
                "STATUS_ID" => "F",
                "CANCELED" => "N",
            );
            $sum = CSaleOrder::__SaleOrderCount($arOrderFilter, 'RUB');
            $userOrdersSum = $sum['PRICE'];

            setcookie('medi_cos', $userOrdersSum, time() + 86400, "/");
        }

    endif; ?>
    <script>var $nUserEmail = '<?=$nUserEmail?>';
      var msite_id = '<?=SITE_ID?>';
      vViewedProdsPrice = 0;</script>
    <? if ($APPLICATION->GetCurDir() == '/catalog/') { ?>
        <link rel="canonical" href="https://www.medi-salon.ru/catalog/"/>
    <? } ?>
    <? if ($APPLICATION->GetCurPage() == '/index.php') { ?>

        <meta property="og:title" content="<? $APPLICATION->ShowTitle(); ?>"/>
        <meta property="og:url" content="https://www.medi-salon.ru"/>
        <meta property="og:description" content="Официальный интернет-магазин ортопедических изделий medi - это
			 большая сеть салонов по всей России. В нашем каталоге представлен широкий выбор ортопедических товаров
			 бренда medi и других производителей, которые можно купить с доставкой по РФ или самовывозом в любом
			 удобном для вас салоне Москвы и РФ."/>
        <meta property="og:image" content="https://www.medi-salon.ru/upload/images/medi-600x600.png"/>
        <link rel="canonical" href="https://www.medi-salon.ru"/>

        <script>
          var dataLayer = window.dataLayer || [];
          dataLayer.push({
            'event': 'page_view',
            'items': [{
              'google_business_vertical': 'retail'
            }]
          });
          dataLayer.push({'event': 'crto_homepage', crto: {'email': '<?=$nUserEmail?>'}});
        </script>
    <? } ?>
    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/fonts/roboto/roboto.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/themes/" . $TEMPLATE_THEME_NAME . "/style.css"); ?>

    <? //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.11.0.min.js");?>
    <? $APPLICATION->AddHeadScript("https://yastatic.net/jquery/2.0.1/jquery.min.js"); ?>

    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.easing.1.3.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.cookie.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.scrollTo.min.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/rangeSlider.js"); ?>
    <? //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/maskedinput.js");?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.mask.min.js"); ?>

    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/style.min.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/slick.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/slick-theme.css"); ?>

    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/main.min.js"); ?>

    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/gdeslon.js"); ?>

    <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/bootstrap-grid.min.css"); ?>

    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/system.js?1"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/topMenu.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/topSearch.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/dwCarousel.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/dwSlider.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/dwTimer.js"); ?>
    <? //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/dwZoomer.js");?>
    <? $APPLICATION->AddHeadScript("https://kit.fontawesome.com/4e1ccc2c65.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/favorite.js"); ?>
    <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/mobile-detect.min.js"); ?>

    <!-- calltouch -->
    <script>(function (w, d, n, c) {
        w.CalltouchDataObject = n;
        w[n] = function () {
          w[n]["callbacks"].push(arguments)
        };
        if (!w[n]["callbacks"]) {
          w[n]["callbacks"] = []
        }
        w[n]["loaded"] = false;
        if (typeof c !== "object") {
          c = [c]
        }
        w[n]["counters"] = c;
        for (var i = 0; i < c.length; i += 1) {
          p(c[i])
        }

        function p(cId) {
          var a = d.getElementsByTagName("script")[0], s = d.createElement("script"), i = function () {
            a.parentNode.insertBefore(s, a)
          }, m = typeof Array.prototype.find === 'function', n = m ? "init-min.js" : "init.js";
          s.type = "text/javascript";
          s.async = true;
          s.src = "https://mod.calltouch.ru/" + n + "?id=" + cId;
          if (w.opera == "[objectOpera]") {
            d.addEventListener("DOMContentLoaded", i, false)
          } else {
            i()
          }
        }
      })(window, document, "ct", "bnkdzxqx");</script>
    <!-- calltouch -->

    <? $Mobile_Detect = new Mobile_Detect;

    //if (!$Mobile_Detect->isMobile()){?>
    <!-- Begin Talk-Me {literal} -->
    <script>
      (function (d, w, m) {
        window.supportAPIMethod = m;
        var s = d.createElement('script');
        s.type = 'text/javascript';
        s.id = 'supportScript';
        s.charset = 'utf-8';
        s.async = true;
        var id = '2e7664badba9b23ba7835f00526f314d';
        s.src = 'https://lcab.talk-me.ru/support/support.js?h=' + id;
        var sc = d.getElementsByTagName('script')[0];
        w[m] = w[m] || function () {
          (w[m].q = w[m].q || []).push(arguments);
        };
        if (sc) sc.parentNode.insertBefore(s, sc);
        else d.documentElement.firstChild.appendChild(s);
      })(document, window, 'TalkMe');
    </script>
    <!-- {/literal} End Talk-Me -->
    <? //} ?>
    <!-- RuTarget -->
    <script>
      (function (w, d, s, p) {
        var f = d.getElementsByTagName(s)[0], j = d.createElement(s);
        j.async = true;
        j.src = '//cdn.rutarget.ru/static/tag/tag.js';
        f.parentNode.insertBefore(j, f);
        w[p] = {rtgNoSync: false, rtgSyncFrame: true};
      })(window, document, 'script', '_rtgParams');
    </script>
    <script>
      if (window.localStorage.getItem('rutarget_sync') !== "true") {
        var _rutarget = window._rutarget || [];
        _rutarget.push({
          'event': 'sync',
          'partner': 'www.medi-salon.ru',
          'external_visitor_id': '<?=intval($nUserID)?><?=($nUserEmail ? '|' . $nUserEmail : '')?>'
        });
        window.localStorage.setItem('rutarget_sync', true);
      }
    </script>
    <!-- /RuTarget -->
    <? if (!strpos($APPLICATION->GetCurDir(), "catalog")
        && !strpos($APPLICATION->GetCurDir(), "cart")
        && !strpos($APPLICATION->GetCurDir(), "order")
    ) { ?>
        <script>
          var _rutarget = window._rutarget || [];
          _rutarget.push({'event': 'otherPage'});
        </script>

    <? } ?>

    <?
    if (strpos($APPLICATION->GetCurPage(), ".html") !== false || strpos($APPLICATION->GetCurDir(), "salons") || strpos($APPLICATION->GetCurDir(), "services") || strpos($APPLICATION->GetCurDir(), "stock")) {
        $APPLICATION->AddHeadScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=391d1c41-5055-400d-8afc-49ee21c8f4a1&load=package.full');
    } ?>

    <? //$APPLICATION->AddHeadString('<script src="https://kit.fontawesome.com/4e1ccc2c65.js" crossorigin="anonymous"></script>');?>
    <? /*if(DwSettings::isPagen()):?><?$APPLICATION->AddHeadString('<link rel="canonical" href="'.DwSettings::getPagenCanonical().'" />');//pagen canonical?><?endif;*/ ?>
    <? if (!DwSettings::isBot() && !empty($arTemplateSettings["TEMPLATE_METRICA_CODE"])): ?><? $APPLICATION->AddHeadString($arTemplateSettings["TEMPLATE_METRICA_CODE"]);//metrica counter code?><? endif; ?>

    <? $APPLICATION->ShowHead(); ?>
    <title><? $APPLICATION->ShowTitle("title"); ?></title>

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
          'gtm.start':
            new Date().getTime(), event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
      })(window, document, 'script', 'dataLayer', 'GTM-5VMKQLZ');</script>
    <!-- End Google Tag Manager -->
    <link rel="preconnect" href="//mc.yandex.ru" crossorigin=""/>
    <link rel="preconnect" href="//metrika.yandex.com" crossorigin=""/>
    <link rel="preconnect" href="//vc.com" crossorigin=""/>
    <link rel="preconnect" href="//top-fwz1.mail.ru" crossorigin=""/>
    <link rel="dns-prefetch" href="//mc.yandex.ru"/>
    <link rel="dns-prefetch" href="//top-fwz1.mail.ru"/>
    <link rel="dns-prefetch" href="//mod.calltouch.ru"/>
    <link rel="dns-prefetch" href="//cdn.rutarget.ru"/>
    <link rel="dns-prefetch" href="//yastatic.net"/>

    <? /*<script defer >
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1387626394933707');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1387626394933707&ev=PageView&noscript=1"
/></noscript>*/ ?>
    <script>!function () {
        var t = document.createElement("script");
        t.type = "text/javascript", t.async = !0, t.src = "https://vk.com/js/api/openapi.js?169", t.onload = function () {
          VK.Retargeting.Init("VK-RTRG-1013227-3hAoK"), VK.Retargeting.Hit()
        }, document.head.appendChild(t)
      }();</script>
    <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1013227-3hAoK" style="position:fixed; left:-999px;" alt=""/>
    </noscript>

    <script>
      const PRICE_LIST_ID = 136797;
      var vViewedProds = [];
    </script>

    <script type="text/javascript">!function () {
        var t = document.createElement("script");
        t.type = "text/javascript", t.async = !0, t.src = 'https://vk.com/js/api/openapi.js?169', t.onload = function () {
          VK.Retargeting.Init("VK-RTRG-1376410-7uHtv"), VK.Retargeting.Hit()
        }, document.head.appendChild(t)
      }();</script>
    <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1376410-7uHtv" style="position:fixed; left:-999px;" alt=""/>
    </noscript>

    <meta name="facebook-domain-verification" content="d9mwvv8jyva13ttj3082x2remxoenf"/>

</head>
<body class="loading <? if (INDEX_PAGE == "Y"): ?>index<? endif; ?><? if (!empty($TEMPLATE_PANELS_COLOR) && $TEMPLATE_PANELS_COLOR != "default"): ?> panels_<?= $TEMPLATE_PANELS_COLOR ?><? endif; ?>">
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5VMKQLZ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<div class="overlay" style="display:none;"></div>
<div class="loader" style="display:none;">
    <row><span></span><span></span></row>
    <row><span></span><span></span></row>
</div>

<div id="foundation"<? if (!empty($TEMPLATE_SLIDER_HEIGHT) && $TEMPLATE_SLIDER_HEIGHT != "default"): ?> class="slider_<?= $TEMPLATE_SLIDER_HEIGHT ?>"<? endif; ?>>
    <div id="top_alert">
        <? $APPLICATION->IncludeComponent(
            "medi:topalert",
            "",
            array(
                "IBLOCK_ID" => 26,
                "IS_MOBILE" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "864000",
            ),
            false
        ); ?>
    </div>
    <? require_once($_SERVER["DOCUMENT_ROOT"] . "/" . SITE_TEMPLATE_PATH . "/headers/" . $TEMPLATE_HEADER . "/template.php"); ?>
    <div id="main"<? if ($TEMPLATE_BACKGROUND_NAME != ""): ?> class="color_<?= $TEMPLATE_BACKGROUND_NAME ?>"<? endif; ?>>
        <? $APPLICATION->ShowViewContent("landing_page_banner_container"); ?>
        <? $APPLICATION->ShowViewContent("before_breadcrumb_container"); ?>
        <? if (!defined("INDEX_PAGE") && !defined("SHOES_PAGE") && !defined("HIDE_LIMITER")): ?>
        <div class="limiter"><? endif; ?>

            <? if (!defined("INDEX_PAGE") && !defined("WIKI_PAGE") && !defined("NO_HEAD_BREADCRUMB") && !defined("SHOES_PAGE") && !defined("ERROR_404")): ?>
                <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
                    "START_FROM" => "0",
                    "PATH" => "",
                    "SITE_ID" => "-",
                ),
                                                  false
                ); ?>
            <? endif; ?>
            <? $APPLICATION->ShowViewContent("after_breadcrumb_container"); ?>
            <? $APPLICATION->ShowViewContent("landing_page_top_text_container"); ?>
