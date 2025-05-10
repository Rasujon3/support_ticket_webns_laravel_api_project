<?php

namespace App\Modules\Messages\Repositories;

use App\Modules\Attachments\Models\Attachment;
use App\Modules\Messages\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageRepository
{
    public function checkPermission($ticket)
    {
        // Check permission (creator or assigned admin)
        $user = Auth::user();
        if ($user->id !== $ticket->user_id && $user->id !== $ticket->assigned_to && !$user->isAdmin()) {
            return false;
        }
        return true;
    }
    public function store($request, $ticket): ?Message
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Create the record in the database
            $message = Message::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'message'   => $request->message,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filePath = $this->storeFile($file);

                    Attachment::create([
                        'message_id'    => $message->id,
                        'file_path'     => $filePath,
                        'original_name' => $originalName,
                    ]);
                }
            }
            DB::commit();

            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
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
    private function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/messages';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('messages_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
}
