<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Create a new support ticket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'phone' => 'required|string|min:10|max:20',
            'subject' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:5000',
            'priority' => 'nullable|in:low,medium,high',
            'files' => 'nullable|array|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov|max:51200',
            'audio' => 'nullable|file|mimes:webm,ogg,mp3,wav|max:10240',
        ], [
            'order_id.required' => translate('Order ID is required'),
            'phone.required' => translate('Phone number is required'),
            'subject.required' => translate('Subject is required'),
            'subject.min' => translate('Subject must be at least 5 characters'),
            'description.required' => translate('Description is required'),
            'description.min' => translate('Description must be at least 20 characters'),
            'files.max' => translate('Maximum 10 files allowed'),
            'files.*.mimes' => translate('Invalid file type. Allowed: jpg, jpeg, png, webp, gif, mp4, webm, mov'),
            'files.*.max' => translate('File size must not exceed 50MB'),
            'audio.mimes' => translate('Invalid audio format. Allowed: webm, ogg, mp3, wav'),
            'audio.max' => translate('Audio file must not exceed 10MB'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate order exists and phone matches (phone is in order_groups table)
        $order = Order::where('order_code', $request->order_id)
            ->whereHas('orderGroup', function ($query) use ($request) {
                $query->where('phone', $request->phone);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => translate('Order not found or phone number does not match'),
            ], 422);
        }

        // Get first admin for auto-assignment
        $firstAdmin = User::whereIn('user_type', ['admin', 'staff'])->first();

        // Create ticket - user_id is optional for guest tickets
        $ticket = Ticket::create([
            'user_id' => auth('sanctum')->check() ? apiUserId() : null,
            'order_id' => $order->id,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
            'priority' => $request->priority ?? 'medium',
            'assigned_to' => $firstAdmin?->id,
        ]);

        // Process file attachments
        if ($request->hasFile('files')) {
            $this->processAttachments($ticket, $request->file('files'));
        }

        // Process audio recording
        if ($request->hasFile('audio')) {
            $this->processAudio($ticket, $request->file('audio'));
        }

        $ticket->load(['user', 'order', 'assignedAdmin', 'attachments']);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Ticket created successfully'),
            'data' => [
                'ticket' => $ticket,
                'public_url' => url('/support/ticket/' . $ticket->public_id),
            ],
        ]);
    }

    /**
     * Get ticket details by public UUID (public access)
     */
    public function show($publicId)
    {
        $ticket = Ticket::where('public_id', $publicId)
            ->with(['user', 'order', 'assignedAdmin', 'attachments', 'replies.user'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => [
                'ticket' => $ticket,
            ],
        ]);
    }

    /**
     * Add reply to ticket
     */
    public function reply(Request $request, $publicId)
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

        $ticket = Ticket::where('public_id', $publicId)
            ->first();

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
            'user_id' => auth('sanctum')->check() ? apiUserId() : null,
            'message' => $request->message,
            'is_admin_reply' => false,
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

        // Update ticket status to in_progress if it was resolved
        if ($ticket->status === 'resolved') {
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
     * Get all replies for a ticket
     */
    public function getReplies($publicId)
    {
        $ticket = Ticket::where('public_id', $publicId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('Ticket not found'),
            ], 404);
        }

        $replies = $ticket->replies()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark admin replies as read
        $ticket->replies()
            ->where('is_admin_reply', true)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => [
                'replies' => $replies,
            ],
        ]);
    }

    /**
     * Process file attachments
     */
    private function processAttachments(Ticket $ticket, array $files)
    {
        $path = "uploads/ticket/{$ticket->id}/attachments";
        Storage::makeDirectory($path);

        foreach ($files as $file) {
            $mimeType = $file->getMimeType();
            $fileType = Str::startsWith($mimeType, 'image/') ? 'image' : 'video';
            $filePath = $file->storeAs($path, $file->hashName());
            $dbPath = '/uploads/ticket/' . $ticket->id . '/attachments/' . $file->hashName();

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'file_path' => $dbPath,
                'file_type' => $fileType,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Process audio recording
     */
    private function processAudio(Ticket $ticket, $audioFile)
    {
        $path = "uploads/ticket/{$ticket->id}/audio";
        Storage::makeDirectory($path);
        $fileName = 'audio_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $audioFile->getClientOriginalExtension();
        $audioFile->storeAs($path, $fileName);
        $dbPath = '/uploads/ticket/' . $ticket->id . '/audio/' . $fileName;
        $ticket->update(['audio_path' => $dbPath]);
    }
}