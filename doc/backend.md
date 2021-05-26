# backend

## Configuration

Create backend/.env.local file based on .env file.
Make it correspond to your deployment/docker/.env config.

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

## Lint

```
# PHP CS Fixer
compose cs

# PHP CS Fixer autofix
compose csf

# PHPStan
composer ps
```

## Testing

```
# Unit tests
composer tu

# Unit tests with coverage
composer tuc
```

## Prod

Generate your first user with this command:
```
php bin/console app:user:create -a first_user first_user@mp3000.fr first_pa$$word3000 
```
