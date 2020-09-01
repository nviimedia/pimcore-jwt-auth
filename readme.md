# <a name="jwt-module"></a>Pimcore 5 JWT module

This bundle provides JWT (Json Web Token) authentication for your Pimcore API.
It is based on [`lexik/jwt-authentication-bundle`](https://packagist.org/packages/lexik/jwt-authentication-bundle)

It is compatible and tested with PHP 7.1 and Pimcore 5.x.

**Table of Contents**

- [JWT module](#jwt-module)
	- [Prerequisites](#prerequisites)
	- [Installation](#installation)
	- [Configuration](#configuration)
	- [Usage](#usage)
	- [Usage](#usage)
	- [Features](#features)
	- [Supported Pimcore types](#supported-types)
	- [Standards & Code Quality](#standards)
	- [About Authors](#authors)
	
## <a name="prerequisites"></a>Prerequisites	
This module requires Pimcore 5.x and openssl extension.

**Protip:** Though the bundle doesn't enforce you to do so, it is highly recommended to use HTTPS. 

## <a name="installation"></a>Installation
Create User class in Pimcore:

![Screenshot](doc/images/user-class.png)

Register dependencies in `app/AppKernel.php`:
```php
  public function registerBundlesToCollection(BundleCollection $collection)
    {
        // ...
        if (class_exists('Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle')) {
            $collection->addBundle(new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle());
        }
        if (class_exists('Nelmio\CorsBundle\NelmioCorsBundle')) {
            $collection->addBundle(new Nelmio\CorsBundle\NelmioCorsBundle());
        }
    }
```

Generate the SSH keys :
``` bash
$ mkdir app/config/jwt
$ openssl genrsa -out app/config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in app/config/jwt/private.pem -out config/jwt/public.pem
```

In case first ```openssl``` command forces you to input password use following to get the private key decrypted
``` bash
$ openssl rsa -in app/config/jwt/private.pem -out config/jwt/private2.pem
$ mv app/config/jwt/private.pem config/jwt/private.pem-back
$ mv app/config/jwt/private2.pem config/jwt/private.pem
```

## <a name="configuration"></a>Configuration
Configure the SSH keys path in your `app/config/lexik_jwt_authentication.yaml` :

``` yaml
lexik_jwt_authentication:
    secret_key:       '%kernel.project_dir%/app/config/jwt/private.pem' # required for token creation
    public_key:       '%kernel.project_dir%/app/config/jwt/public.pem'  # required for token verification
    pass_phrase:      'your_secret_passphrase' # required for token creation, usage of an environment variable is recommended
    token_ttl:        3600
```

Configure CORS configuration in `app/config/nelmio_cors.yml` :
``` yaml
nelmio_cors:
  defaults:
    allow_credentials: false
    allow_origin: []
    allow_headers: []
    allow_methods: []
    expose_headers: []
    max_age: 0
    hosts: []
    origin_regex: false
    forced_allow_origin_value: ~
  paths:
    '^/api/':
      allow_origin: ['*']
      allow_headers: ['*']
      allow_methods: ['POST', 'PUT', 'GET', 'DELETE']
      max_age: 3600
``` 


Configure your `app/config/security.yml` :

``` yaml
security:
    # ...
    
   providers:
       pimcore_user_provider:
         id: login_bundle.security.user_provider
     firewalls:
       login:
         pattern: ^/api/login
         stateless: true
         anonymous: true
         provider: pimcore_user_provider
         json_login:
           check_path:               /api/login
           success_handler:          lexik_jwt_authentication.handler.authentication_success
           failure_handler:          lexik_jwt_authentication.handler.authentication_failure
   
       api:
          pattern:   ^/api
          stateless: true
          provider: pimcore_user_provider
          guard:
            authenticators:
              - lexik_jwt_authentication.jwt_token_authenticator
   
     access_control:
       - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
       - { path: ^/api,       roles: IS_AUTHENTICATED_FULLY }
```
Configure yor `app/config/config.yml`:
``` yaml
imports:
    # ...
    - { resource: lexik_jwt_authentication.yml }
    - { resource: nelmio_cors.yml }
```
   
Configure your `config/routing.yml` :

``` yaml
api_login_check:
    path: /api/login
```


## <a name="usage"></a>Usage	
The first step is to authenticate the user using its credentials.
A classical form_login on an anonymously accessible firewall will do perfect.

Just set the provided `lexik_jwt_authentication.handler.authentication_success` service as success handler to
generate the token and send it as part of a json response body.

Store it (client side), the JWT is reusable until its ttl has expired (3600 seconds by default).
Create in Pimcore panel an object of class User and fill its credentials.
 
Note: You can test getting the token with a simple curl command like this:

```bash
curl -X POST -H "Content-Type: application/json" http://localhost/api/login -d '{"username":"admin","password":"test"}'
```

If it works, you will receive something like this:

```json
{
   "token" : "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0MzQ3Mjc1MzYsInVzZXJuYW1lIjoia29ybGVvbiIsImlhdCI6IjE0MzQ2NDExMzYifQ.nh0L_wuJy6ZKIQWh6OrW5hdLkviTs1_bau2GqYdDCB0Yqy_RplkFghsuqMpsFls8zKEErdX5TYCOR7muX0aQvQxGQ4mpBkvMDhJ4-pE4ct2obeMTr_s4X8nC00rBYPofrOONUOR4utbzvbd4d2xT_tj4TdR_0tsr91Y7VskCRFnoXAnNT-qQb7ci7HIBTbutb9zVStOFejrb4aLbr7Fl4byeIEYgp2Gd7gY"
}
```

### Use the token

Simply pass the JWT on each request to the protected firewall, either as an authorization header
or as a query parameter. 

By default only the authorization header mode is enabled : `Authorization: Bearer {token}`

### About token expiration

Each request after token expiration will result in a 401 response.
Redo the authentication process to obtain a new token. 

Maybe you want to use a **refresh token** to renew your JWT. In this case you can check [JWTRefreshTokenBundle](https://github.com/gesdinet/JWTRefreshTokenBundle).

### More details

For more details head to [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md)
 
## <a name="standards"></a>Standards & Code Quality
This module respects PSR-2 code quality rule and our own PHPCS and PHPMD rulesets.

## <a name="authors"></a>About Authors

![Divante-logo](http://divante.co/logo-HG.png "Divante")

We are a Software House from Europe, existing from 2008 and employing about 150 people. Our core competencies are built around Magento, Pimcore and bespoke software projects (we love Symfony3, Node.js, Angular, React, Vue.js). We specialize in sophisticated integration projects trying to connect hardcore IT with good product design and UX.

We work for Clients like INTERSPORT, ING, Odlo, Onderdelenwinkel and CDP, the company that produced The Witcher game. We develop two projects: [Open Loyalty](http://www.openloyalty.io/ "Open Loyalty") - an open source loyalty program and [Vue.js Storefront](https://github.com/DivanteLtd/vue-storefront "Vue.js Storefront").

We are part of the OEX Group which is listed on the Warsaw Stock Exchange. Our annual revenue has been growing at a minimum of about 30% year on year.

Visit our website [Divante.co](https://divante.co/ "Divante.co") for more information.