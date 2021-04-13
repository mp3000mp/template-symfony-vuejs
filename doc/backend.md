# backend

todo

## Generate RSA key

```
mkdir -p backend/config/jwt
openssl genrsa -aes256 4096 > backend/config/jwt/private.pem
```
Type the value of your `JWT_PASSPHRASE` environment variable.
```
 openssl rsa -pubout -in backend/config/jwt/private.pem > backend/config/jwt/public.pem
```

## Init dummy data

```
# fixtures 
php bin/console doctrine:fixtures:load --group=dev
```
or
```
php bin/console doctrine:fixtures:load --group=test
```

## Testing

```
composer tu
```
