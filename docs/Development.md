# Development

## Symfony
### Database

```shell
php ../../bin/console make:entity
```

```shell
php ../bin/console make:migration
```

```shell
php ../bin/console doctrine:migrations:migrate
```

### User entity
Manually hash a password
```shell
php ../bin/console security:hash-password
``` 

### Cache
```shell
php ../bin/console cache:pool:clear cache.global_clearer
```
