=== Better Shipping Calculator for Brazilian Markets ===
Contributors: luizbills
Donate link: https://www.luizpb.com/en/
Tags: woocommerce, brasil, brazil, calculadora, simulador, frete
Requires at least: 4.6
Tested up to: 5.7
Stable tag: 2.0.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Calculadora de frete do WooCommerce sem os campos de País e Estado.

== Description ==

Calculadora de frete do WooCommerce sem os campos de País e Estado.

> Github: https://github.com/luizbills/wc-better-shipping-calculator-for-brazil

== Installation ==

1. Instale o plugin pela página "Plugins" no seu wp-admin e ative-o.
1. Pronto! Não precisa fazer mais nada.

== Screenshots ==

1. calculadora de frete somente com o campo de CEP.

== Frequently Asked Questions ==

= Este plugin é compatível com a versão 3.0 ou mais recente? =

Sim.

= Posso fazer apenas o campo de estado ser ocultado? =

Sim. Adicione o código abaixo no seu `functions.php` ou use um [plugin para adicionar códigos](https://medium.com/@luizbills/adicione-php-ao-seu-tema-wordpress-sem-ter-que-editar-o-functions-php-66728752f9f4):

```php
add_filter( 'wc_better_shipping_calculator_for_brazil_hide_country', function () {
  return false;
} );
```

== Changelog ==

= 2.0.1 =
* Internal fixes

= 2.0.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
* Initial release
