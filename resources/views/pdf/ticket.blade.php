
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }
        .header-content {
            flex-grow: 1;
        }
        .qr-code {
            width: 100px;
            height: 100px;
            margin-left: 15px;
            flex-shrink: 0;
        }
        .ticket-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 10px 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 5px 10px;
            width: 50%;
            vertical-align: top;
        }
        .section {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }
        .comment {
            background: #f9fafb;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        .comment-header {
            color: #4b5563;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .meta {
            color: #6b7280;
            font-size: 10pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10pt;
            margin-right: 6px;
            background: #e5e7eb;
            color: #374151;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
        }
        .header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a1a1a;
        }
        .comment {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .comment-header {
            margin-bottom: 5px;
            color: #666;
        }
        .document {
            margin-bottom: 5px;
        }
        .meta {
            color: #666;
            font-size: 0.9em;
        }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9em;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 80%;">
                    <h1 class="ticket-title">Ticket #{{ $ticket->id }} - {{ $ticket->title }}</h1>
                </td>
                @if($qrCodeBase64)
                <td style="width: 20%; text-align: right; vertical-align: top;">
                    <!-- QR Code -->
                    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code" style="width: 80px; height: 80px;">
                </td>
                @endif
            </tr>
            
            <div>
                <span class="badge" style="background: {{ $ticket->status->color }}20; color: {{ $ticket->status->color }}">
                    {{ $ticket->status->name }}
                </span>
                <span class="badge" style="background: {{ $ticket->priority === 'high' ? '#fee2e2' : ($ticket->priority === 'medium' ? '#fef3c7' : '#d1fae5') }}; color: {{ $ticket->priority === 'high' ? '#dc2626' : ($ticket->priority === 'medium' ? '#d97706' : '#059669') }}">
                    {{ ucfirst($ticket->priority) }}
                </span>
                @if($ticket->category)
                    <span class="badge" style="background: {{ $ticket->category->color }}20; color: {{ $ticket->category->color }}">
                        {{ $ticket->category->name }}
                    </span>
                @endif
            </div>
        </div>

    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell">
                <span class="label">Créé par:</span> {{ $ticket->creator->name }}<br>
                <span class="label">Assigné à:</span> {{ $ticket->assignees->isNotEmpty() ? $ticket->assignees->pluck('name')->join(', ') : 'Non assigné' }}
            </div>
            <div class="info-cell">
                <span class="label">Date de création:</span> {{ $ticket->created_at->format('d/m/Y H:i') }}<br>
                @if($ticket->due_date)
                    <span class="label">Échéance:</span> {{ $ticket->due_date->format('d/m/Y') }}
                @endif
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <div style="white-space: pre-wrap;">{{ $ticket->description }}</div>
    </div>

    @if($ticket->documents->count() > 0)
    <div class="section">
        <div class="section-title">Documents attachés</div>
        @foreach($ticket->documents as $document)
        <div style="margin-bottom: 4px;">
            {{ $document->name }}
            <span class="meta">({{ number_format($document->size / 1024, 2) }} KB)</span>
        </div>
        @endforeach
    </div>
    @endif

    @if($ticket->comments->count() > 0)
    <div class="section">
        <div class="section-title">Commentaires</div>
        @foreach($ticket->comments as $comment)
        <div class="comment">
            <div class="comment-header">
                <span style="color: #4f46e5;">CM-{{ $comment->id }}</span> | 
                {{ $comment->user->name }} | 
                <span class="meta">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="white-space: pre-wrap;">{{ $comment->content }}</div>
            @if($comment->attachments && count($comment->attachments) > 0)
            <div class="meta" style="margin-top: 4px;">
                @foreach($comment->attachments as $attachment)
                <div style="margin-left: 10px;">{{ $attachment['name'] }}</div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
