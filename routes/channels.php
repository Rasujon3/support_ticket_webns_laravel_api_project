<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{ticketId}', function ($user, $ticketId) {
    return true; // Or more complex permission logic
});

