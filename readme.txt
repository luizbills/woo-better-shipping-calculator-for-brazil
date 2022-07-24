=== Calculadora de frete melhorada para lojas brasileiras ===
Contributors: luizbills
Donate link: https://luizpb.com/donate/
Tags: woocommerce, brasil, brazil, calculadora de frete, frete
Requires at least: 4.6
Tested up to: 6.0
Stable tag: 2.1.2
Requires PHP: 7.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Calculadora de frete do WooCommerce sem os campos de País e Estado. Deixando somente o campo de CEP sempre vísivel.

== Description ==

Calculadora de frete do WooCommerce sem os campos de Cidade, Estado e País. Deixando somente o campo de CEP sempre vísivel.

= Ajuda e Suporte =

Quando precisar de ajuda, crie um tópico no [Fórum do Plugin](https://wordpress.org/support/plugin/woo-better-shipping-calculator-for-brazil/).

= Contribuições =

Se descobrir algum bug ou tiver sugestões, abra uma issue no nosso [repositório do Github](https://github.com/luizbills/wc-better-shipping-calculator-for-brazil).

= Doações =

Me ajude a manter este plugin sempre atualizado, doando em [https://luizpb.com/donate/](https://luizpb.com/donate/).

== Installation ==

1. Instale o plugin pela página "Plugins" no seu wp-admin e ative-o.
1. Pronto! Não precisa fazer mais nada.

== Screenshots ==

1. calculadora de frete somente com o campo de CEP.

== Frequently Asked Questions ==

= Posso fazer apenas o campo de estado ser ocultado? =

Sim. Use o código abaixo:

https://gist.github.com/luizbills/6058ee9e94d9b6f4058118ea227d78f2

== Changelog ==

= 2.1.2 =
* Minor fixes.

= 2.1.1 =
* Fix JavaScript

= 2.1.0 =
* Plugin name changed to "Calculadora de frete melhorada para lojas brasileiras"
* Now the postcode field is always visible
* New hook filter: `wc_better_shipping_calculator_for_brazil_add_postcode_mask` (default: `true`
* New hook filter: `wc_better_shipping_calculator_for_brazil_postcode_label` (default: `"Calcule o frete:"`)
* Fix register_activation_hook

= 2.0.4 =
* Fix pt_BR translation
* Tested with WordPress 6.0 and WooCommerce 6.5

= 2.0.3 =
* Fix an syntax error with older versions of PHP

= 2.0.2 =
* JavaScript fixes
* Added PT-BR translation

= 2.0.1 =
* Internal fixes

= 2.0.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
* Initial release
