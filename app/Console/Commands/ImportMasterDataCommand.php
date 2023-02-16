<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportMasterDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:master-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import master data from CSV files into the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Importing master data started');
        $timestamp = \Carbon\Carbon::now();

        // Import customer data
        $customer_file = Storage::disk('public')->get('customers.csv');
        $customers = $this->getDataFromCsv($customer_file);
        
        $customer_imported = 0;
        $customer_not_imported = 0;

        foreach ($customers as $customer) {
            try {
                $names = explode(' ', $customer['FirstName LastName']);
                $first_name = $names[0];
                $last_name = $names[1];
                $registeredSince = date('Y-m-d', strtotime($customer['registered_since']));

                DB::table('customers')->insert([
                    'job_title' => $customer['Job Title'],
                    'email' => $customer['Email Address'],
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'registered_since' => $registeredSince,
                    'phone' => $customer['phone'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                $customer_imported++;
            } catch (\Exception $e) {
                Log::error('Error while importing customer data: ' . $e->getMessage());
                $customer_not_imported++;
            }
        }

        Log::info('Importing customer data finished, imported: ' . $customer_imported . ', not imported: ' . $customer_not_imported);


        // Import product data
        $product_file = Storage::disk('public')->get('products.csv');
        $products = $this->getDataFromCsv($product_file);
        
        $product_imported = 0;
        $product_not_imported = 0;

        foreach ($products as $product) {
            try {
                DB::table('products')->insert([
                    'product_name' => $product['productname'],
                    'price' => $product['price'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                $product_imported++;
            } catch (\Exception $e) {
                Log::error('Error while importing product data: ' . $e->getMessage());
                $product_not_imported++;
            }
        }

        Log::info('Importing product data finished, imported: ' . $product_imported . ', not imported: ' . $product_not_imported);

        Log::info('Importing master data finished');
    }

    private function getDataFromCsv($csv)
    {
        $lines = explode("\n", $csv);
        $header = str_getcsv(array_shift($lines));
        $data = [];

        foreach ($lines as $line) {
            $data[] = array_combine($header, str_getcsv($line));
        }

        return $data;
    }
}
