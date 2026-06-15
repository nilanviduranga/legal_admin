@extends('admin.layouts.app')
@section('title', 'Import PDF')
@section('heading', 'Import Act from PDF')

@section('content')
<div class="max-w-3xl">
    {{-- Upload form --}}
    <div id="upload-section" class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Upload PDF</h2>
        <form id="import-form" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" class="hidden" onchange="handleFileSelect(this)">
                <label for="pdf_file" class="cursor-pointer">
                    <div class="text-gray-400 text-sm">
                        <span class="font-medium text-indigo-600">Click to upload</span> a PDF Act document
                    </div>
                    <p id="file-name" class="text-xs text-gray-400 mt-1">No file selected</p>
                </label>
            </div>
            <button type="button" onclick="processImport()"
                    class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    id="process-btn" disabled>
                Parse with AI
            </button>
        </form>
        <div id="loading" class="hidden mt-4 flex items-center gap-2 text-sm text-gray-500">
            <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Parsing PDF with Claude AI…
        </div>
    </div>

    {{-- Preview section (shown after parse) --}}
    <div id="preview-section" class="hidden bg-white shadow rounded-lg p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Preview — Confirm before saving</h2>
        <div id="preview-content" class="text-sm text-gray-700 mb-4"></div>
        <form id="confirm-form" method="POST" action="{{ route('admin.import.confirm') }}">
            @csrf
            <input type="hidden" name="data" id="confirm-data">
            <button type="submit" class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                Confirm & Import
            </button>
            <button type="button" onclick="resetForm()" class="ml-3 rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">
                Start Over
            </button>
        </form>
    </div>
</div>

<script>
function handleFileSelect(input) {
    document.getElementById('file-name').textContent = input.files[0]?.name ?? 'No file selected';
    document.getElementById('process-btn').disabled = !input.files.length;
}

async function processImport() {
    const form = document.getElementById('import-form');
    const formData = new FormData(form);
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('process-btn').disabled = true;

    try {
        const res = await fetch('{{ route('admin.import.process') }}', { method: 'POST', body: formData });
        const json = await res.json();
        showPreview(json.preview);
    } catch (e) {
        alert('Parse failed: ' + e.message);
    } finally {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('process-btn').disabled = false;
    }
}

function showPreview(data) {
    document.getElementById('preview-section').classList.remove('hidden');
    document.getElementById('upload-section').classList.add('hidden');
    document.getElementById('confirm-data').value = JSON.stringify(data);

    const act = data.act || {};
    const nodes = data.nodes || [];
    const refs = data.cross_references || [];

    document.getElementById('preview-content').innerHTML = `
        <div class="mb-3 p-3 bg-gray-50 rounded">
            <strong>Act No. ${act.act_no} of ${act.act_year}</strong> — ${act.title || ''}
        </div>
        <div class="mb-2 text-xs text-gray-500">${countNodes(nodes)} nodes detected · ${refs.length} cross-references detected</div>
        <pre class="bg-gray-50 rounded p-3 text-xs overflow-auto max-h-64">${JSON.stringify(data, null, 2)}</pre>
    `;
}

function countNodes(nodes, acc = 0) {
    for (const n of nodes) {
        acc++;
        if (n.children) acc = countNodes(n.children, acc);
    }
    return acc;
}

function resetForm() {
    document.getElementById('preview-section').classList.add('hidden');
    document.getElementById('upload-section').classList.remove('hidden');
}
</script>
@endsection
