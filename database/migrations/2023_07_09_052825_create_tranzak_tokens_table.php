<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranzakTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tranzak_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->string('token_type')->default('Bearer');
            $table->enum('product', [
                'collection',
                'disbursement',
            ]);
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tranzak_tokens');
    }
}
