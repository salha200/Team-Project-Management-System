<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('task_user', function (Blueprint $table) {
            $table->string('role')->nullable(); // أو `->default('some_default_value')` إذا كان لديك قيمة افتراضية
        });
    }
    
    public function down()
    {
        Schema::table('task_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
    
};