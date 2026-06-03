@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Inquiry History</h2>

        <div x-data="{ open: false, inquiry: {} }" x-cloak>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Title</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Date Submitted</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="px-4 py-2 text-gray-700">{{ $inquiry->NewsTitle }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $statusColor = match($inquiry->InquiryStatus) {
                                        'Pending'     => 'bg-gray-100 text-gray-600',
                                        'Reviewed'    => 'bg-blue-100 text-blue-700',
                                        'Assigned'    => 'bg-purple-100 text-purple-700',
                                        'In Progress' => 'bg-yellow-100 text-yellow-700',
                                        'Resolved'    => 'bg-green-100 text-green-700',
                                        'Rejected'    => 'bg-orange-100 text-orange-700',
                                        'Discarded'   => 'bg-red-100 text-red-600',
                                        default       => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $inquiry->InquiryStatus }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $inquiry->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">
                                <button
                                    @click="open = true; inquiry = {
                                        NewsTitle: {{ Js::from($inquiry->NewsTitle) }},
                                        InquiryStatus: {{ Js::from($inquiry->InquiryStatus) }},
                                        created_at: {{ Js::from($inquiry->created_at->format('d M Y h:i A')) }},
                                        NewsContent: {{ Js::from($inquiry->NewsContent) }},
                                        NewsSource: {{ Js::from($inquiry->NewsSource) }},
                                        attachment: {{ Js::from($inquiry->attachment) }},
                                        agency_name: {{ Js::from($inquiry->agency->user?->name ?? 'Not assigned') }},
                                        assignment_date: {{ Js::from(optional($inquiry->assignment)->AssignmentDate ? \Carbon\Carbon::parse($inquiry->assignment->AssignmentDate)->format('d M Y') : 'Not assigned') }},
                                        due_date: {{ Js::from(optional($inquiry->assignment)->due_date ? \Carbon\Carbon::parse($inquiry->assignment->due_date)->format('d M Y') : null) }},
                                        rejection_reason: {{ Js::from(optional($inquiry->assignment)->rejection_reason) }},
                                        progress: {{ Js::from($inquiry->progress->map(fn($p) => [
                                            'date'                => \Carbon\Carbon::parse($p->UpdateDate)->format('d M Y'),
                                            'status'              => $p->ProgressStatus,
                                            'description'         => $p->ProgressDescription,
                                            'officer'             => $p->ReviewingOfficer,
                                            'supporting_document' => $p->SupportingDocument,
                                        ])) }}
                                    }"
                                    class="text-blue-600 hover:underline"
                                    type="button">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                No inquiries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Modal -->
            <div x-show="open" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative max-h-[90vh] overflow-y-auto">
                    <button @click="open = false" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    <h3 class="text-xl font-bold mb-5 text-gray-800">Inquiry Details</h3>

                    <!-- Stepper -->
                    <div class="mb-5">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Progress</p>
                        <div id="inquiryStepperModal"></div>
                    </div>

                    <!-- Reason reject -->
                    <template x-if="inquiry.InquiryStatus === 'Rejected' && inquiry.rejection_reason">
                        <div class="mb-5 px-4 py-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <p class="text-xs font-semibold text-orange-600 uppercase mb-1">Rejection Reason</p>
                            <p class="text-sm text-orange-800" x-text="inquiry.rejection_reason"></p>
                        </div>
                    </template>

                    <hr class="my-4 border-gray-200">

                    <!-- Inquiry Info -->
                    <div class="space-y-2 text-sm">
                        <div><span class="font-semibold text-gray-600">Title:</span> <span x-text="inquiry.NewsTitle" class="text-gray-800"></span></div>
                        <div><span class="font-semibold text-gray-600">Date Submitted:</span> <span x-text="inquiry.created_at" class="text-gray-800"></span></div>
                        <div><span class="font-semibold text-gray-600">Content:</span> <span x-text="inquiry.NewsContent" class="text-gray-800"></span></div>
                        <div><span class="font-semibold text-gray-600">Source:</span> <span x-text="inquiry.NewsSource" class="text-gray-800"></span></div>
                        <div><span class="font-semibold text-gray-600">Agency:</span> <span x-text="inquiry.agency_name" class="text-gray-800"></span></div>
                        <div><span class="font-semibold text-gray-600">Assigned On:</span> <span x-text="inquiry.assignment_date" class="text-gray-800"></span></div>

                        <!-- Due date -->
                        <template x-if="inquiry.due_date">
                            <div>
                                <span class="font-semibold text-gray-600">Expected Resolution:</span>
                                <span x-text="inquiry.due_date" class="text-gray-800"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Attachment -->
                    <template x-if="inquiry.attachment">
                        <div class="mt-3">
                            <span class="font-semibold text-gray-600 text-sm">Attachment:</span>
                            <a :href="'/storage/' + inquiry.attachment" target="_blank" class="text-blue-600 underline text-sm ml-1">View Attachment</a>
                        </div>
                    </template>

                    <!-- Activity Log dari table progress -->
                    <template x-if="inquiry.progress && inquiry.progress.length > 0">
                        <div class="mt-5">
                            <hr class="mb-4 border-gray-200">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Activity Log</p>
                            <div class="space-y-3">
                                <template x-for="(log, index) in inquiry.progress" :key="index">
                                    <div class="flex gap-3">
                                        <!-- dot -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                                            <template x-if="index < inquiry.progress.length - 1">
                                                <div class="w-px flex-1 bg-gray-200 my-1"></div>
                                            </template>
                                        </div>
                                        
                                        <div class="pb-3 flex-1">
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700" x-text="log.status"></span>
                                                <span class="text-xs text-gray-400" x-text="log.date"></span>
                                            </div>
                                            <p class="text-sm text-gray-700 mt-1" x-text="log.description"></p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                Officer: <span class="font-medium text-gray-500" x-text="log.officer"></span>
                                            </p>
                                            
                                            <template x-if="log.supporting_document">
                                                <a
                                                    :href="'/storage/' + log.supporting_document"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 mt-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    View Supporting Document
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const INQUIRY_STEPS = [
    { key: 'Pending',     label: 'Submitted',    sub: 'Inquiry received by MCMC' },
    { key: 'Reviewed',    label: 'Reviewed',      sub: 'MCMC reviewed your inquiry' },
    { key: 'Assigned',    label: 'Assigned',      sub: 'Forwarded to agency' },
    { key: 'In Progress', label: 'In progress',   sub: 'Agency is investigating' },
    { key: 'Resolved',    label: 'Resolved',      sub: 'Inquiry has been resolved' },
];
const ORDER = ['Pending', 'Reviewed', 'Assigned', 'In Progress', 'Resolved'];

