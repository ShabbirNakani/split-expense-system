<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplitExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('split_expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('the user\'s i\'d who were involved in the expence');;
            $table->bigInteger('receiver_id')->comment('the user\'s i\'d who created the expence');
            $table->bigInteger('expense_id');
            $table->bigInteger('group_list_id');
            $table->double('amount', 15, 8)->comment('splited amount');
            $table->enum('status', ['owe', 'pay'])->default('owe');
            $table->enum('is_Settled', ['Settled', 'notSettled'])->default('notSettled');
            $table->softDeletes();
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
        Schema::dropIfExists('split_expenses');
    }
}
