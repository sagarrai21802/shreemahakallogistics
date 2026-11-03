@props(['status' => ''])

@php
    $statuses = [
        'new' => ['label' => 'New', 'tone' => 'attention'],
        'ready_to_match' => ['label' => 'Ready to match', 'tone' => 'neutral'],
        'invitations_sent' => ['label' => 'Invitations sent', 'tone' => 'attention'],
        'awaiting_responses' => ['label' => 'Awaiting responses', 'tone' => 'attention'],
        'driver_accepted' => ['label' => 'Driver accepted', 'tone' => 'success'],
        'follow_up_needed' => ['label' => 'Follow-up needed', 'tone' => 'attention'],
        'confirmed' => ['label' => 'Confirmed', 'tone' => 'success'],
        'closed' => ['label' => 'Closed', 'tone' => 'neutral'],
        'cancelled' => ['label' => 'Cancelled', 'tone' => 'neutral'],
        'no_driver_available' => ['label' => 'No driver available', 'tone' => 'danger'],
        'pending_review' => ['label' => 'Pending review', 'tone' => 'attention'],
        'needs_information' => ['label' => 'Needs information', 'tone' => 'attention'],
        'approved' => ['label' => 'Approved', 'tone' => 'success'],
        'rejected' => ['label' => 'Rejected', 'tone' => 'danger'],
        'submitted' => ['label' => 'Submitted', 'tone' => 'attention'],
        'draft' => ['label' => 'Draft', 'tone' => 'neutral'],
    ];

    $badge = $statuses[$status] ?? [
        'label' => \Illuminate\Support\Str::headline($status),
        'tone' => 'neutral',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'status-badge status-badge--'.$badge['tone']]) }}>
    {{ $badge['label'] }}
</span>
