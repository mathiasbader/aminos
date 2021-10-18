# Study the 20 proteinogetic amino acids

## Symfony
###Database

`php bin/console make:entity`

`php bin/console make:migration`

`php bin/console doctrine:migrations:migrate`

### User entity
`php bin/console security:hash-password` Manually hash a password

### Cache
`php bin/console cache:pool:clear cache.global_clearer`
