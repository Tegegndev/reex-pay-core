<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Notifications\TemplateNotification;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;

class TicketController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'pendingTicket|inprogress|closeTicket|history' => 'support-ticket-list',
            'ticketShow|ticketReplyStore|statusUpdate'     => 'support-ticket-manage',
        ];
    }

    public function pendingTicket()
    {
        $title   = __('New Support Ticket Request');
        $tickets = Ticket::where('status', TicketStatus::PENDING)->orderBy('created_at', 'desc')->paginate(10);

        return view('backend.support_ticket.list', compact('tickets', 'title'));
    }

    public function inprogress()
    {
        $title   = __('Open Support Ticket Request');
        $tickets = Ticket::where('status', TicketStatus::OPEN)->orderBy('created_at', 'desc')->paginate(10);

        return view('backend.support_ticket.list', compact('tickets', 'title'));
    }

    public function closeTicket()
    {
        $title   = __('Close Support Ticket Request');
        $tickets = Ticket::where('status', TicketStatus::CLOSED)->orderBy('created_at', 'desc')->paginate(10);

        return view('backend.support_ticket.list', compact('tickets', 'title'));
    }

    public function history()
    {
        $title   = __('Support Ticket History');
        $tickets = Ticket::orderBy('created_at', 'desc')->paginate(10);

        return view('backend.support_ticket.list', compact('tickets', 'title'));
    }

    public function ticketShow(Ticket $ticket)
    {

        $title = __('Support Ticket Details');

        return view('backend.support_ticket.show', compact('ticket', 'title'));
    }

    public function ticketReplyStore(Ticket $ticket, Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);
        $ticket->messages()->create([
            'admin_id'   => auth()->id(),
            'message'    => $request->message,
            'attachment' => $request->hasFile('attachment') ? $this->uploadFile($request->file('attachment')) : null,
        ]);

        if ($ticket->status === TicketStatus::PENDING) {
            $ticket->status = TicketStatus::OPEN;
            $ticket->save();
        }

        $ticket->user->notify(new TemplateNotification(
            identifier: 'support_admin_replied',
            data: [
                'ticket_number' => $ticket->uuid,
                'subject'       => $ticket->title,
            ],
            action: route('user.support-ticket.show', $ticket->id),
        ));

        notifyEvs('success', 'Reply created successfully');

        return redirect()->back();
    }

    public function statusUpdate(Request $request, $ticketId)
    {
        $request->validate([
            'ticket_status' => 'required',
        ]);
        $ticket = Ticket::find($ticketId);
        $ticket->update([
            'status' => $request->ticket_status,
        ]);

        if ($ticket->status === TicketStatus::CLOSED) {
            $ticket->user->notify(new TemplateNotification(
                identifier: 'support_user_closed',
                data: [
                    'ticket_number' => $ticket->uuid,
                ],
                action: route('user.support-ticket.show', $ticket->id),
            ));
        }

        notifyEvs('success', 'Ticket status updated successfully');

        return redirect()->back();
    }
}
