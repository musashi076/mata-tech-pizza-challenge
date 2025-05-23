<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PizzaType;
use App\Models\Pizza;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;

class CSVImportSeeder extends Seeder
{
    public function run()
    {
        $this->importPizzaTypes();
        $this->importPizzas();
        $this->importOrders();
        $this->importOrderDetails();
    }

    protected function importCsv($filename)
    {
        $path = storage_path('app/' . $filename);

        if (!file_exists($path)) {
            $this->command->error("File {$filename} not found");
            return null;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $rows = [];
        while (($row = fgetcsv($file)) !== false) {
            $row = array_map(function ($field) {
                return mb_convert_encoding($field, 'UTF-8', 'auto');
            }, $row);
            $rows[] = array_combine($header, $row);
        }

        fclose($file);

        return $rows;
    }

    protected function importPizzaTypes()
    {
        $records = $this->importCsv('pizza_types.csv');
        if (!$records) return;

        $totalRecords = count($records);
        $this->command->info("Importing $totalRecords orders...");

        $progressBar = $this->command->getOutput()->createProgressBar($totalRecords);
        $progressBar->start();

        foreach ($records as $record) {
            PizzaType::updateOrCreate(
                ['pizza_type_id' => $record['pizza_type_id']],
                [
                    'name' => $record['name'],
                    'category' => $record['category'],
                    'ingredients' => $record['ingredients'],
                ]
            );
            
            $progressBar->advance();
        }

        
        $progressBar->finish();
        $this->command->info('Pizza types imported.');
    }

    protected function importPizzas()
    {
        $records = $this->importCsv('pizzas.csv');
        if (!$records) return;
        $totalRecords = count($records);
        $this->command->info("Importing $totalRecords orders...");

        $progressBar = $this->command->getOutput()->createProgressBar($totalRecords);
        $progressBar->start();

        foreach ($records as $record) {
            Pizza::updateOrCreate(
                ['pizza_id' => $record['pizza_id']],
                [
                    'pizza_type_id' => $record['pizza_type_id'],
                    'size' => $record['size'],
                    'price' => $record['price'],
                ]
            );
            
            $progressBar->advance();
        }

        
        $progressBar->finish();
        $this->command->info('Pizzas imported.');
    }

    protected function importOrders()
    {
        $records = $this->importCsv('orders.csv');
        if (!$records) return;

        $totalRecords = count($records);
        $this->command->info("Importing $totalRecords orders...");

        $progressBar = $this->command->getOutput()->createProgressBar($totalRecords);
        $progressBar->start();

        foreach ($records as $record) {
            $orderDateTime = Carbon::parse($record['date'] . ' ' . $record['time']);

            Order::updateOrCreate(
                ['order_id' => $record['order_id']],
                [
                    'order_date' => $orderDateTime, // Use the new merged column
                ]
            );
            $progressBar->advance();
        }

        
        $progressBar->finish();
        $this->command->info('Orders imported.');
    }

    protected function importOrderDetails()
    {
        $records = $this->importCsv('order_details.csv');
        if (!$records) return;

        $totalRecords = count($records);
        $this->command->info("Importing $totalRecords orders...");

        $progressBar = $this->command->getOutput()->createProgressBar($totalRecords);
        $progressBar->start();
        
        foreach ($records as $record) {
            OrderDetail::updateOrCreate(
                ['order_details_id' => $record['order_details_id']],
                [
                    'order_id' => $record['order_id'],
                    'pizza_id' => $record['pizza_id'],
                    'quantity' => $record['quantity'],
                ]
            );
            $progressBar->advance();
        }

        
        $progressBar->finish();
        $this->command->info('Order details imported.');
    }
}
