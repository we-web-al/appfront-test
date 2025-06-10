<?php

namespace App\Console\Commands;

use App\Jobs\SendPriceChangeNotification;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Console\Command;

class UpdateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--name=} {--description=} {--price=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product with the specified details';

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
     * @return int
     */
    public function handle()
    {
        $data = [];
        if ($this->option('name')) {
            $data['name'] = $this->option('name');
            if (empty($data['name']) || trim($data['name']) == '') {
                $this->error("Name cannot be empty.");
                return 1;
            }
            if (strlen($data['name']) < 3) {
                $this->error("Name must be at least 3 characters long.");
                return 1;
            }
        }

        if ($this->option('description')) {
            $data['description'] = $this->option('description');
        }
        if ($this->option('price')) {
            $data['price'] = $this->option('price');
        }


        if ( ! empty($data)) {
            $repo = app(ProductRepository::class);

            $id = $this->argument('id');
            $product = $repo->find($id);
            if ( ! $product) {
                $this->error("Product not found.");
                return 1;
            }

            try {
                $repo->update($product, $data);

                $this->info("Product updated successfully.");
            } catch (Exception $e) {
                $this->error("Error updating product: ".$e->getMessage());
            }


        } else {
            $this->info("No changes provided. Product remains unchanged.");
        }

        return 0;
    }
}
