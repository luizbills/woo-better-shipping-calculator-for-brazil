# Calculadora de frete melhorada para lojas brasileiras

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/woo-better-shipping-calculator-for-brazil?label=Plugin%20Version&logo=wordpress&style=flat-square)](https://wordpress.org/plugins/woo-better-shipping-calculator-for-brazil/)
[![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/woo-better-shipping-calculator-for-brazil?label=PHP%20Required&logo=php&logoColor=white&style=flat-square)](https://wordpress.org/plugins/woo-better-shipping-calculator-for-brazil/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/woo-better-shipping-calculator-for-brazil?label=Plugin%20Rating&logo=wordpress&style=flat-square)](https://wordpress.org/support/plugin/woo-better-shipping-calculator-for-brazil/reviews/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/woo-better-shipping-calculator-for-brazil.svg?label=Downloads&logo=wordpress&style=flat-square)](https://wordpress.org/plugins/woo-better-shipping-calculator-for-brazil/advanced/)
[![License](https://img.shields.io/badge/LICENSE-GPLv3-blue?style=flat-square)](https://wordpress.org/plugins/woo-better-shipping-calculator-for-brazil/)

## Descrição

Calculadora de frete do WooCommerce sem os campos de Cidade, Estado e País. Deixando somente o campo de CEP sempre vísivel.

### Doações

[![Donate](https://img.shields.io/badge/ME%20APOIE-DOAÇÕES-2b8a3e?style=for-the-badge)](https://luizpb.com/donate/)

## Instalação

-   Baixa o plugin em: https://wordpress.org/plugins/woo-better-shipping-calculator-for-brazil/
-   Ou pelo seu próprio WordPress pesquise pelo plugin **WooCommerce Better Shipping Calculator for Brazil**.

## Perguntas frequentes

### Este plugin é compatível com a versão 3.0 ou mais recente?

Sim. Este plugin foi testado com a versão 2.6 e, até o momento, com a versão 3.3 do WooCommerce.

### Posso fazer apenas o campo de estado ser ocultado?

Sim. Adicione o código abaixo no seu `functions.php` ou use um [plugin para adicionar códigos](https://medium.com/@luizbills/adicione-php-ao-seu-tema-wordpress-sem-ter-que-editar-o-functions-php-66728752f9f4):

```php
add_filter( 'wc_better_shipping_calculator_for_brazil_hide_country', function () {
  return false;
} );
```

## Licensa

[GPL v2](https://github.com/luizbills/wc-better-shipping-calculator-for-brazil/blob/master/LICENSE)
