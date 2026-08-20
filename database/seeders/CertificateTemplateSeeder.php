<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            /*
            |--------------------------------------------------------------------------
            | 1. Classic Blue
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Classic Blue',
                'slug' => 'classic-blue',
                'design_type' => 'classic-blue',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#173B6C',
                    'secondary_color' => '#2E5B8C',
                    'accent_color' => '#F59E0B',
                    'background_color' => '#FFFFFF',

                    'font_family' => 'Georgia',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 58,
                        'top' => 13,
                    ],

                    'subtitle' => [
                        'text' => 'OF COMPLETION',
                        'font_size' => 28,
                        'top' => 23,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 31,
                    ],

                    'name' => [
                        'font_size' => 46,
                        'top' => 48,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 61,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 82,
                        'left' => 32,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 82,
                        'left' => 68,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 1,
            ],


            /*
            |--------------------------------------------------------------------------
            | 2. Premium Gold
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Premium Gold',
                'slug' => 'premium-gold',
                'design_type' => 'premium-gold',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#6B4F12',
                    'secondary_color' => '#8B6914',
                    'accent_color' => '#D4AF37',
                    'background_color' => '#FFFDF5',

                    'font_family' => 'Georgia',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 58,
                        'top' => 14,
                    ],

                    'subtitle' => [
                        'text' => 'OF ACHIEVEMENT',
                        'font_size' => 27,
                        'top' => 24,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 32,
                    ],

                    'name' => [
                        'font_size' => 48,
                        'top' => 49,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 62,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 2,
            ],


            /*
            |--------------------------------------------------------------------------
            | 3. Modern Green
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Modern Green',
                'slug' => 'modern-green',
                'design_type' => 'modern-green',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#14532D',
                    'secondary_color' => '#166534',
                    'accent_color' => '#22C55E',
                    'background_color' => '#F7FFF9',

                    'font_family' => 'Arial',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 56,
                        'top' => 14,
                    ],

                    'subtitle' => [
                        'text' => 'OF COMPLETION',
                        'font_size' => 27,
                        'top' => 24,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 32,
                    ],

                    'name' => [
                        'font_size' => 45,
                        'top' => 48,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 61,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 3,
            ],


            /*
            |--------------------------------------------------------------------------
            | 4. Dark Premium
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Dark Premium',
                'slug' => 'dark-premium',
                'design_type' => 'dark-premium',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#FFFFFF',
                    'secondary_color' => '#E5E7EB',
                    'accent_color' => '#A855F7',
                    'background_color' => '#111827',

                    'font_family' => 'Arial',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 58,
                        'top' => 15,
                    ],

                    'subtitle' => [
                        'text' => 'OF EXCELLENCE',
                        'font_size' => 27,
                        'top' => 25,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 33,
                    ],

                    'name' => [
                        'font_size' => 47,
                        'top' => 49,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 62,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 4,
            ],


            /*
            |--------------------------------------------------------------------------
            | 5. Minimal White
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Minimal White',
                'slug' => 'minimal-white',
                'design_type' => 'minimal-white',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#111827',
                    'secondary_color' => '#4B5563',
                    'accent_color' => '#6366F1',
                    'background_color' => '#FFFFFF',

                    'font_family' => 'Arial',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 56,
                        'top' => 18,
                    ],

                    'subtitle' => [
                        'text' => 'OF COMPLETION',
                        'font_size' => 26,
                        'top' => 27,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 35,
                    ],

                    'name' => [
                        'font_size' => 46,
                        'top' => 50,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 62,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 5,
            ],


            /*
            |--------------------------------------------------------------------------
            | 6. Academic
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Academic',
                'slug' => 'academic',
                'design_type' => 'academic',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#1E3A8A',
                    'secondary_color' => '#334155',
                    'accent_color' => '#CA8A04',
                    'background_color' => '#FFFEF8',

                    'font_family' => 'Georgia',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 56,
                        'top' => 15,
                    ],

                    'subtitle' => [
                        'text' => 'OF COMPLETION',
                        'font_size' => 28,
                        'top' => 25,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 33,
                    ],

                    'name' => [
                        'font_size' => 45,
                        'top' => 50,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 62,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 6,
            ],


            /*
            |--------------------------------------------------------------------------
            | 7. Modern Gradient
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Modern Gradient',
                'slug' => 'modern-gradient',
                'design_type' => 'modern-gradient',

                'preview_image' => null,
                'background_image' => null,
                'signature_image' => null,

                'signer_name' => 'Anuj Singh',
                'signer_designation' => 'Director',
                'organization_name' => 'GG Hub',

                'settings' => [
                    'orientation' => 'landscape',

                    'primary_color' => '#4C1D95',
                    'secondary_color' => '#6D28D9',
                    'accent_color' => '#EC4899',
                    'background_color' => '#FAF5FF',

                    'font_family' => 'Arial',

                    'title' => [
                        'text' => 'CERTIFICATE',
                        'font_size' => 56,
                        'top' => 15,
                    ],

                    'subtitle' => [
                        'text' => 'OF COMPLETION',
                        'font_size' => 27,
                        'top' => 25,
                    ],

                    'course' => [
                        'font_size' => 20,
                        'top' => 33,
                    ],

                    'name' => [
                        'font_size' => 46,
                        'top' => 49,
                    ],

                    'description' => [
                        'font_size' => 18,
                        'top' => 62,
                    ],

                    'date' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 30,
                    ],

                    'certificate_id' => [
                        'font_size' => 15,
                        'top' => 83,
                        'left' => 70,
                    ],

                    'signature' => [
                        'right' => 12,
                        'bottom' => 8,
                    ],
                ],

                'is_active' => true,
                'sort_order' => 7,
            ],
        ];


        foreach ($templates as $template) {

            CertificateTemplate::updateOrCreate(
                [
                    'slug' => $template['slug'],
                ],
                $template
            );
        }


        $this->command?->info(
            '7 certificate templates seeded successfully.'
        );
    }
}