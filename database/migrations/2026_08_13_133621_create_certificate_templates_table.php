<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('slug')->unique();

            /*
            |--------------------------------------------------------------------------
            | Template Design
            |--------------------------------------------------------------------------
            |
            | classic-blue
            | premium-gold
            | modern-green
            | dark-premium
            | minimal-white
            | academic
            | modern-gradient
            |
            */

            $table->string('design_type');

            /*
            |--------------------------------------------------------------------------
            | Preview / Background
            |--------------------------------------------------------------------------
            */

            $table->string('preview_image')->nullable();

            $table->string('background_image')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Signature
            |--------------------------------------------------------------------------
            */

            $table->string('signature_image')->nullable();

            $table->string('signer_name')->nullable();

            $table->string('signer_designation')->nullable();

            $table->string('organization_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Design Settings
            |--------------------------------------------------------------------------
            |
            | Dynamic positions, fonts, colors etc.
            |
            */

            $table->json('settings')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};