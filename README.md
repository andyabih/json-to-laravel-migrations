# JSON to Laravel Migrations
Simply create a .json file with the schema for your database, and run the artisan command `json:migrate schema.json` to create all the migrations for your project.

**Note:** This package is built to be used to kickstart your Laravel projects, and not to use on something that's already been built.

----

## Installation
You can install this package by running the below composer command:
```
composer require --dev andyabih/json-to-laravel-migrations
```

----

## Creating the JSON schema
Create a `schema.json` file in the root of your project, and use a template like the below:
```json
{
    "posts": {
        "name"   : "string:50|index:50",
        "state"  : "enum:active,inactive|default:active",
        "text"   : "text",
        "slug"   : "string:50|unique",
        "active" : "boolean|default:false",
        "user_id": "foreign|nullable|constrained|onDelete"
    },

    "categories": {
        "name" : "string",
        "image": "string"
    },

    "subcategories": {
        "name"       : "string",
        "category_id": "foreign|constrained"
    }
}
```
The main keys of your JSON represent the table names. Make sure to create them in order in case a table has a relationship with another. In this case, `posts`, `categories`, and `subcategories` are our tables.

Next, for each table, define your columns as keys (so `name`, `state`, `text`, ... in this case), and set their properties.

## Properties
Properties are separated with a pipe (`|`), and the first property should always be the column type. The package supports every column type in Laravel. 

Additional options (such as string length) can be supplied using a colon (`:`), followed by the value of the option. Multiple options can be supplied (for `float`, for example).

## Migrations
Run the above using:
```
php artisan json:migrate schema.json
```

The above schema will create something three different migrations. The `posts` schema will look like:
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50)->index();
			$table->enum("state", ['active', 'inactive'])->default('active');
			$table->text("text");
			$table->string("slug", 50)->unique();
			$table->boolean("active")->default(false);
			$table->foreignId("user_id")->nullable(true)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
```

## Future plans
* Creating pivot tables.
* Creating models (with relationship).
* [Backpack](https://backpackforlaravel.com/) integration. Backpack is my favorite Admin panel, and would love to have something that integrates with them and creates CRUD controllers with fields automatically.
* Open to any suggestions! Whatever idea you have, please let me know.







