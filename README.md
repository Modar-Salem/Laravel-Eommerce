# Laravel-Eommerce
Online Shop with many features


<hr>

## marketplace provides you the following :

### 1- Product & Product Variation System
 - Open market where every user can add new products for sale, or delete its previous ones.
 - The ability to add multiple images fro the product.
### 2- Categorization system: 
 - The product owner is asked for classifying its products into categories.
 - The User can suggest a new category, and it will be added only after the approval of the Admin.
### 4- Rating system:
 - A User can rate products, on a scale from 1 to 5.

### 5- The ability of selecting favourites.


<hr>

## Installation Steps


### 1. Add the DB Credentials & APP_URL

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=127.0.0.1
DB_DATABASE=store
DB_USERNAME=root
DB_PASSWORD=

```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```
APP_URL=http://localhost
```

### 3. Getting Your Environment Ready.

#### Just Run The following command.

```bash
 php artisan serve
```
