# advans/php-api-lrfc

[![Latest Stable Version](https://img.shields.io/packagist/v/advans/php-api-lrfc?style=flat-square)](https://packagist.org/packages/advans/php-api-lrfc)
[![Total Downloads](https://img.shields.io/packagist/dt/advans/php-api-lrfc?style=flat-square)](https://packagist.org/packages/advans/php-api-lrfc)

## Instalación usando Composer

```sh
$ composer require advans/php-api-lrfc
```

## Ejemplo

````
$config = new \Advans\Api\Lrfc\Config([
    'base_url' => '*************************',
    'key' => '**********************'
]);
$service_lrfc = new \Advans\Api\Lrfc\Lrfc($config);

$response = $service_lrfc->getByRFC('CACX7605101P8');
````

## Configuración

| Parámetro | Valor por defecto | Descripción |
| :--- | :--- | :--- |
| base_url | null | URL de la API |
| key | null | API Key |
