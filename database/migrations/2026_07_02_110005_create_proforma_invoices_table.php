<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('proforma_no', 50)->unique();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('proforma_date');
            $table->date('valid_until')->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->boolean('gm_selected')->default(false);
            $table->text('notes')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proforma_invoices');
    }
}
