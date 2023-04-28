## Pet Shop API
A simple Pet Shop API built on top Laravel following TDD.

### Fulfilling of requirements
I have tried to fulfil the following requirements:

- The Must
- The Recommended

### Installation

Please follow the commands below to make the API up and run. First clone the 
repo with the command below and `cd` to `pet-shop-api` dir:

```bash
git clone https://github.com/unclexo/pet-shop-api.git

cd pet-shop-api
```

Create private and public keys to be used for aysmmetric algorithm to 
generate JWT token.

```bash
mkdir -p ./storage/app/token

openssl genrsa -out ./storage/app/token/jwt-token-private.key

openssl rsa -in ./storage/app/token/jwt-token-private.key \ 
-pubout -out ./storage/app/token/jwt-token-public.key
``` 

Set environment variables for database, and private and public key files for 
JWT token:
```bash
DB_CONNECTION=mysql
DB_HOST=xo-pet-api-db
DB_PORT=3306
DB_DATABASE=pet_shop_db
DB_USERNAME=root
DB_PASSWORD=root
```

```bash
# Path is relative to storage/app dir
JWT_TOKEN_PRIVATE_KEY_PATH=token/jwt-token-private.key
JWT_TOKEN_PUBLIC_KEY_PATH=token/jwt-token-public.key
```
You may change these the way you need.

Now you can run docker containers by the following command:

```bash
docker compose up -d --build
```

To make sure containers are running well, run the following command:

```bash
docker container list
```

Once the containers are up and running you can install laravel dependencies. 
To do that run the command below:

```bash
docker exec -it xo-pet-api-app composer install
```

When laravel dependencies are installed, you can make migrations of db tables 
and seed tables with dummy data using the following command. 

```bash
docker exec -it xo-pet-api-app php artisan migrate:fresh --force --seed
```

### Open the application
The application runs on port `8000` as it exposed by docker. So open your 
browser and hit the following URL:

```bash
http://localhost:8000
```

### API docs
The Pet Shop API is available at the following URL:

```bash
http://localhost:8000/docs/v1
```

### Test
To run all unit and feature tests run the following command please:

```bash
php artisan test

// or

./vendor/bin/phpunit
```
