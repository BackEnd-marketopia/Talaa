<?php

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
        Schema::table('configs', function (Blueprint $table) {
            $table->boolean('discount_enabled')->default(false)->after('whatsapp');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('discount_enabled');
            $table->decimal('discount_value', 5, 2)->default(0.00)->after('discount_type');
            $table->decimal('min_order_total_for_discount', 8, 2)->default(0.00)->after('discount_value');
            $table->text('discount_description')->nullable()->after('discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('discount_enabled');
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_value');
            $table->dropColumn('discount_description');
        });
    }
};
