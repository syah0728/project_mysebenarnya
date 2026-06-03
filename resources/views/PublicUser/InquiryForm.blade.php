@extends('layouts.dashboard')
@section('content')
<div class="py-12 ">
  <div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-3xl shadow-2xl p-10">
    <h2 class="text-3xl font-extrabold mb-8 text-gray-900 tracking-wide">Submit New Inquiry</h2>
    <form method="POST" action="{{ route('PublicUser.storeInquiry', ['user_id' => Auth::id()]) }}" enctype="multipart/form-data">
      @csrf

      <div class="mb-6">
        <label for="title" class="block text-gray-800 font-semibold mb-2">Title</label>
        <input
          type="text"
          id="title"
          name="title"
          class="w-full rounded-lg border border-gray-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          required
          placeholder="Enter inquiry title"
        >
      </div>

      <div class="mb-6">
        <label for="content" class="block text-gray-800 font-semibold mb-2">Content</label>
        <textarea
          id="content"
          name="content"
          rows="5"
          class="w-full rounded-lg border border-gray-400 px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          required
          placeholder="Write the details of your inquiry here..."
        ></textarea>
      </div>

      <div class="mb-6">
        <label for="source" class="block text-gray-800 font-semibold mb-2">Source</label>
        <input
          type="text"
          id="source"
          name="source"
          class="w-full rounded-lg border border-gray-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          required
          placeholder="Mention the source of your inquiry"
        >
      </div>

      <div class="mb-8">
        <label for="proof" class="block text-gray-800 font-semibold mb-2">Proof (Attachment)</label>
        <input
          type="file"
          id="proof"
          name="proof"
          accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
          class="w-full text-gray-700"
          onchange="previewFile(this)"
        >

        <div id="file-preview" class="mt-4 hidden">
          <div class="flex items-center justify-between bg-white border border-gray-300 rounded-xl px-4 py-3 shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
              <div id="file-icon" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 font-bold text-xs"></div>
              <div class="min-w-0">
                <p id="file-name" class="text-sm font-semibold text-gray-800 truncate max-w-xs"></p>
                <p id="file-size" class="text-xs text-gray-500"></p>
              </div>
            </div>
            <button type="button" onclick="viewFile()" class="shrink-0 ml-4 flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              View File
            </button>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition">
          Submit Inquiry
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
<script>
let selectedFile = null;

function previewFile(input) {
  selectedFile = input.files[0];
  const preview = document.getElementById('file-preview');
  preview.classList.toggle('hidden', !selectedFile);
  if (!selectedFile) return;

  const ext = selectedFile.name.split('.').pop().toUpperCase();
  const sizeKB = (selectedFile.size / 1024).toFixed(1);
  const sizeMB = (selectedFile.size / (1024 * 1024)).toFixed(2);

  document.getElementById('file-name').textContent = selectedFile.name;
  document.getElementById('file-size').textContent = selectedFile.size > 1024 * 1024 ? sizeMB + ' MB' : sizeKB + ' KB';
  document.getElementById('file-icon').textContent = ext;
}

function viewFile() {
  if (!selectedFile) return;

  if (selectedFile.name.endsWith('.docx')) {
    const reader = new FileReader();
    reader.onload = function(e) {
      mammoth.convertToHtml({ arrayBuffer: e.target.result })
        .then(function(result) {
          const win = window.open('', '_blank');
          win.document.write('<html><body style="font-family:sans-serif;padding:2rem;">' + result.value + '</body></html>');
          win.document.close();
        });
    };
    reader.readAsArrayBuffer(selectedFile);
  } else {
    window.open(URL.createObjectURL(selectedFile), '_blank');
  }
}
</script>
  
@endsection
