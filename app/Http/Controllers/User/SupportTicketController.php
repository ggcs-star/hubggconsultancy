<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use App\Models\SupportIssueType;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * User: Show only logged-in user's tickets
     */
    public function index()
    {
        $userId = auth()->id();

        $tickets = SupportTicket::with([
            'issueType',
            'product',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => SupportTicket::where(
                'user_id',
                $userId
            )->count(),

            'open' => SupportTicket::where(
                'user_id',
                $userId
            )
                ->where('status', 'open')
                ->count(),

            'in_progress' => SupportTicket::where(
                'user_id',
                $userId
            )
                ->where('status', 'in_progress')
                ->count(),

            'resolved' => SupportTicket::where(
                'user_id',
                $userId
            )
                ->where('status', 'resolved')
                ->count(),
        ];

        return view(
            'user.support.tickets.index',
            compact(
                'tickets',
                'stats'
            )
        );
    }


    /**
     * User: Create ticket page
     *
     * Only products assigned/purchased by the logged-in user
     * from saas_product_user will be shown.
     *
     * Issue types for those products are loaded so the Blade
     * can filter them when the user changes the product.
     */
    public function create()
    {
        $userId = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | User Products
        |--------------------------------------------------------------------------
        |
        | saas_products       = master product table
        | saas_product_user   = user's assigned/purchased products
        |
        */

        $products = SaasProduct::query()
            ->join(
                'saas_product_user',
                'saas_products.id',
                '=',
                'saas_product_user.saas_product_id'
            )
            ->where(
                'saas_product_user.user_id',
                $userId
            )
            ->where(
                'saas_products.active',
                true
            )
            ->select(
                'saas_products.id',
                'saas_products.name',
                'saas_products.slug',
                'saas_products.category',
                'saas_products.logo',
                'saas_products.sort_order'
            )
            ->distinct()
            ->orderBy('saas_products.sort_order')
            ->orderBy('saas_products.name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Issue Types
        |--------------------------------------------------------------------------
        |
        | Only active issues belonging to products assigned to
        | the current user are loaded.
        |
        */

        $issueTypes = SupportIssueType::query()
            ->where('status', true)
            ->whereIn(
                'saas_product_id',
                $products->pluck('id')->toArray()
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();


        return view(
            'user.support.tickets.create',
            compact(
                'products',
                'issueTypes'
            )
        );
    }


    /**
     * User: Store ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:saas_products,id',
            ],

            'issue_type_id' => [
                'required',
                'integer',
                'exists:support_issue_types,id',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify Product Belongs To Logged-in User
        |--------------------------------------------------------------------------
        |
        | User cannot manually change product_id and create a ticket
        | for a product which is not assigned to their account.
        |
        */

        $product = SaasProduct::query()
            ->join(
                'saas_product_user',
                'saas_products.id',
                '=',
                'saas_product_user.saas_product_id'
            )
            ->where(
                'saas_products.id',
                $validated['product_id']
            )
            ->where(
                'saas_product_user.user_id',
                auth()->id()
            )
            ->where(
                'saas_products.active',
                true
            )
            ->select(
                'saas_products.*'
            )
            ->first();


        if (!$product) {

            return back()
                ->withInput()
                ->withErrors([
                    'product_id' =>
                        'The selected product is not assigned to your account.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Issue Type
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | The selected issue MUST belong to the selected product.
        |
        | Example:
        |
        | LocalPulse
        |     -> Login Issue
        |     -> Business Profile Issue
        |
        | PharmaSphere
        |     -> Inventory Issue
        |     -> Prescription Issue
        |
        | A LocalPulse ticket can NEVER submit a PharmaSphere issue.
        |
        */

        $issueType = SupportIssueType::query()
            ->where(
                'id',
                $validated['issue_type_id']
            )
            ->where(
                'saas_product_id',
                $product->id
            )
            ->where(
                'status',
                true
            )
            ->first();


        if (!$issueType) {

            return back()
                ->withInput()
                ->withErrors([
                    'issue_type_id' =>
                        'The selected issue does not belong to the selected product.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Attachment
        |--------------------------------------------------------------------------
        */

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {

            $attachmentPath = $request
                ->file('attachment')
                ->store(
                    'support-tickets',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Unique Ticket Number
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
                auth()->id(),

            'product_id' =>
                $product->id,

            'issue_type_id' =>
                $issueType->id,

            'priority' =>
                $issueType->default_priority,

            'status' =>
                'open',

            'description' =>
                $validated['description'],

            'attachment' =>
                $attachmentPath,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create Initial User Message
        |--------------------------------------------------------------------------
        */

        SupportTicketMessage::create([

            'ticket_id' =>
                $ticket->id,

            'user_id' =>
                auth()->id(),

            'sender_type' =>
                'user',

            'message' =>
                $validated['description'],

            'attachment' =>
                $attachmentPath,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'user.support.tickets.show',
                $ticket
            )
            ->with(
                'success',
                'Support ticket raised successfully.'
            );
    }


    /**
     * User: Quick-add a new issue type under one of their assigned products,
     * used by the "Can't find your issue?" popup on the ticket form. Becomes
     * a real, permanently shared SupportIssueType — same quick-add pattern
     * used for Campaigns and FAQ Sections elsewhere in the app.
     */
    public function storeIssueType(Request $request)
    {
        $data = $request->validate([
            'saas_product_id' => [
                'required',
                'integer',
                'exists:saas_products,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $isAssigned = SaasProduct::query()
            ->join(
                'saas_product_user',
                'saas_products.id',
                '=',
                'saas_product_user.saas_product_id'
            )
            ->where('saas_products.id', $data['saas_product_id'])
            ->where('saas_product_user.user_id', auth()->id())
            ->where('saas_products.active', true)
            ->exists();

        if (!$isAssigned) {
            $message = 'That product is not assigned to your account.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withErrors(['saas_product_id' => $message]);
        }

        $baseSlug = Str::slug($data['saas_product_id'] . '-' . $data['name']);
        $slug = $baseSlug;
        $suffix = 1;

        while (SupportIssueType::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        $nextSortOrder = (int) SupportIssueType::where('saas_product_id', $data['saas_product_id'])->max('sort_order') + 1;

        $issueType = SupportIssueType::create([
            'saas_product_id' => $data['saas_product_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'module' => null,
            'default_priority' => 'medium',
            'icon' => null,
            'description' => null,
            'sort_order' => $nextSortOrder,
            'status' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $issueType->id,
                'name' => $issueType->name,
                'module' => $issueType->module,
            ]);
        }

        return back()->with('success', 'Issue added.');
    }


    /**
     * User: Show own ticket only
     */
    public function show(SupportTicket $ticket)
    {
        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $ticket->user_id === auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Load Ticket Data
        |--------------------------------------------------------------------------
        */

        $ticket->load([
            'product',
            'issueType',
            'messages.user',
        ]);


        return view(
            'user.support.tickets.show',
            compact('ticket')
        );
    }


    /**
     * User: Reply to own ticket
     */
    public function reply(
        Request $request,
        SupportTicket $ticket
    ) {
        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $ticket->user_id === auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Closed / Resolved Ticket
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $ticket->status,
                [
                    'resolved',
                    'closed',
                ],
                true
            )
        ) {

            return back()->with(
                'error',
                'This ticket is already resolved or closed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'message' => [
                'required',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Attachment
        |--------------------------------------------------------------------------
        */

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {

            $attachmentPath = $request
                ->file('attachment')
                ->store(
                    'support-tickets',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Save User Message
        |--------------------------------------------------------------------------
        */

        SupportTicketMessage::create([

            'ticket_id' =>
                $ticket->id,

            'user_id' =>
                auth()->id(),

            'sender_type' =>
                'user',

            'message' =>
                $validated['message'],

            'attachment' =>
                $attachmentPath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Re-open Ticket
        |--------------------------------------------------------------------------
        */

        $ticket->update([

            'status' =>
                'open',

            'resolved_at' =>
                null,

        ]);


        return back()->with(
            'success',
            'Reply sent successfully.'
        );
    }
}