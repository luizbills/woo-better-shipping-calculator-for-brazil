# WooCommerce Better Shipping Calculator for Brazil

Calculadora de frete do WooCommerce sem os campos de País e Estado. 

## Observações

- Ainda precisa ser testado em vários temas, somente testei com o Storefront, GeneratePress, Flatsome e Porto. Caso ocorra qualquer erro ou comportamento estranho, abra uma [nova issue](https://github.com/luizbills/wc-better-shipping-calculator-for-brazil/issues/new) **informando a versão do seu WordPress, a versão do seu WooCommerce, assim como o nome e versão do seu tema**.

## Instalação

1. Baixe o arquivo *zip* da [versão mais recente](https://github.com/luizbills/wc-better-shipping-calculator-for-brazil/releases/download/1.0.2/wc-better-shipping-calculator-for-brazil.zip).
1. Vá no seu *wp-admin* em **Plugins > Adicionar novo** e depois em **Fazer upload do plugin**.
1. Faça upload do zip que você baixou.
1. Ative o plugin.

*Pronto! Sua calculadora de frete agora só vai mostrar o campo de CEP.*

## Perguntas frequentes

### Este plugin é compatível com a versão 3.0 ou mais recente?
Sim. Este plugin foi testado com a versão 2.6 e, até o momento, com a versão 3.2 do WooCommerce.

### Posso fazer apenas o campo de estado ser ocultado?
Sim. Adicione o código abaixo no seu `functions.php` ou use um [plugin para adicionar códigos](https://medium.com/@luizbills/adicione-php-ao-seu-tema-wordpress-sem-ter-que-editar-o-functions-php-66728752f9f4):
```php
add_filter( 'wc_better_shipping_calculator_for_brazil_hide_country', function () {
  return false;
} );
```

## Licensa

[GPL v2](https://github.com/luizbills/wc-better-shipping-calculator-for-brazil/blob/master/LICENSE)
