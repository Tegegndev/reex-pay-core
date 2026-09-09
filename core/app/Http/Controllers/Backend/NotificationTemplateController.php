<?php

namespace App\Http\Controllers\Backend;

use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateChannel;
use Illuminate\Http\Request;

class NotificationTemplateController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'              => 'notification-template-list',
            'edit|updateChannel' => 'notification-template-manage',
        ];
    }

    public function index()
    {
        $notifyTemplates = NotificationTemplate::with('channels')->paginate(8)->withQueryString();

        return view('backend.notifications.template.index', compact('notifyTemplates'));
    }

    public function edit(NotificationTemplate $template)
    {
        $template->load('channels');

        return view('backend.notifications.template.edit', compact('template'));
    }

    public function updateChannel(Request $request, NotificationTemplate $template, NotificationTemplateChannel $channel)
    {

        $request->validate([
            'title'   => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        if ($channel->template_id != $template->id) {
            abort(403);
        }

        $channel->update([
            'title'     => $request->input('title'),
            'message'   => $request->input('message'),
            'is_active' => $request->has('is_active'),
        ]);

        notifyEvs('success', 'Channel template updated successfully.');

        return redirect()->route('admin.notifications.template.index');
    }
}
