<?php

namespace App\Modules\Messages\Controllers;

use App\Modules\Attachments\Models\Attachment;
use App\Modules\Messages\Models\Message;
use App\Modules\Messages\Queries\MessageDatatable;
use App\Modules\Messages\Repositories\MessageRepository;
use App\Modules\Messages\Requests\MessageRequest;
use App\Modules\Tickets\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppBaseController;

class MessageController extends AppBaseController
{
    protected $messageRepository;

    // Inject the repository using the constructor
    public function __construct(MessageRepository $messageRepo)
    {
        $this->messageRepository = $messageRepo;
    }

    /**
     * Store a new ticket reply.
     */
    public function store(MessageRequest $request, Ticket $ticket)
    {
        // Check permission (creator or assigned admin)
        $checkPermission = $this->messageRepository->checkPermission($ticket);
        if (!$checkPermission) {
            return redirect()->route('tickets.index')->with('error', 'Unauthorized, only the creator or assigned admin can reply.');
        }

        $store = $this->messageRepository->store($request, $ticket);

        if (!$store) {
            return redirect()->route('tickets.show', $ticket->id)->with('error', 'Failed to add reply.');
        }

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Reply added successfully.');
    }
}
