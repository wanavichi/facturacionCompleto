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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que crea la factura
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0)->after('user_id');
            $table->decimal('descuento', 10, 2)->default(0)->after('subtotal');
            $table->decimal('iva', 10, 2)->default(0)->after('descuento');
            $table->boolean('anulada')->default(false);
            $table->boolean('pagada')->default(false);  // <--- Aquí
            $table->enum('estado', ['pendiente', 'aprobado', 'anulada'])->default('pendiente');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
