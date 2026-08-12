<?php

namespace Database\Seeders;

use App\Models\SaasProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SaasProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Franchise Builder 360',
                'category' => 'Franchise & Business Expansion',
                'description' => 'Franchise Builder 360 is a complete platform to launch, manage, and scale franchise businesses with centralized control, branding, onboarding, and growth tools.',
            ],
            [
                'name' => 'LocalPulse',
                'category' => 'News & Local Media',
                'description' => 'LocalPulse is your all-in-one platform to build, manage, and monetize your digital presence effortlessly — everything you need, all in one place.',
            ],
            [
                'name' => 'EduCore 360',
                'category' => 'Education',
                'description' => 'EduCore 360 brings your entire institute management to your fingertips on a single platform. Get a personalised app & website for your institute now!',
            ],
            [
                'name' => 'PrepMaster',
                'category' => 'Exam Preparation',
                'description' => "PrepMaster simplifies exam preparation by helping educators create quizzes, evaluate answers, and track every learner's progress from a single, unified platform — saving time and improving outcomes.",
            ],
            [
                'name' => 'EduSphere',
                'category' => 'EdTech',
                'description' => 'Edusphere brings everything you need to manage courses, build your brand, and monetize, all in one seamless platform.',
            ],
            [
                'name' => 'PharmaSphere360',
                'category' => 'Healthcare / Pharma',
                'description' => 'Advanced system for managing pharmaceutical businesses. It helps pharmacies, distributors, and manufacturers handle everything smoothly from stock to sales and compliance making daily operations easier and more efficient.',
            ],
            [
                'name' => 'VendoStream',
                'category' => 'V-Commerce',
                'description' => 'With VendoStream, you can go live, showcase your products, and sell to unlimited buyers online — turning every live stream into a sales machine.',
            ],
            [
                'name' => 'Restaurant Revenue +',
                'category' => 'Retail / FMCG',
                'description' => 'Restaurant Revenue + is a 100% commission-free digital commerce platform that empowers local sellers to sell online with complete control, zero platform fees, and no hidden charges — forever.',
            ],
            [
                'name' => 'Rapid Retail',
                'category' => 'Quick Commerce',
                'description' => 'Rapid Retail lets you delight customers with your products while enabling ultra-fast 10-minute deliveries. Sign up, list your products, and start selling instantly with speed and convenience.',
            ],
            [
                'name' => 'Real Estate +',
                'category' => 'Real Estate',
                'description' => 'Real Estate + is a digital platform designed for realtors and developers to list properties, manage leads, showcase projects, and close deals faster with complete control.',
            ],
        ];

        foreach ($products as $index => $product) {
            SaasProduct::firstOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'description' => $product['description'],
                    'emi_available' => true,
                    'active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
