<?php

namespace App\Modules\Tickets\Controllers;

use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Repositories\TicketRepository;
use App\Modules\Tickets\Requests\TicketRequest;
use App\Http\Controllers\AppBaseController;

class TicketController extends AppBaseController
{
    protected $ticketRepository;

    // Inject the repository using the constructor
    public function __construct(TicketRepository $ticketRepo)
    {
        $this->ticketRepository = $ticketRepo;
    }
    /**
     * Display a list of tickets.
     * - User sees their own tickets.
     * - Admin sees all tickets.
     */
    public function index()
    {
        $data = $this->ticketRepository->getTicketData();
//        $data['is_admin'] = $data['is_admin'];
//        return $this->sendResponse($data['is_admin'], 'Ticket created successfully.');
//        return $this->sendResponse($data, 'Ticket created successfully.');

//        return view('Tickets::index', compact('tickets'));
//        return $this->sendResponse($tickets, 'Areas retrieved successfully.');
        return response()->json([
            'data' => [
                'data' => $data['tickets'], // Nested 'data' to match your structure
            ],
            'meta' => [
                'is_admin' => $data['is_admin'],
                'auth_user_id' => $data['auth_user_id'],
            ],
        ]);
    }

    /**
     * Show the ticket creation form.
     */
    public function create()
    {
        if (auth()->user()->isAdmin()) {
//            return redirect()->route('tickets.index')->with('error', 'Admins cannot create tickets.');
            return $this->sendError('Admins cannot create tickets.', 500);
        }
        return view('Tickets::create');
    }

    /**
     * Store a new ticket.
     */
    public function store(TicketRequest $request)
    {
        $store = $this->ticketRepository->store($request->all());
        if (!$store) {
//            return redirect()->back()->with('error', 'Something went wrong!!! [TCS-01]');
            return $this->sendError('Something went wrong!!! [TCS-01]', 500);
        }

//        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
        return $this->sendResponse($store, 'Ticket created successfully.');
    }
    /**
     * Show a single ticket with its messages (conversation).
     */
    public function show(Ticket $ticket)
    {
        $this->authorizeTicketAccess($ticket);

        $messages = $this->ticketRepository->getMessageData($ticket);

//        return view('Tickets::show', compact('ticket', 'messages'));
        return $this->sendResponse(['ticket' => $ticket, 'messages' => $messages], 'Ticket data retrieved successfully.');
    }
    /**
     * Assign ticket to an admin (Admin only).
     */
    public function assign(TicketRequest $request, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => $request->assigned_to]);
        $updated = $this->ticketRepository->assignUpdate($ticket, $request->all());
        if (!$updated) {
//            return redirect()->back()->with('error', 'Something went wrong!!! [TCU-01]');
            return $this->sendError('Something went wrong!!! [TCU-01]', 500);
        }

//        return redirect()->route('tickets.index')->with('success', 'Ticket assigned successfully.');
        return $this->sendResponse($updated, 'Ticket assigned successfully.');
    }

    /**
     * Update ticket status (Admin only).
     */
    public function updateStatus(TicketRequest $request, Ticket $ticket)
    {
        if (auth()->id() !== $ticket->assigned_to) {
//            return redirect()->route('tickets.index')->with('error', 'Only the assigned admin can update ticket status.');
            return $this->sendError('Only the assigned admin can update ticket status.', 500);
        }

        $updated = $this->ticketRepository->updateStatus($ticket, $request->all());
        if (!$updated) {
//            return redirect()->back()->with('error', 'Something went wrong!!! [TCU-02]');
            return $this->sendError('Something went wrong!!! [TCU-02]', 500);
        }

//        return redirect()->route('tickets.index')->with('success', 'Ticket status updated.');
        return $this->sendResponse($updated, 'Ticket status updated.');
    }
    public function assignForm(Ticket $ticket)
    {
        $admins = $this->ticketRepository->getAdminsData();
//        return view('Tickets::assign_form', compact('ticket', 'admins'));
        return $this->sendResponse(['ticket' => $ticket, 'admins' => $admins], 'Admin data retrieved successfully.');
    }

    public function statusForm(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->assigned_to) {
//            return redirect()->route('tickets.index')->with('error', 'Only the assigned admin can update the ticket status.');
            return $this->sendError('Only the assigned admin can update the ticket status.', 500);
        }
//        return view('Tickets::status_form', compact('ticket'));
        return $this->sendResponse(['ticket' => $ticket], 'Ticket data retrieved successfully.');
    }
    /**
     * Authorize access for ticket viewing.
     */
    private function authorizeTicketAccess(Ticket $ticket)
    {
        $user = $this->ticketRepository->getUserData();

        if ($user->isAdmin() || $ticket->user_id === $user->id || $ticket->assigned_to === $user->id) {
            return true;
        }

//        return redirect()->route('tickets.index')->with('error', 'Unauthorized');
        return $this->sendError('Admins cannot create tickets.', 500);
    }
}
