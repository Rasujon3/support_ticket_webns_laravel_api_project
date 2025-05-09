@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">Ticket #{{ $ticket->id }} - {{ $ticket->title }}</h4>

        <div class="mb-4">
            <p><strong>Status:</strong> <span class="badge bg-info text-dark">{{ ucfirst($ticket->status) }}</span></p>
            <p><strong>Priority:</strong> <span class="badge bg-secondary">{{ ucfirst($ticket->priority) }}</span></p>
            <p><strong>Created At:</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>
            <p><strong>Assigned To:</strong> {{ $ticket->assignedTo?->name ?? 'Not Assigned' }}</p>
        </div>

        <hr>

        <h5 class="mb-3">Conversation</h5>

        @forelse ($messages as $msg)
            <div class="mb-4 p-3 border rounded @if($msg->user_id === auth()->id()) bg-light @endif">
                <div class="d-flex justify-content-between mb-1">
                    <strong>{{ $msg->user->name }}</strong>
                    <small>{{ $msg->created_at->diffForHumans() }}</small>
                </div>
                <p>{{ $msg->message }}</p>

                @if ($msg->attachments->count())
                    <div>
                        <strong>Attachments:</strong>
                        <ul class="list-unstyled">
                            @foreach ($msg->attachments as $file)
                                <li>
                                    <a href="{{ asset($file->file_path) }}" target="_blank">{{ $file->original_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @empty
            <p>No messages yet.</p>
        @endforelse

        <hr>

        <h5 class="mb-3">Reply</h5>
        <form action="{{ route('messages.store', $ticket->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Your Message</label>
                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required></textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Attachments</label>
                <input type="file" name="attachments[]" class="form-control" multiple>
            </div>

            <button type="submit" class="btn btn-primary">Send Reply</button>
        </form>
    </div>
@endsection
