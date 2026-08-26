<?php

namespace Database\Seeders;

use App\Models\SaasProduct;
use App\Models\SupportIssueType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SaasProductSupportIssueSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SaaS Products
        |--------------------------------------------------------------------------
        */

        $products = [

            [
                'name' => 'LocalPulse',
                'slug' => 'localpulse',
                'category' => 'Local Business Management',
                'description' =>
                    'Local business management and customer engagement platform.',
                'active' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'PharmaSphere',
                'slug' => 'pharmasphere',
                'category' => 'Pharma ERP',
                'description' =>
                    'Pharmaceutical business management, inventory and operations platform.',
                'active' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'PrepMaster',
                'slug' => 'prepmaster',
                'category' => 'Education & Exam Preparation',
                'description' =>
                    'Online learning and exam preparation platform.',
                'active' => true,
                'sort_order' => 3,
            ],

            [
                'name' => 'FranchiseBuilder',
                'slug' => 'franchisebuilder',
                'category' => 'Franchise Management',
                'description' =>
                    'Franchise business management and expansion platform.',
                'active' => true,
                'sort_order' => 4,
            ],

            [
                'name' => 'Other',
                'slug' => 'other',
                'category' => 'General Support',
                'description' =>
                    'For anything that does not fall under one of the products above — always available to every user as a fallback.',
                'active' => true,
                'sort_order' => 999,
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Create / Update Products
        |--------------------------------------------------------------------------
        */

        $createdProducts = [];

        foreach ($products as $productData) {

            $product = SaasProduct::updateOrCreate(
                [
                    'slug' => $productData['slug'],
                ],
                $productData
            );

            $createdProducts[$product->slug] = $product;
        }


        /*
        |--------------------------------------------------------------------------
        | Support Issue Types
        |--------------------------------------------------------------------------
        */

        $issues = [

            /*
            |--------------------------------------------------------------------------
            | LocalPulse
            |--------------------------------------------------------------------------
            */

            'localpulse' => [

                [
                    'name' => 'Login Issue',
                    'module' => 'authentication',
                    'default_priority' => 'high',
                    'icon' => 'lock',
                    'description' =>
                        'Unable to log in to the LocalPulse account or authentication fails.',
                    'sort_order' => 1,
                ],

                [
                    'name' => 'Business Profile Issue',
                    'module' => 'business_profile',
                    'default_priority' => 'medium',
                    'icon' => 'building',
                    'description' =>
                        'Problems updating business information, address, phone number or business details.',
                    'sort_order' => 2,
                ],

                [
                    'name' => 'Location Not Showing',
                    'module' => 'locations',
                    'default_priority' => 'high',
                    'icon' => 'map-pin',
                    'description' =>
                        'A business location is missing, incorrect or not appearing correctly.',
                    'sort_order' => 3,
                ],

                [
                    'name' => 'Review Management Issue',
                    'module' => 'reviews',
                    'default_priority' => 'medium',
                    'icon' => 'star',
                    'description' =>
                        'Issues with viewing, responding to or managing customer reviews.',
                    'sort_order' => 4,
                ],

                [
                    'name' => 'Notification Issue',
                    'module' => 'notifications',
                    'default_priority' => 'medium',
                    'icon' => 'bell',
                    'description' =>
                        'Expected notifications are not being received or notifications are delayed.',
                    'sort_order' => 5,
                ],

                [
                    'name' => 'Dashboard Issue',
                    'module' => 'dashboard',
                    'default_priority' => 'medium',
                    'icon' => 'grid',
                    'description' =>
                        'Dashboard widgets, statistics or business insights are not displaying correctly.',
                    'sort_order' => 6,
                ],

                [
                    'name' => 'Data Sync Issue',
                    'module' => 'data_sync',
                    'default_priority' => 'high',
                    'icon' => 'refresh',
                    'description' =>
                        'Business information or connected platform data is not synchronizing correctly.',
                    'sort_order' => 7,
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | PharmaSphere
            |--------------------------------------------------------------------------
            */

            'pharmasphere' => [

                [
                    'name' => 'Login Issue',
                    'module' => 'authentication',
                    'default_priority' => 'high',
                    'icon' => 'lock',
                    'description' =>
                        'Unable to access the PharmaSphere account or authentication is failing.',
                    'sort_order' => 1,
                ],

                [
                    'name' => 'Product Management Issue',
                    'module' => 'products',
                    'default_priority' => 'high',
                    'icon' => 'cube',
                    'description' =>
                        'Problems creating, editing, viewing or managing pharmaceutical products.',
                    'sort_order' => 2,
                ],

                [
                    'name' => 'Inventory Issue',
                    'module' => 'inventory',
                    'default_priority' => 'urgent',
                    'icon' => 'archive',
                    'description' =>
                        'Stock quantity, inventory calculation or stock synchronization problems.',
                    'sort_order' => 3,
                ],

                [
                    'name' => 'Order Issue',
                    'module' => 'orders',
                    'default_priority' => 'high',
                    'icon' => 'shopping-cart',
                    'description' =>
                        'Problems with creating, processing, updating or completing orders.',
                    'sort_order' => 4,
                ],

                [
                    'name' => 'Payment Issue',
                    'module' => 'payments',
                    'default_priority' => 'urgent',
                    'icon' => 'credit-card',
                    'description' =>
                        'Payment failures, transaction problems or payment status discrepancies.',
                    'sort_order' => 5,
                ],

                [
                    'name' => 'Prescription Issue',
                    'module' => 'prescriptions',
                    'default_priority' => 'high',
                    'icon' => 'clipboard',
                    'description' =>
                        'Problems uploading, viewing or processing customer prescriptions.',
                    'sort_order' => 6,
                ],

                [
                    'name' => 'Purchase Order Issue',
                    'module' => 'purchase_orders',
                    'default_priority' => 'medium',
                    'icon' => 'document',
                    'description' =>
                        'Issues with creating, editing or processing purchase orders.',
                    'sort_order' => 7,
                ],

                [
                    'name' => 'Report Issue',
                    'module' => 'reports',
                    'default_priority' => 'medium',
                    'icon' => 'chart',
                    'description' =>
                        'Reports are missing, incorrect or not generating as expected.',
                    'sort_order' => 8,
                ],

                [
                    'name' => 'API Integration Issue',
                    'module' => 'api',
                    'default_priority' => 'high',
                    'icon' => 'code',
                    'description' =>
                        'Problems with API requests, integrations or external system synchronization.',
                    'sort_order' => 9,
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | PrepMaster
            |--------------------------------------------------------------------------
            */

            'prepmaster' => [

                [
                    'name' => 'Login Issue',
                    'module' => 'authentication',
                    'default_priority' => 'high',
                    'icon' => 'lock',
                    'description' =>
                        'Unable to log in to the PrepMaster account.',
                    'sort_order' => 1,
                ],

                [
                    'name' => 'Course Access Issue',
                    'module' => 'courses',
                    'default_priority' => 'high',
                    'icon' => 'academic-cap',
                    'description' =>
                        'Purchased or assigned courses are not appearing or cannot be opened.',
                    'sort_order' => 2,
                ],

                [
                    'name' => 'Video Playback Issue',
                    'module' => 'videos',
                    'default_priority' => 'high',
                    'icon' => 'play',
                    'description' =>
                        'Course videos are not loading, playing or completing correctly.',
                    'sort_order' => 3,
                ],

                [
                    'name' => 'Quiz Issue',
                    'module' => 'quizzes',
                    'default_priority' => 'medium',
                    'icon' => 'check-circle',
                    'description' =>
                        'Problems starting quizzes, submitting answers or viewing quiz results.',
                    'sort_order' => 4,
                ],

                [
                    'name' => 'Certificate Issue',
                    'module' => 'certificates',
                    'default_priority' => 'medium',
                    'icon' => 'badge',
                    'description' =>
                        'Certificate is not generated, displayed or downloadable after course completion.',
                    'sort_order' => 5,
                ],

                [
                    'name' => 'Progress Tracking Issue',
                    'module' => 'progress',
                    'default_priority' => 'medium',
                    'icon' => 'chart',
                    'description' =>
                        'Course progress, lesson completion or learning statistics are incorrect.',
                    'sort_order' => 6,
                ],

                [
                    'name' => 'Study Material Issue',
                    'module' => 'study_material',
                    'default_priority' => 'medium',
                    'icon' => 'document',
                    'description' =>
                        'Study materials, PDFs or downloadable resources are missing or inaccessible.',
                    'sort_order' => 7,
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | FranchiseBuilder
            |--------------------------------------------------------------------------
            */

            'franchisebuilder' => [

                [
                    'name' => 'Login Issue',
                    'module' => 'authentication',
                    'default_priority' => 'high',
                    'icon' => 'lock',
                    'description' =>
                        'Unable to log in to the FranchiseBuilder account.',
                    'sort_order' => 1,
                ],

                [
                    'name' => 'Franchise Profile Issue',
                    'module' => 'franchise_profile',
                    'default_priority' => 'medium',
                    'icon' => 'building',
                    'description' =>
                        'Problems creating or updating franchise business information.',
                    'sort_order' => 2,
                ],

                [
                    'name' => 'Lead Management Issue',
                    'module' => 'leads',
                    'default_priority' => 'high',
                    'icon' => 'users',
                    'description' =>
                        'Problems creating, assigning, updating or managing franchise leads.',
                    'sort_order' => 3,
                ],

                [
                    'name' => 'Franchise Application Issue',
                    'module' => 'applications',
                    'default_priority' => 'high',
                    'icon' => 'document',
                    'description' =>
                        'Issues with franchise applications, application status or submitted information.',
                    'sort_order' => 4,
                ],

                [
                    'name' => 'Document Upload Issue',
                    'module' => 'documents',
                    'default_priority' => 'medium',
                    'icon' => 'upload',
                    'description' =>
                        'Problems uploading, viewing or downloading franchise documents.',
                    'sort_order' => 5,
                ],

                [
                    'name' => 'Payment Issue',
                    'module' => 'payments',
                    'default_priority' => 'urgent',
                    'icon' => 'credit-card',
                    'description' =>
                        'Payment failures, transaction issues or incorrect payment status.',
                    'sort_order' => 6,
                ],

                [
                    'name' => 'Analytics Issue',
                    'module' => 'analytics',
                    'default_priority' => 'medium',
                    'icon' => 'chart',
                    'description' =>
                        'Dashboard statistics, reports or franchise analytics are incorrect.',
                    'sort_order' => 7,
                ],

                [
                    'name' => 'Notification Issue',
                    'module' => 'notifications',
                    'default_priority' => 'medium',
                    'icon' => 'bell',
                    'description' =>
                        'Important franchise or lead notifications are not being delivered.',
                    'sort_order' => 8,
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | Other (generic fallback, not tied to a specific product)
            |--------------------------------------------------------------------------
            */

            'other' => [

                [
                    'name' => 'General Inquiry',
                    'module' => 'system',
                    'default_priority' => 'low',
                    'icon' => 'help-circle',
                    'description' =>
                        'A general question that does not fit any specific product.',
                    'sort_order' => 1,
                ],

                [
                    'name' => 'Account / Login Issue',
                    'module' => 'authentication',
                    'default_priority' => 'high',
                    'icon' => 'lock',
                    'description' =>
                        'Trouble logging in or managing your account that is not specific to one product.',
                    'sort_order' => 2,
                ],

                [
                    'name' => 'Billing / Payment Issue',
                    'module' => 'payment',
                    'default_priority' => 'high',
                    'icon' => 'coin',
                    'description' =>
                        'A billing, invoice or payment problem not tied to a specific product.',
                    'sort_order' => 3,
                ],

                [
                    'name' => 'Something Else',
                    'module' => 'system',
                    'default_priority' => 'medium',
                    'icon' => 'help-circle',
                    'description' =>
                        'None of the above — describe your issue in the details field below.',
                    'sort_order' => 4,
                ],

            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Create Issue Types
        |--------------------------------------------------------------------------
        */

        foreach ($issues as $productSlug => $productIssues) {

            $product = $createdProducts[$productSlug] ?? null;

            if (!$product) {
                continue;
            }


            foreach ($productIssues as $issue) {

                /*
                |--------------------------------------------------------------------------
                | Globally Unique Slug
                |--------------------------------------------------------------------------
                */

                $slug = Str::slug(
                    $product->slug . '-' . $issue['name']
                );


                SupportIssueType::updateOrCreate(
                    [
                        'slug' => $slug,
                    ],
                    [
                        'saas_product_id' =>
                            $product->id,

                        'name' =>
                            $issue['name'],

                        'slug' =>
                            $slug,

                        'module' =>
                            $issue['module'],

                        'default_priority' =>
                            $issue['default_priority'],

                        'icon' =>
                            $issue['icon'],

                        'description' =>
                            $issue['description'],

                        'sort_order' =>
                            $issue['sort_order'],

                        'status' =>
                            true,
                    ]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Attach "Other" To Every User
        |--------------------------------------------------------------------------
        |
        | Unlike the product-specific rows above, "Other" is meant to always
        | be available as a support fallback — attach it to every existing
        | user now; new users get it automatically via User::booted().
        */

        $otherProduct = $createdProducts['other'] ?? null;

        if ($otherProduct) {
            $otherProduct->users()->syncWithoutDetaching(
                \App\Models\User::pluck('id')
            );
        }


        $this->command?->info(
            'SaaS products and support issue types seeded successfully.'
        );
    }
}