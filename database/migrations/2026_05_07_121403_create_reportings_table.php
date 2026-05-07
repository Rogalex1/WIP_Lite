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
Schema::create('reportings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le TC
    $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
    $table->date('report_date');
    $table->integer('calls_count')->default(0);      // Nombre d'appels
    $table->integer('success_count')->default(0);    // Ventes / RDV pris
    $table->decimal('dmc', 5, 2)->default(0);        // Durée Moyenne de Com (en min)
    $table->text('comment')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportings');
    }
};
