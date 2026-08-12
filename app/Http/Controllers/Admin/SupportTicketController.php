<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Admin: All support tickets
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with([
            'user',
            'issueType',
        ])->latest();


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'ticket_number',
                    'like',
                    "%{$search}%"
                );

                $q->orWhereHas('user', function ($userQuery) use ($search) {

                    $userQuery
                        ->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        );

                });

                $q->orWhereHas('issueType', function ($issueQuery) use ($search) {

                    $issueQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );

                });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Priority Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Tickets
        |--------------------------------------------------------------------------
        */

        $tickets = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [

            'total' => SupportTicket::count(),

            'open' => SupportTicket::where(
                'status',
                'open'
            )->count(),

            'in_progress' => SupportTicket::where(
                'status',
                'in_progress'
            )->count(),

            'waiting' => SupportTicket::where(
                'status',
                'waiting_for_user'
            )->count(),

            'resolved' => SupportTicket::where(
                'status',
                'resolved'
            )->count(),

        ];


        return view(
            'admin.support.tickets.index',
            compact(
                'tickets',
                'stats'
            )
        );
    }


    /**
     * Admin: View single ticket
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load([
            'user',
            'issueType',
            'messages.user',
        ]);


        return view(
            'admin.support.tickets.show',
            compact('ticket')
        );
    }


    /**
     * Admin: Reply to ticket
     */
    public function reply(
        Request $request,
        SupportTicket $ticket
    ) {
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
        | Save Admin Message
        |--------------------------------------------------------------------------
        */

        SupportTicketMessage::create([

            'ticket_id' => $ticket->id,

            'user_id' => auth()->id(),

            'sender_type' => 'admin',

            'message' => $validated['message'],

            'attachment' => $attachmentPath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Ticket Status
        |--------------------------------------------------------------------------
        */

        if ($ticket->status === 'open') {

            $ticket->update([
                'status' => 'in_progress',
            ]);
        }


        return back()->with(
            'success',
            'Reply sent successfully.'
        );
    }


    /**
     * Admin: Change ticket status
     */
    public function updateStatus(
        Request $request,
        SupportTicket $ticket
    ) {
        $validated = $request->validate([

            'status' => [
                'required',
                'in:open,in_progress,waiting_for_user,resolved,closed',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Status
        |--------------------------------------------------------------------------
        */

        $ticket->update([

            'status' => $validated['status'],

            'resolved_at' =>
                in_array(
                    $validated['status'],
                    [
                        'resolved',
                        'closed',
                    ]
                )
                    ? now()
                    : null,

        ]);


        return back()->with(
            'success',
            'Ticket status updated successfully.'
        );
    }
}