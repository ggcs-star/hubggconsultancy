<?php

namespace Database\Seeders;

use App\Models\SaasProduct;
use App\Models\SupportIssueType;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Demo User
        |--------------------------------------------------------------------------
        */

        $user = User::where(
            'email',
            'user@presalesschool.test'
        )->first();

        if (!$user) {
            $this->command?->warn(
                'User user@presalesschool.test not found. Seeder stopped.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        |
        | support_ticket_messages.user_id is NOT NULL,
        | therefore admin replies also need a real user ID.
        |
        */

        $admin = User::where(
            'email',
            'admin@presalesschool.test'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = SaasProduct::whereIn(
            'slug',
            [
                'localpulse',
                'pharmasphere',
                'prepmaster',
                'franchisebuilder',
            ]
        )
            ->where('active', true)
            ->get()
            ->keyBy('slug');


        if ($products->isEmpty()) {
            $this->command?->warn(
                'No SaaS products found. Run SaasProductSupportIssueSeeder first.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Assign Products To User
        |--------------------------------------------------------------------------
        */

        foreach ($products as $product) {

            DB::table('saas_product_user')->updateOrInsert(
                [
                    'user_id' =>
                        $user->id,

                    'saas_product_id' =>
                        $product->id,
                ],
                [
                    'updated_at' =>
                        now(),

                    'created_at' =>
                        now(),
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Demo Tickets
        |--------------------------------------------------------------------------
        */

        $ticketData = [

            /*
            |--------------------------------------------------------------------------
            | LocalPulse
            |--------------------------------------------------------------------------
            */

            [
                'product_slug' => 'localpulse',
                'issue_name' => 'Login Issue',
                'priority' => 'high',
                'status' => 'open',
                'description' =>
                    'I am unable to log in to my LocalPulse account. The login page keeps returning an authentication error even though the credentials are correct.',
            ],

            [
                'product_slug' => 'localpulse',
                'issue_name' => 'Review Management Issue',
                'priority' => 'medium',
                'status' => 'in_progress',
                'description' =>
                    'Customer reviews are not appearing in the LocalPulse review management section. Please check the review synchronization.',
            ],


            /*
            |--------------------------------------------------------------------------
            | PharmaSphere
            |--------------------------------------------------------------------------
            */

            [
                'product_slug' => 'pharmasphere',
                'issue_name' => 'Inventory Issue',
                'priority' => 'urgent',
                'status' => 'open',
                'description' =>
                    'The current inventory quantity is not matching the actual available stock. Please check the inventory calculation and synchronization.',
            ],

            [
                'product_slug' => 'pharmasphere',
                'issue_name' => 'Prescription Issue',
                'priority' => 'high',
                'status' => 'resolved',
                'description' =>
                    'A customer prescription was uploaded successfully but was not appearing in the prescription management section.',
            ],


            /*
            |--------------------------------------------------------------------------
            | PrepMaster
            |--------------------------------------------------------------------------
            */

            [
                'product_slug' => 'prepmaster',
                'issue_name' => 'Video Playback Issue',
                'priority' => 'high',
                'status' => 'in_progress',
                'description' =>
                    'The lesson video starts loading but stops after a few seconds. I have tried refreshing the page and using another browser.',
            ],

            [
                'product_slug' => 'prepmaster',
                'issue_name' => 'Certificate Issue',
                'priority' => 'medium',
                'status' => 'open',
                'description' =>
                    'I completed the required course lessons but the certificate is not appearing in my certificates section.',
            ],


            /*
            |--------------------------------------------------------------------------
            | FranchiseBuilder
            |--------------------------------------------------------------------------
            */

            [
                'product_slug' => 'franchisebuilder',
                'issue_name' => 'Lead Management Issue',
                'priority' => 'high',
                'status' => 'open',
                'description' =>
                    'A newly created franchise lead is not appearing in the lead management list after submission.',
            ],

            [
                'product_slug' => 'franchisebuilder',
                'issue_name' => 'Document Upload Issue',
                'priority' => 'medium',
                'status' => 'resolved',
                'description' =>
                    'The franchise agreement document was uploaded successfully but the document preview was not loading.',
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Create Tickets
        |--------------------------------------------------------------------------
        */

        foreach ($ticketData as $data) {

            $product = $products->get(
                $data['product_slug']
            );

            if (!$product) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Product-Specific Issue
            |--------------------------------------------------------------------------
            */

            $issueType = SupportIssueType::query()
                ->where(
                    'saas_product_id',
                    $product->id
                )
                ->where(
                    'name',
                    $data['issue_name']
                )
                ->where(
                    'status',
                    true
                )
                ->first();


            if (!$issueType) {

                $this->command?->warn(
                    "Issue '{$data['issue_name']}' not found for {$product->name}."
                );

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Demo Tickets
            |--------------------------------------------------------------------------
            */

            $existingTicket = SupportTicket::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'product_id',
                    $product->id
                )
                ->where(
                    'issue_type_id',
                    $issueType->id
                )
                ->where(
                    'description',
                    $data['description']
                )
                ->first();


            if ($existingTicket) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Ticket Number
            |--------------------------------------------------------------------------
            */

            do {

                $ticketNumber =
                    'TK-' .
                    strtoupper(
                        Str::random(8)
                    );

            } while (
                SupportTicket::where(
                    'ticket_number',
                    $ticketNumber
                )->exists()
            );


            /*
            |--------------------------------------------------------------------------
            | Create Ticket
            |--------------------------------------------------------------------------
            */

            $ticket = SupportTicket::create([

                'ticket_number' =>
                    $ticketNumber,

                'user_id' =>
                    $user->id,

                'product_id' =>
                    $product->id,

                'issue_type_id' =>
                    $issueType->id,

                'priority' =>
                    $data['priority'],

                'status' =>
                    $data['status'],

                'description' =>
                    $data['description'],

                'attachment' =>
                    null,

                'resolved_at' =>
                    in_array(
                        $data['status'],
                        [
                            'resolved',
                            'closed',
                        ],
                        true
                    )
                        ? now()
                        : null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Initial User Message
            |--------------------------------------------------------------------------
            */

            SupportTicketMessage::create([

                'ticket_id' =>
                    $ticket->id,

                'user_id' =>
                    $user->id,

                'sender_type' =>
                    'user',

                'message' =>
                    $data['description'],

                'attachment' =>
                    null,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Admin Reply
            |--------------------------------------------------------------------------
            |
            | Only create admin reply if an admin exists.
            |
            */

            if (
                $admin &&
                in_array(
                    $data['status'],
                    [
                        'in_progress',
                        'resolved',
                    ],
                    true
                )
            ) {

                SupportTicketMessage::create([

                    'ticket_id' =>
                        $ticket->id,

                    'user_id' =>
                        $admin->id,

                    'sender_type' =>
                        'admin',

                    'message' =>
                        $data['status'] === 'resolved'
                            ? 'Our support team has reviewed the issue and resolved it. Please check again and let us know if you continue to face the problem.'
                            : 'Our support team is currently investigating this issue. We will update you once we have more information.',

                    'attachment' =>
                        null,

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            'User products and support ticket demo data created successfully.'
        );
    }
}