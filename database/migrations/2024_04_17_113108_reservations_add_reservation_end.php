<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('reservation_end')->nullable();
            $table->renameColumn('reservation_time', 'reservation_start');
        });

        DB::statement(
            "UPDATE reservations 
             SET reservation_end = reservation_start + (duration * interval '1 minute')"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reservation_end');
            $table->renameColumn('reservation_start', 'reservation_time');
        });
    }
};
