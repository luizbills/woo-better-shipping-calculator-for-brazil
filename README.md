# WooCommerce Better Shipping Calculator for Brazil

Calculadora de frete do WooCommerce sem os campos de País e Estado.

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
