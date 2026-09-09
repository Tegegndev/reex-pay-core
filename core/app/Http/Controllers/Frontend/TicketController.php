<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\Status;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupportCategory;
use App\Models\Ticket;
use App\Notifications\TemplateNotification;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    use FileManageTrait;

    public function index()
    {
        $tickets = Ticket::with('category')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(5);

        return view('frontend.user.support_ticket.index', compact('tickets'));
    }

    public function create()
    {
        $categories = SupportCategory::where('status', Status::ACTIVE)->get();

        return view('frontend.user.support_ticket.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'message' => 'required',
        ]);

        // Prepare ticket data
        $ticketData = [
            'uuid'        => strtoupper('#'.Str::random(7)),
            'title'       => $request->title,
            'message'     => $request->message,
            'attachment'  => $request->hasFile('attachment') ? $this->uploadFile($request->file('attachment')) : null,
            'user_id'     => auth()->id(),
            'category_id' => $request->category_id,
            'priority'    => $request->priority,
            'status'      => TicketStatus::PENDING,
        ];

        // Create ticket
        $ticket = Ticket::create($ticketData);

        $admins = Admin::permission('support-ticket-notification')->get();

        Notification::send($admins, new TemplateNotification(
            identifier: 'support_user_created',
            data: [
                'user'          => auth()->user()->name,
                'ticket_number' => $ticket->uuid,
                'subject'       => $ticket->title,
            ],
            sender: auth()->user(),
            action: route('admin.support-ticket.show', $ticket->id)
        ));

        notifyEvs('success', __('Ticket created successfully'));

        return redirect()->route('user.support-ticket.index');
    }

    public function show($id)
    {

        $title = __('Support Ticket Details');

        $ticket = Ticket::findOrFail($id);

        return view('frontend.user.support_ticket.show', compact('title', 'ticket'));
    }

    public function reply(Ticket $ticket, Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $ticket->messages()->create([
            'message'    => $request->message,
            'attachment' => $request->hasFile('attachment') ? $this->uploadFile($request->file('attachment')) : null,
        ]);

        $ticket->status = TicketStatus::OPEN;
        $ticket->save();

        $admins = Admin::permission('support-ticket-notification')->get();

        Notification::send($admins, new TemplateNotification(
            identifier: 'support_user_replied',
            data: [
                'user'          => auth()->user()->name,
                'ticket_number' => $ticket->uuid,
                'subject'       => $ticket->title,
            ],
            sender: auth()->user(),
            action: route('admin.support-ticket.show', $ticket->id)
        ));

        notifyEvs('success', __('Reply created successfully'));

        return redirect()->back();
    }
}