function renderInquiryStepper(status) {
    const svgCheck = `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
    const svgX     = `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;

    function makeRow(step, state, isLast, lineState, num) {
        const circleClass = {
            done:      'bg-green-100 border-green-400 text-green-600',
            active:    'bg-blue-100 border-blue-500 text-blue-700 ring-4 ring-blue-100',
            pending:   'bg-gray-100 border-gray-300 text-gray-400',
            rejected:  'bg-orange-100 border-orange-400 text-orange-700',
            discarded: 'bg-red-100 border-red-400 text-red-600',
        }[state];

        const lineClass = {
            done:    'bg-green-300',
            warn:    'bg-orange-300',
            pending: 'bg-gray-200',
        }[lineState] ?? 'bg-gray-200';

        const iconHtml = state === 'done'      ? svgCheck
                       : state === 'discarded' ? svgX
                       : state === 'rejected'  ? svgX
                       : `<span class="text-xs font-semibold">${num}</span>`;

        const badge = state === 'active'    ? `<span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Current status</span>`
                    : state === 'rejected'  ? `<span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">Awaiting reassignment</span>`
                    : state === 'discarded' ? `<span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-600">Discarded</span>`
                    : '';

        const dim = state === 'pending';

        return `
        <div class="flex items-stretch gap-3">
            <div class="flex flex-col items-center" style="min-width:36px">
                <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${circleClass}">
                    ${iconHtml}
                </div>
                ${!isLast ? `<div class="w-0.5 flex-1 min-h-5 my-1 ${lineClass}"></div>` : ''}
            </div>
            <div class="${isLast ? 'pt-1' : 'pb-4 pt-1'}">
                <p class="text-sm font-semibold ${dim ? 'text-gray-400' : 'text-gray-800'}">${step.label}</p>
                <p class="text-xs ${dim ? 'text-gray-300' : 'text-gray-500'}">${step.sub}</p>
                ${badge}
            </div>
        </div>`;
    }

    if (status === 'Discarded') {
        const steps = [
            INQUIRY_STEPS[0],
            INQUIRY_STEPS[1],
            { key: 'Discarded', label: 'Discarded', sub: 'Inquiry discarded by MCMC — not forwarded' },
        ];
        let html = steps.map((s, i) => {
            const isLast = i === steps.length - 1;
            const state  = i < 1 ? 'done' : i === 1 ? 'done' : 'discarded';
            const line   = state === 'done' ? 'done' : 'pending';
            return makeRow(s, state, isLast, line, i + 1);
        }).join('');
        html += `<p class="mt-3 text-xs text-gray-400 bg-red-50 border border-red-100 rounded-lg px-3 py-2">Inquiry was discarded after MCMC review. No further action will be taken.</p>`;
        return html;
    }

    if (status === 'Rejected') {
        const steps = [
            ...INQUIRY_STEPS,
            { key: 'Rejected', label: 'Rejected by agency', sub: 'Returned to MCMC for reassignment' },
        ];
        let html = steps.map((s, i) => {
            const isLast = i === steps.length - 1;
            const state  = i < INQUIRY_STEPS.length ? 'done' : 'rejected';
            const line   = i < INQUIRY_STEPS.length - 1 ? 'done' : i === INQUIRY_STEPS.length - 1 ? 'warn' : 'pending';
            return makeRow(s, state, isLast, line, i + 1);
        }).join('');
        html += `<p class="mt-3 text-xs text-gray-400 bg-orange-50 border border-orange-100 rounded-lg px-3 py-2">Agency has rejected this inquiry. MCMC will reassign or review it again.</p>`;
        return html;
    }

    const ci = ORDER.indexOf(status);
    return INQUIRY_STEPS.map((s, i) => {
        const isLast = i === INQUIRY_STEPS.length - 1;
        const state  = i < ci ? 'done' : i === ci ? 'active' : 'pending';
        const line   = i < ci ? 'done' : 'pending';
        return makeRow(s, state, isLast, line, i + 1);
    }).join('');
}

document.addEventListener('click', function () {
    setTimeout(() => {
        const el = document.getElementById('inquiryStepperModal');
        if (!el) return;
        const alpineEl = document.querySelector('[x-data]');
        if (!alpineEl) return;
        const status = Alpine.$data(alpineEl)?.inquiry?.InquiryStatus;
        if (status) el.innerHTML = renderInquiryStepper(status);
    }, 50);
});
</script>
@endpush

@endsection