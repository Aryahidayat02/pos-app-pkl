<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique('name', 'suppliers_name_unique');
            $table->unique('phone', 'suppliers_phone_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email', 'customers_email_unique');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->unique(['sale_id', 'product_id'], 'sale_items_sale_product_unique');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->unique(['purchase_id', 'product_id'], 'purchase_items_purchase_product_unique');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropUnique('purchase_items_purchase_product_unique');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropUnique('sale_items_sale_product_unique');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique('suppliers_phone_unique');
            $table->dropUnique('suppliers_name_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_email_unique');
        });
    }
};
