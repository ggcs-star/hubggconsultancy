<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('highest_qualification')->nullable()->after('city');
            $table->string('institution_name')->nullable()->after('highest_qualification');
            $table->string('field_of_study')->nullable()->after('institution_name');
            $table->string('education_year')->nullable()->after('field_of_study');
            $table->string('pincode')->nullable()->after('education_year');
            $table->string('state')->nullable()->after('pincode');
            $table->string('country')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'highest_qualification',
                'institution_name',
                'field_of_study',
                'education_year',
                'pincode',
                'state',
                'country',
            ]);
        });
    }
};
