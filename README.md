## Laravel-panic

Lib para executar requisições de pânico para as secretarias de segurança indicadas.

## Requisitos

1º: Verificar se as funções do helper do projeto existem e se estão de acordo com as funções do helper do projeto de mobilidade web5.

## Instalação

Adicione o pacote no composer.json:


```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://libs:ofImhksJ@git.codificar.com.br/laravel-libs/laravel-panic.git"
    }
]
```


```json
require:{
        "codificar/panic": "0.1.0",
}
```


Agora Adicione no Autoload do Composer.json

```json
    "autoload": {
        //...
        "psr-4": {
    		"Codificar\\Panic\\": "vendor/codificar/panic/src",
    		"Codificar\\Panic\\Tests\\": "vendor/codificar/panic/tests/"
            //...
        }
    },
```

Update project dependencies:

```shell
$ composer update
```


Register the service provider in `config/app.php`:

```php
'providers' => [
  /*
   * Package Service Providers...
   */
  Codificar\Panic\PanicServiceProvider::class,
],
```


Publish Js Libs and Tests:

```shell
$ php artisan vendor:publish --tag=public_vuejs_libs --force
```


Run the migrations:

```shell
$ php artisan migrate
```


---

# Langs

-pt-br
-en
-ao

### ROTAS

## {POST} /lib/panic/save

| Parâmetros   | Tipo   | Descrição                                     |
| ------------ | ------ | --------------------------------------------- |
| `ledger_id`  | Number | Id do Ledger que originou o pedido de pânico  |
| `request_id` | Number | Id do request que originou o pedido de pânico |

---

| Retorno   | Tipo    | Descrição                                                |
| --------- | ------- | -------------------------------------------------------- |
| `success` | Boolean | `true` se o cartão foi criado e `false` se foi recusado. |
| `id`      | Number  | Id do pedido de pânico que foi salvo no banco            |

---

## {DELETE} /lib/panic/delete

| Parâmetros | Tipo   | Descrição                                |
| ---------- | ------ | ---------------------------------------- |
| `id`       | Number | Id do request de panico para ser apagado |

---

| Retorno   | Tipo    | Descrição                        |
| --------- | ------- | -------------------------------- |
| `success` | Boolean | `true` se o request foi apagado. |

---

## {GET} /lib/panic/settings

| Retorno                         | Tipo    | Descrição                                            |
| ------------------------------- | ------- | ---------------------------------------------------- |
| `panic_button_enabled_user`     | Boolean | Indica se o botão foi habilitado no app do usuário.  |
| `panic_button_enabled_provider` | Boolean | Indica se o botão foi habilitado no app do provedor. |

---

## {POST} /lib/panic/settings/save

| Parâmetros                      | Tipo   | Descrição                                                                        |
| ------------------------------- | ------ | -------------------------------------------------------------------------------- |
| `panic_button_enabled_user`     | String | Indica se o botão será habilitado no app do usuário. Deve ser `true` ou `false`  |
| `panic_button_enabled_provider` | String | Indica se o botão será habilitado no app do provedor. Deve ser `true` ou `false` |

---

| Retorno                         | Tipo    | Descrição                                            |
| ------------------------------- | ------- | ---------------------------------------------------- |
| `panic_button_enabled_user`     | Boolean | Indica se o botão será habilitado no app do usuário. |
| `panic_button_enabled_provider` | String  | Indica se o botão será habilitado no app do usuário. |

---

## {GET} /lib/panic/settings/segup

| Retorno                  | Tipo    | Descrição                             |
| ------------------------ | ------- | ------------------------------------- |
| `success`                | Boolean | Indica se o request foi bem sucedido. |
| `segup_login`            | String  | Dados do Login da Segup.              |
| `segup_password`         | String  | Password do Login da Segup.           |
| `segup_request_url`      | String  | Url de Login da Segup                 |
| `segup_verification_url` | String  | Url de verificação da url.            |

---

## {POST} /lib/panic/settings/save

| Parâmetros                 | Tipo   | Descrição                                                                     |
| -------------------------- | ------ | ----------------------------------------------------------------------------- |
| `security_provider_agency` | String | Secretaria de segurança a ser usada, `segup` é a única registrada no momento. |
| `segup_login`              | String | Dados do Login da Segup.                                                      |
| `segup_password`           | String | Password do Login da Segup.                                                   |
| `segup_request_url`        | String | Url de Login da Segup                                                         |
| `segup_verification_url`   | String | Url de verificação da url.                                                    |

---

| Retorno                    | Tipo    | Descrição                                                                     |
| -------------------------- | ------- | ----------------------------------------------------------------------------- |
| `success`                  | Boolean | Indica se o request foi bem sucedido.                                         |
| `security_provider_agency` | String  | Secretaria de segurança a ser usada, `segup` é a única registrada no momento. |
| `segup_login`              | String  | Dados do Login da Segup.                                                      |
| `segup_password`           | String  | Password do Login da Segup.                                                   |
| `segup_request_url`        | String  | Url de Login da Segup                                                         |
| `segup_verification_url`   | String  | Url de verificação da url.                                                    |

---

## {GET} /lib/panic/settings/admin

| Retorno                    | Tipo   | Descrição                                                                                             |
| -------------------------- | ------ | ----------------------------------------------------------------------------------------------------- |
| `panic_admin_id`           | Number | O Id do admin registrado para receber os alertas, deve ser igual ao id do admin registrado no painel. |
| `panic_admin_phone_number` | Number | Telefone para enviar os alertas. Formato: +XXYYZZZZZZZZZ.                                             |
| `panic_admin_email`        | String | Email para enviar os alertas.                                                                         |

---

## {POST} /lib/panic/settings/save/admin

| Parâmetros                 | Tipo   | Descrição                                                                                             |
| -------------------------- | ------ | ----------------------------------------------------------------------------------------------------- |
| `panic_admin_id`           | Number | O Id do admin registrado para receber os alertas, deve ser igual ao id do admin registrado no painel. |
| `panic_admin_phone_number` | Number | Telefone para enviar os alertas. Formato: +XXYYZZZZZZZZZ.                                             |
| `panic_admin_email`        | String | Email para enviar os alertas.                                                                         |

| Retorno                    | Tipo    | Descrição                                                                                             |
| -------------------------- | ------- | ----------------------------------------------------------------------------------------------------- |
| `success`                  | Boolean | Indica se o request foi bem sucedido.                                                                 |
| `panic_admin_id`           | Number  | O Id do admin registrado para receber os alertas, deve ser igual ao id do admin registrado no painel. |
| `panic_admin_phone_number` | Number  | Telefone para enviar os alertas. Formato: +XXYYZZZZZZZZZ.                                             |
| `panic_admin_email`        | String  | Email para enviar os alertas.                                                                         |

---
