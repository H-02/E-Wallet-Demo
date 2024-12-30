<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('type', ['DEPOSIT', 'WITHDRAW']);
            $table->timestamp('transaction_time')->useCurrent();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->boolean('is_user_updated')->default(false);

            // Foreign key reference to users table
            $table->foreign('user_id')->references('id')->onDelete('no action');

            // Unique constraint for user_id and transaction_time
            $table->unique(['user_id', 'transaction_time']);
        });

        // Indexes for the transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('transaction_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
