<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable(false)->unique();
            $table->string('mobile_number')->nullable();
            $table->string('password')->nullable();
            $table->decimal('wallet_balance', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->string('role', 10)->default('USER')->check('role IN ("USER", "ADMIN")');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint for email + deleted_at combination
            $table->unique(['email', 'deleted_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
