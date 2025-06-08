@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Unassigned Inquiries</h2>
        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 font-semibold shadow">
                {{ session('success') }}
            </div>
        @endif
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold">No.</th>
                        <th class="px-4 py-3 text-center font-semibold">Sender Name</th>
                        <th class="px-4 py-3 text-center font-semibold">Inquiry</th>
                        <th class="px-4 py-3 text-center font-semibold">Date</th>
                        <th class="px-4 py-3 text-center font-semibold">Review</th>
                        <th class="px-4 py-3 text-center font-semibold">Assign To</th>
                        <th class="px-4 py-3 text-center font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $index => $inquiry)
                    <tr class="transition hover:bg-blue-50">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-2">
                                <span class="bg-blue-200 text-blue-800 rounded-full px-3 py-1 text-xs font-bold">
                                    {{ $inquiry->publicUser ? $inquiry->publicUser->name : 'N/A' }}
                                </span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-blue-700 mb-1">{{ strtoupper($inquiry->NewsTitle) }}</div>
                            <div class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit($inquiry->NewsContent, 80) }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($inquiry->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('MCMC.InquiryReview', ['user_id' => $user->id, 'inquiry_id' => $inquiry->id]) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Review
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <!-- <form method="POST" action="{{ route('MCMC.AssignInquiry', ['user_id' => Auth::id()]) }}"> -->
                                @csrf
                                <input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">
                                <select id="select_agency_{{ $inquiry->id }}" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
                                    <option value="">-- Select Agency --</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}" {{ $inquiry->Agency_id == $agency->id ? 'selected' : '' }}>
                                            {{ strtoupper($agency->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                        <!-- <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-1.5 px-5 rounded-lg shadow transition transform hover:scale-105">
                                            Assign
                                        </button> -->
                                        <button type="button"
                                                onclick="openAssignmentModal('{{ $inquiry->id }}')"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Assign
                                        </button>
                                </td>
                            <!-- </form> -->
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-lg">
                            No inquiries available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Assignment Modal -->
<div id="assignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Assign Inquiry</h3>

            <form id="assignmentForm" method="POST" action="{{ route('MCMC.AssignInquiry', ['user_id' => Auth::id()]) }}" class="mt-4">
                @csrf
                <input type="hidden" name="inquiry_id" id="modal_inquiry_id">
                <input type="hidden" name="agency_id" id="modal_agency_id">
                
                <!-- Due Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="due_date">
                        Due Date
                    </label>
                    <input type="date" 
                           name="due_date" 
                           id="due_date"
                           required
                           min="{{ date('Y-m-d') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Comments -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="comments">
                        Comments
                    </label>
                    <textarea name="comments" 
                              id="comments"
                              required
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              rows="5"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end mt-4">
                    <button type="button" 
                            onclick="closeModal()"
                            class="mr-2 px-4 py-2 text-gray-500 bg-gray-200 rounded hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAssignmentModal(inquiryId) {
    const select = document.getElementById('select_agency_' + inquiryId);
    const agencyId = select.value;

    if (!agencyId) {
        alert('Please select an agency first');
        return;
    }

    document.getElementById('modal_inquiry_id').value = inquiryId;
    document.getElementById('modal_agency_id').value = agencyId;

    document.getElementById('assignmentModal').classList.remove('hidden');
}
function closeModal() {
     // Prevent form submission
    document.getElementById('assignmentModal').classList.add('hidden');
    document.getElementById('assignmentForm').reset();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('assignmentModal');
    const modalContent = modal.querySelector('div');
    if (event.target === modal) {
        closeModal();
    }
});
document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    console.log('Form submitted');
});
</script>
@endpush