<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('transaction', 'transactions');

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('Date/Time', 'date_time');
            $table->renameColumn('Type', 'type');
            $table->renameColumn('Sector', 'sector');
            $table->renameColumn('Entry Location', 'entry_location');
            $table->renameColumn('Exit Location', 'exit_location');
            $table->renameColumn('Amount', 'amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->renameColumn('date_time', 'Date/Time');
            $table->renameColumn('type', 'Type');
            $table->renameColumn('sector', 'Sector');
            $table->renameColumn('entry_location', 'Entry Location');
            $table->renameColumn('exit_location', 'Exit Location');
            $table->renameColumn('amount', 'Amount');
        });

        Schema::rename('transactions', 'transaction');
    }
};
