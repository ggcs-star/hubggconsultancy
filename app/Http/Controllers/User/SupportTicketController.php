<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
     */
    public function create()
    {
        $issueTypes = SupportIssueType::where(
            'status',
            true
        )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'user.support.tickets.create',
            compact('issueTypes')
        );
    }


    /**
     * User: Store ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'issue_type_id' => [
                'required',
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
        | Make sure selected issue type is active
        |--------------------------------------------------------------------------
        */

        $issueType = SupportIssueType::where(
            'id',
            $validated['issue_type_id']
        )
            ->where('status', true)
            ->first();


        if (!$issueType) {

            return back()
                ->withInput()
                ->withErrors([
                    'issue_type_id' =>
                        'This support issue is currently unavailable.',
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
        | Ticket Number
        |--------------------------------------------------------------------------
        */

        do {

            $ticketNumber = 'TK-' .
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

            'ticket_number' => $ticketNumber,

            'user_id' => auth()->id(),

            'issue_type_id' => $issueType->id,

            'priority' => $issueType->default_priority,

            'status' => 'open',

            'description' => $validated['description'],

            'attachment' => $attachmentPath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Save Initial User Message
        |--------------------------------------------------------------------------
        */

        SupportTicketMessage::create([

            'ticket_id' => $ticket->id,

            'user_id' => auth()->id(),

            'sender_type' => 'user',

            'message' => $validated['description'],

            'attachment' => $attachmentPath,

        ]);


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
     * User: Show own ticket only
     */
    public function show(SupportTicket $ticket)
    {
        /*
        |--------------------------------------------------------------------------
        | Security: User can only see own ticket
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $ticket->user_id === auth()->id(),
            403
        );


        $ticket->load([
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
        | Security: Only ticket owner
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $ticket->user_id === auth()->id(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Closed / Resolved ticket
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $ticket->status,
                [
                    'resolved',
                    'closed',
                ]
            )
        ) {

            return back()->with(
                'error',
                'This ticket is already resolved or closed.'
            );
        }


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

            'ticket_id' => $ticket->id,

            'user_id' => auth()->id(),

            'sender_type' => 'user',

            'message' => $validated['message'],

            'attachment' => $attachmentPath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $ticket->update([

            'status' => 'open',

            'resolved_at' => null,

        ]);


        return back()->with(
            'success',
            'Reply sent successfully.'
        );
    }
}