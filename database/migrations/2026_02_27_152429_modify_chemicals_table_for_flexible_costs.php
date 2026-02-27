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
        Schema::table('chemicals', function (Blueprint $table) {
            $table->string('type')->default('chemical')->after('service_job_id');
            $table->decimal('quantity', 10, 2)->nullable()->change();
            $table->string('unit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->decimal('quantity', 10, 2)->nullable(false)->change();
            $table->string('unit')->nullable(false)->change();
        });
    }
};
