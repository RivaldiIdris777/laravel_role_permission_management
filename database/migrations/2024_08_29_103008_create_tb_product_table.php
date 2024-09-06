<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('description');
            $table->string('product_code')->nullable();
            $table->timestamps();
        });

        $faker = \Faker\Factory::create();
        for($i=0; $i<10; $i++) {
            Product::create([
                'name' => $faker->word,
                'price' => $faker->randomNumber(3,true),
                'description' => $faker->sentence(5,true),
                'product_code' => $faker->randomNumber(7, true)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_product');
    }
};
