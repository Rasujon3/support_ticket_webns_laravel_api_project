<?php

namespace App\Modules\Messages\Controllers;

use App\Events\NewMessageSent;
use App\Modules\Attachments\Models\Attachment;
use App\Modules\Messages\Models\Message;
use App\Modules\Messages\Queries\MessageDatatable;
use App\Modules\Messages\Repositories\MessageRepository;
use App\Modules\Messages\Requests\MessageRequest;
use App\Modules\Tickets\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;

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
        try {
            // Check permission (creator or assigned admin)
            $checkPermission = $this->messageRepository->checkPermission($ticket);
            if (!$checkPermission) {
                return $this->sendError('Unauthorized, only the creator or assigned admin can reply.', 500);
            }

            $store = $this->messageRepository->store($request, $ticket);

            if (!$store) {
                return $this->sendError('Failed to add reply.', 500);
            }

            # broadcast(new NewMessageSent($store))->toOthers();

            return $this->sendResponse([], 'Reply added successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in storing message: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendError('Something went wrong!!!', 500);
        }
    }
}
