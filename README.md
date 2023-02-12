# web-shop
The following example is the implementation of a simplified mini web shop. It consists of customers, products and orders. The goal is first to import CSV files with some example data and then create an API for orders (CRUD operations + attach product + pay). To successfully pay an order, a micro payment provider has to be invoked by the shop.

Prerequisites
PHP 7.2 or later
Composer
Laravel
Guzzle HTTP Client

Installation
Clone the repository: git clone [https://github.com/https://github.com/EshanAnsari/web-shop.git](https://github.com/EshanAnsari/web-shop.git)
Navigate to the project directory: cd web-shop-api
Install the dependencies: composer install
Create a database and update the database credentials in the .env file
Run the migrations: php artisan migrate

Import Master Data
This command is used to import the master data from the given URLs into the API Webshop database.
php artisan import:master-data

Logging
The command logs the import results (how many datasets were imported/not imported, etc.) in the laravel.log file located in the storage/logs directory.

API Endpoints
Orders
GET /api/orders: Retrieve a list of orders
POST /api/orders: Create a new order
POST /api/orders/{id}/add: Attach a product to an existing order
POST /api/orders/{id}/pay: Submit an order for payment
