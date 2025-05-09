<?php

namespace App\Modules\Tickets\Repositories;

use App\Models\User;
use App\Modules\Tickets\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketRepository
{
    public function getTicketData()
    {
        $user = Auth::user();

        $tickets =  $user->isAdmin()
            ? Ticket::latest()->paginate(10)
            : $user->tickets()->latest()->paginate(10);

        $is_admin = $user->isAdmin();

        $auth_user_id = $user->id;

        return [
            'is_admin' => $is_admin,
            'auth_user_id' => $auth_user_id,
            'tickets' => $tickets
        ];
    }

    public function store(array $data): ?Ticket
    {
        try {
            $data['user_id'] = Auth::id();
            $data['status'] = 'open';

            // Create the record in the database
            $store = Ticket::create($data);

            return $store;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in storing data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function getMessageData($ticket)
    {
        return $ticket->messages()->with('user')->get();
    }

    public function assignUpdate(Ticket $ticket, array $data): ?Ticket
    {
        try {
            // Perform the update
            $ticket->update($data);

            return $ticket;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function updateStatus(Ticket $ticket, array $data): ?Ticket
    {
        try {
            // Perform the update
            $ticket->update($data);

            return $ticket;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error status updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function getAdminsData()
    {
        return User::where('role', 'admin')->get();
    }
    public function getUserData()
    {
        return Auth::user();
    }
}
