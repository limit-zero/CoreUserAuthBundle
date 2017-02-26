# ModlrAuthBundle
Implements core authentication services for projects using [as3io/modlr](https://github.com/as3io/modlr)

## Requirements
- You must use or replicate the supplied `core-user` [model](/Resources/config/core-user.yml.dist).
  - You can (optionally) use the supplied `core-account` [model](/Resources/config/core-account.yml.dist).
  
## Installation

Install the package via composer:
```
composer require limit0/modlr-auth-bundle
```

Include the bundle in your `AppKernel.php`:
```php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Limit0\ModlrAuthBundle\Limit0ModlrAuthBundle(),
            // ...
```

## Configuration

### Routing
You will need to import this bundle's routing. To prevent any potential collision issues, be sure to load it before your API is loaded in your application:
```yml

limit0_modlr_auth:
    resource: "@Limit0ModlrAuthBundle/Resources/config/routing.yml"
    
as3_modlr_bundle:
    resource: "@As3ModlrBundle/Resources/config/routing.yml"
    defaults:
        _format: json
# ...
```

### Security
Update `security.yml` configuration (a [template](/Resources/config/security.yml.dist) is available):

Add the `core_user` provider:
```yml
    providers:
        core_user:
            id: modlr_auth_bundle.security.user_provider.core_user
    # ...
```

Add the user encoder:
```yml
    encoders:
        Limit0\Bundle\ModlrAuthBundle\Security\User\CoreUser:
            algorithm: bcrypt
            cost: 13
    # ...
```

There are two authenticators supplied, a stateless API authenticator that uses JWT:
```yml
    firewalls:
        api:
            context: core
            pattern: ^/api\/rest
            provider: core_user
            guard:
                authenticators:
                    - modlr_auth_bundle.security.authenticator.api
```

And one that uses stateful Symfony framework tokens:
```yml
    firewalls:
        manage:
            context: core
            anonymous: ~
            provider: core_user
            guard:
                authenticators:
                    - modlr_auth_bundle.security.authenticator.core_user
            remember_me:
                secret: "%secret%"
                lifetime: 63072000
                name: __modlr-auth
                always_remember_me: true
            logout:
                path: /api/auth/user/destroy
                invalidate_session: false
                success_handler: modlr_auth_bundle.security.logout_success_handler
```

Once you've configured your firewalls, configure your access controlled paths.

To lock out the modlr API (replace `api/rest` with your configured modlr rest api prefix):
```yml
    access_control:
        - { path: ^/api/rest, roles: [ ROLE_ADMIN\USER ] }
```

Or locking down the entire application can be done as well:

```yml
    access_control:
        - { path: ^/, roles: [ ROLE_ADMIN\USER ] }
```
