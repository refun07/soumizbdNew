<?php

namespace App\Http\Controllers\Backend\Admin\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_tickets'])->only(['app', 'index', 'show']);
        $this->middleware(['permission:update_tickets'])->only(['updateStatus', 'updatePriority', 'assignToAdmin']);
        $this->middleware(['permission:reply_tickets'])->only(['reply']);
        $this->middleware(['permission:delete_tickets'])->only(['deleteAttachment']);
    }

    /**
     * Render the React app view
     */
    public function app()
    {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            'currency' => [
                'code' => getSetting('currencyCode') ?? 'usd',
                'symbol' => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1 : 0] : 'symbol_first',
                    'show' => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals' => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator' => getSetting('decimalSeparator') ?? '.',
            ],
            'appUrl' => config('app.url'),
            'customerTicketUrl' => config('app.url') . '/support/ticket/create',
        ];

        return view('backend.admin.support.index', compact('settings'));
    }

    /**
     * List all tickets with filters
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;

        $query = Ticket::with(['user', 'order', 'assignedAdmin'])
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned admin
        if ($request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order code, phone, or subject
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('code', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $tickets,
        ]);
    }

    /**
     * Get single ticket details
     */
    public function show($id)
    {
        $ticket = Ticket::with([
            'user',
            'order',
            'assignedAdmin',
            'attachments',
            'replies.user',
        ])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        // Get available admins for assignment
        $admins = User::whereIn('user_type', ['admin', 'staff'])->select('id', 'name', 'email')->get();

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => [
                'ticket' => $ticket,
                'admins' => $admins,
            ],
        ]);
    }

    /**
     * Add admin reply to ticket
     */
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,pdf,doc,docx|max:10240',
        ], [
            'message.required' => translate('Message is required'),
            'message.min' => translate('Message must be at least 5 characters'),
            'attachment.mimes' => translate('Invalid file type'),
            'attachment.max' => translate('Attachment must not exceed 10MB'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        // Don't allow replies on closed tickets
        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => translate('Cannot reply to a closed ticket'),
            ], 422);
        }

        $replyData = [
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => true,
        ];

        // Process attachment if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = "uploads/ticket/{$ticket->id}/replies";
            Storage::makeDirectory($path);
            $fileName = time() . '_' . $file->hashName();
            $file->storeAs($path, $fileName);
            $replyData['attachment_path'] = '/uploads/ticket/' . $ticket->id . '/replies/' . $fileName;
        }

        $reply = TicketReply::create($replyData);
        $reply->load('user');

        // Update ticket status if it's open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Reply added successfully'),
            'data' => [
                'reply' => $reply,
            ],
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved,closed',
        ], [
            'status.required' => translate('Status is required'),
            'status.in' => translate('Invalid status'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        $updateData = ['status' => $request->status];

        // Set resolved_at when status is resolved
        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        } else {
            $updateData['resolved_at'] = null;
        }

        $ticket->update($updateData);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Status updated successfully'),
            'data' => [
                'ticket' => $ticket->fresh(),
            ],
        ]);
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|in:low,medium,high',
        ], [
            'priority.required' => translate('Priority is required'),
            'priority.in' => translate('Invalid priority'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        $ticket->update(['priority' => $request->priority]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Priority updated successfully'),
            'data' => [
                'ticket' => $ticket->fresh(),
            ],
        ]);
    }

    /**
     * Assign ticket to admin
     */
    public function assignToAdmin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
        ], [
            'assigned_to.required' => translate('Admin is required'),
            'assigned_to.exists' => translate('Selected admin does not exist'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        // Verify the user is an admin
        $admin = User::find($request->assigned_to);
        if (!$admin || !in_array($admin->user_type, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => translate('Selected user is not an admin'),
            ], 422);
        }

        $ticket->update(['assigned_to' => $request->assigned_to]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Ticket assigned successfully'),
            'data' => [
                'ticket' => $ticket->fresh()->load('assignedAdmin'),
            ],
        ]);
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment($id, $attachmentId)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        $attachment = TicketAttachment::where('ticket_id', $id)
            ->where('id', $attachmentId)
            ->first();

        if (!$attachment) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Attachment not found'),
            ], 404);
        }

        $attachment->delete();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Attachment deleted successfully'),
        ]);
    }
}