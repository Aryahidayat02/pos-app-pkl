<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('discount_type', 20)->default('amount');
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('void_reason', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('void_reason');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount']);
        });
    }
};
