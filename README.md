# NYT API

## Installation



```bash
composer install
```
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
1. find "NYT_API_KEY=" in .env file and add your NYT API Key
2. gather test data by running command:

```bash
php artisan app:get-best-sellers
```

test can be made via api/1/nyt/best-sellers?test=1
