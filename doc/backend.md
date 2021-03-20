# backend

todo

## generate RSA key

```
mkdir -p backend/config/jwt
openssl genrsa -aes256 4096 > backend/config/jwt/private.pem
```
Type the value of your `JWT_PASSPHRASE` environment variable.
```
openssl rsa -pubout -in private.pem > public.pem
```


