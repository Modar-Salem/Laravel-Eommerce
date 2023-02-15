<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name') ;

            $table->unsignedInteger('amount') ;

            $table->unsignedInteger('price') ;

            $table->text('details')->nullable() ;

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade') ;

            $table->foreignId('subcategory_id')->nullable()->constrained('sub_categories')->onDelete('cascade');

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
        Schema::dropIfExists('products');
    }
};
