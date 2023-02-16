# web-shop
The following example is the implementation of a simplified mini web shop. It consists of customers, products and orders. The goal is first to import CSV files with some example data and then create an API for orders (CRUD operations + attach product + pay). To successfully pay an order, a micro payment provider has to be invoked by the shop.

<h1>Prerequisites</h1>
<ul>
	<li>PHP 7.2 or later</li>
	<li>Composer</li>
	<li>Laravel</li>
	<li>Guzzle HTTP Client</li>
</ul>

<h1>Installation</h1>
<ul>
	<li><b>Clone the repository:</b> git clone [https://github.com/https://github.com/EshanAnsari/web-shop.git](https://github.com/EshanAnsari/web-shop.git)</li>
	<li><b>Navigate to the project directory:</b> cd web-shop-api</li>
	<li><b>Install the dependencies:</b> composer install</li>
	<li>Create a database and update the database credentials in the .env file</li>
	<li><b>Run the migrations:</b> php artisan migrate</li>
</ul>

<h1>Import Master Data</h1>
This command is used to import the master data from the given URLs into the API Webshop database.
<pre>php artisan import:master-data</pre>

<h1>Logging</h1>
The command logs the import results (how many datasets were imported/not imported, etc.) in the laravel.log file located in the storage/logs directory.

<h1>API Endpoints</h1>
Orders
<pre>
GET /api/orders: Retrieve a list of orders
POST /api/orders: Create a new order
POST /api/orders/{id}/add: Attach a product to an existing order
POST /api/orders/{id}/pay: Submit an order for payment
</pre>
