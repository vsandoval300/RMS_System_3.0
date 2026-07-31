@component('mail::message')
# Pending Approvals Summary

Hello, {{ $manager->name }},

The following items are awaiting your review in the RMS platform:

@if($pendingBusinesses->isNotEmpty())
---
## Individual Businesses ({{ $pendingBusinesses->count() }})

@component('mail::table')
| Code | Description | Submitted by | Waiting |
|------|-------------|--------------|---------|
@foreach($pendingBusinesses as $biz)
| **{{ $biz->business_code }}** | {{ \Illuminate\Support\Str::limit($biz->description ?? '—', 45) }} | {{ $biz->createdByUser?->name ?? '—' }} | {{ $biz->approval_status_updated_at?->diffForHumans() ?? '—' }} |
@endforeach
@endcomponent
@endif

@if($pendingBatches->isNotEmpty())
---
## Import Batches ({{ $pendingBatches->count() }})

@component('mail::table')
| Batch | Records | Imported by | Waiting |
|-------|---------|-------------|---------|
@foreach($pendingBatches as $batch)
| **{{ $batch->batch_code }}** | {{ $batch->totalRecords() }} | {{ $batch->importer?->name ?? '—' }} | {{ $batch->imported_at?->diffForHumans() ?? '—' }} |
@endforeach
@endcomponent
@endif

@component('mail::button', ['url' => route('filament.admin.resources.businesses.index'), 'color' => 'primary'])
Go to Platform
@endcomponent

This is a weekly summary. Individual in-app notifications are available in the bell icon on the platform.

Thanks,
{{ config('app.name') }}
@endcomponent
