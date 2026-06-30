@extends('admin.layouts.app')
@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- ─── Database ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Database</h2>
            <p class="text-xs text-gray-400 mt-0.5">Connection status and row counts</p>
        </div>
        <div class="px-6 py-5 space-y-5">
            {{-- Connection --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Connection</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ config('database.default') }} · {{ config('database.connections.' . config('database.default') . '.host', 'sqlite') }}</p>
                </div>
                @if($dbStatus === 'connected')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold px-3 py-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Connected
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold px-3 py-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Error
                </span>
                @endif
            </div>

            {{-- Row counts --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['table' => 'gazettes',         'label' => 'Gazettes'],
                    ['table' => 'acts',              'label' => 'Acts'],
                    ['table' => 'legal_nodes',       'label' => 'Legal Nodes (total)'],
                    ['table' => 'soft_deleted',      'label' => 'Soft-deleted Nodes'],
                    ['table' => 'cross_references',  'label' => 'Cross-references'],
                    ['table' => 'case_laws',         'label' => 'Law Cases'],
                ] as $row)
                <div class="rounded-xl bg-gray-50 px-4 py-3">
                    <p class="text-xs text-gray-500">{{ $row['label'] }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-0.5">{{ number_format($tableCounts[$row['table']]) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Application ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Application</h2>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="font-medium text-gray-500">Laravel</dt>
                    <dd class="mt-0.5 text-gray-900">{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">PHP</dt>
                    <dd class="mt-0.5 text-gray-900">{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Environment</dt>
                    <dd class="mt-0.5">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                            {{ app()->environment('production') ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ app()->environment() }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Debug Mode</dt>
                    <dd class="mt-0.5">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                            {{ config('app.debug') ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ config('app.debug') ? 'On' : 'Off' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">App URL</dt>
                    <dd class="mt-0.5 text-gray-900 text-xs break-all">{{ config('app.url') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Timezone</dt>
                    <dd class="mt-0.5 text-gray-900">{{ config('app.timezone') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- ─── Vector Store ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Vector Store</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                RAG service: <span class="font-mono text-indigo-600">{{ env('RAG_URL', 'http://127.0.0.1:8000') }}</span>
                &nbsp;·&nbsp; change <code class="text-gray-500">RAG_URL</code> in <code class="text-gray-500">.env</code>
            </p>
        </div>
        <div class="px-6 py-5 space-y-3">

            {{-- 1. Rebuild Statutes --}}
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-700">Rebuild Statutes</p>
                    <p class="text-xs text-gray-400 mt-0.5">Statute FAISS index only &mdash; <code class="text-gray-500">POST /vectordb/rebuild-statutes</code></p>
                </div>
                <button type="button" data-rag-action="{{ route('admin.settings.vectordb.rebuild-statutes') }}"
                        class="rag-btn rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Rebuild
                </button>
            </div>

            {{-- 2. Rebuild Case Laws --}}
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-700">Rebuild Case Laws</p>
                    <p class="text-xs text-gray-400 mt-0.5">Case law FAISS index only &mdash; <code class="text-gray-500">POST /vectordb/rebuild-caselaws</code></p>
                </div>
                <button type="button" data-rag-action="{{ route('admin.settings.vectordb.rebuild-caselaws') }}"
                        class="rag-btn rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Rebuild
                </button>
            </div>

            {{-- 3. Rebuild Act Metadata --}}
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-700">Rebuild Act Metadata</p>
                    <p class="text-xs text-gray-400 mt-0.5">AI summaries + act-level FAISS index &mdash; <code class="text-gray-500">POST /vectordb/rebuild-act-metadata</code></p>
                </div>
                <button type="button" data-rag-action="{{ route('admin.settings.vectordb.rebuild-act-metadata') }}"
                        class="rag-btn rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Rebuild
                </button>
            </div>

            {{-- 4. Warm Structure Cache --}}
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-700">Warm Structure Cache</p>
                    <p class="text-xs text-gray-400 mt-0.5">Pre-compute act statistics &mdash; <code class="text-gray-500">POST /vectordb/warm-structure-cache</code></p>
                </div>
                <button type="button" data-rag-action="{{ route('admin.settings.vectordb.warm-structure-cache') }}"
                        class="rag-btn rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Warm
                </button>
            </div>

            {{-- Result area --}}
            <div id="rag-result" class="hidden rounded-xl px-4 py-3 text-xs font-mono whitespace-pre-wrap break-all"></div>
        </div>
    </div>

    {{-- ─── Account ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Account</h2>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold text-sm uppercase">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const resultBox = document.getElementById('rag-result');

    document.querySelectorAll('.rag-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const url      = this.dataset.ragAction;
            const origText = this.textContent.trim();

            document.querySelectorAll('.rag-btn').forEach(b => b.disabled = true);
            this.textContent = 'Running…';

            resultBox.className = 'rounded-xl px-4 py-3 text-xs font-mono whitespace-pre-wrap break-all bg-blue-50 text-blue-700';
            resultBox.textContent = 'Calling RAG service… this may take a minute.';

            try {
                const res  = await fetch(url, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const data = await res.json();

                resultBox.className = res.ok
                    ? 'rounded-xl px-4 py-3 text-xs font-mono whitespace-pre-wrap break-all bg-green-50 text-green-700'
                    : 'rounded-xl px-4 py-3 text-xs font-mono whitespace-pre-wrap break-all bg-red-50 text-red-700';
                resultBox.textContent = JSON.stringify(data, null, 2);
            } catch (err) {
                resultBox.className = 'rounded-xl px-4 py-3 text-xs font-mono whitespace-pre-wrap break-all bg-red-50 text-red-700';
                resultBox.textContent = 'Network error: ' + err.message;
            } finally {
                document.querySelectorAll('.rag-btn').forEach(b => b.disabled = false);
                this.textContent = origText;
            }
        });
    });
})();
</script>
@endpush
