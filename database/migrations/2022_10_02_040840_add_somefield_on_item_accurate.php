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
        Schema::table('item_accurates', function (Blueprint $table) {
            $table->string('no')->nullable()->after('item_type');
            $table->text('notes')->nullable()->after('no');
            $table->integer('percent_taxable')->nullable()->after('notes');
            $table->decimal('unit_price', 16, 6)->nullable()->after('percent_taxable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_accurates', function (Blueprint $table) {
            $table->dropColumn('no');
            $table->dropColumn('notes');
            $table->dropColumn('percent_taxable');
            $table->dropColumn('unit_price', 16, 6);
        });
    }
};
