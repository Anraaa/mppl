# Bikin Model, Migrasi, Seeder

php artisan make:model Client -ms

php artisan make:model Product -ms

php artisan make:controlller Api/ProductApiController

php artisan make:middleware ClientAuth

# Migrasi

## Client 

tambahin 
```mysql
$table->string('name');
$table->string('api_token')->unique();
```


## Product

tambahin
```
$table->foreignId('client_id')->constrained()->onDelete('cascade');
$table->string('name');
$table->decimal('price', 10, 2);
```

## s

