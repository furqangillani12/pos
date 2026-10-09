<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Daily duty hours per employee (salary is paid per minute of this). Guarded. */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'duty_hours')) {
            Schema::table('employees', fn (Blueprint $t) => $t->decimal('duty_hours', 4, 2)->default(12)->after('salary'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'duty_hours')) {
            Schema::table('employees', fn (Blueprint $t) => $t->dropColumn('duty_hours'));
        }
    }
};
