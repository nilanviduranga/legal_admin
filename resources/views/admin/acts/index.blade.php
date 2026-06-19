@extends('admin.layouts.app')
@section('title', 'Acts')
@section('heading', 'Acts')

@section('content')

{{-- Add / Edit Form Panel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" id="act-form-panel">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-2 h-2 rounded-full bg-indigo-500" id="act-form-dot"></div>
        <h2 class="text-sm font-semibold text-gray-700" id="act-form-heading">Add New Act</h2>
    </div>

    <div id="act-form-errors" class="hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Gazette</label>
            <select id="f-act-gazette-id"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="">— Select gazette —</option>
                @foreach($gazettes as $gazette)
                    <option value="{{ $gazette->gazette_id }}">
                        {{ $gazette->gazette_no }} — {{ $gazette->gazette_date->format('d M Y') }}
                    </option>
                @endforeach
            </select>
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="gazette_id"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Act No</label>
            <input type="number" id="f-act-no" placeholder="e.g. 9" min="1"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="act_no"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
            <input type="number" id="f-act-year" placeholder="e.g. 2024" min="1900" max="2099"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="act_year"></p>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
            <input type="text" id="f-act-title" placeholder="Full act title…"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="title"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Short Title</label>
            <input type="text" id="f-act-short-title" placeholder="Optional short title…"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="short_title"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Commencement Date</label>
            <input type="date" id="f-act-commencement"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="commencement_date"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select id="f-act-status"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="active">Active</option>
                <option value="repealed">Repealed</option>
                <option value="amended">Amended</option>
            </select>
        </div>
        <div class="sm:col-span-3 flex items-end gap-3 pt-1">
            <button type="button" id="act-submit-btn"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Add
            </button>
            <button type="button" id="act-cancel-btn"
                    class="hidden rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-56">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Title or act number…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
        <select name="year"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All years</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="repealed" {{ request('status') === 'repealed' ? 'selected' : '' }}>Repealed</option>
            <option value="amended"  {{ request('status') === 'amended'  ? 'selected' : '' }}>Amended</option>
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['q','year','status']))
    <a href="{{ route('admin.acts.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3" id="act-count">{{ $acts->total() }} act(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Act / Year</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gazette</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Commencement</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nodes</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="acts-tbody">
            @forelse($acts as $act)
            <tr id="act-row-{{ $act->act_id }}"
                data-act-id="{{ $act->act_id }}"
                data-gazette-id="{{ $act->gazette_id }}"
                data-act-no="{{ $act->act_no }}"
                data-act-year="{{ $act->act_year }}"
                data-title="{{ $act->title }}"
                data-short-title="{{ $act->short_title }}"
                data-commencement-date="{{ $act->commencement_date?->format('Y-m-d') }}"
                data-status="{{ $act->status }}"
                class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-5 py-3.5">
                    <span class="text-sm font-bold text-gray-900">{{ $act->act_no }}</span>
                    <span class="text-xs text-gray-400 ml-1">/ {{ $act->act_year }}</span>
                </td>
                <td class="px-5 py-3.5 max-w-xs">
                    <p class="text-sm text-gray-800 line-clamp-2 cursor-help" data-tooltip="{{ $act->title }}">{{ $act->title }}</p>
                    @if($act->replaced_by_act_id)
                    <span class="inline-flex items-center gap-1 text-xs text-red-500 mt-0.5">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        Replaced
                    </span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">
                    @if($act->gazette)
                    <a href="{{ route('admin.gazettes.show', $act->gazette) }}"
                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ $act->gazette->gazette_no }}
                    </a>
                    @else
                    <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">
                    {{ $act->commencement_date?->format('d M Y') ?? '—' }}
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($act->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ $act->legal_nodes_count }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.acts.show', $act) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
                        <a href="{{ route('admin.acts.nodes.index', $act) }}"
                           class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Nodes</a>
                        <button type="button"
                                onclick="actEditRow(this.closest('tr'))"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.acts.destroy', $act) }}" class="inline"
                              onsubmit="return confirm('Delete Act {{ $act->act_no }}/{{ $act->act_year }} and all its nodes? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="acts-empty-row">
                <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','year','status']))
                        No acts match the current filters.
                        <a href="{{ route('admin.acts.index') }}" class="ml-1 text-indigo-600">Clear filters</a>
                    @else
                        No acts yet. Use the form above to add one.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $acts->withQueryString()->links() }}</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let editId = null;

    const fGazette      = document.getElementById('f-act-gazette-id');
    const fNo           = document.getElementById('f-act-no');
    const fYear         = document.getElementById('f-act-year');
    const fTitle        = document.getElementById('f-act-title');
    const fShortTitle   = document.getElementById('f-act-short-title');
    const fCommencement = document.getElementById('f-act-commencement');
    const fStatus       = document.getElementById('f-act-status');
    const submitBtn     = document.getElementById('act-submit-btn');
    const cancelBtn     = document.getElementById('act-cancel-btn');
    const heading       = document.getElementById('act-form-heading');
    const dot           = document.getElementById('act-form-dot');
    const errorsBox     = document.getElementById('act-form-errors');

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function fmtDate(ymd) {
        if (!ymd) return '—';
        const [y, m, d] = ymd.split('-');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return `${parseInt(d, 10)} ${months[parseInt(m, 10) - 1]} ${y}`;
    }

    function clearErrors() {
        errorsBox.classList.add('hidden');
        errorsBox.innerHTML = '';
        document.querySelectorAll('#act-form-panel .field-error').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function showErrors(data) {
        const fieldErrors = data.errors ?? {};
        const all = Object.values(fieldErrors).flat();
        if (all.length) {
            errorsBox.innerHTML = all.map(m => `<p>${esc(m)}</p>`).join('');
            errorsBox.classList.remove('hidden');
        }
        document.querySelectorAll('#act-form-panel .field-error').forEach(el => {
            const msgs = fieldErrors[el.dataset.field];
            if (msgs?.length) {
                el.textContent = msgs[0];
                el.classList.remove('hidden');
            }
        });
    }

    function resetForm() {
        fGazette.value      = '';
        fNo.value           = '';
        fYear.value         = '';
        fTitle.value        = '';
        fShortTitle.value   = '';
        fCommencement.value = '';
        fStatus.value       = 'active';
        editId = null;
        heading.textContent = 'Add New Act';
        dot.classList.remove('bg-amber-400');
        dot.classList.add('bg-indigo-500');
        submitBtn.textContent = 'Add';
        cancelBtn.classList.add('hidden');
        clearErrors();
    }

    function statusBadge(status) {
        const map = {
            active:   'bg-green-100 text-green-700',
            repealed: 'bg-red-100 text-red-700',
            amended:  'bg-amber-100 text-amber-700',
        };
        const cls = map[status] ?? 'bg-gray-100 text-gray-500';
        const label = status.charAt(0).toUpperCase() + status.slice(1);
        return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${cls}">${label}</span>`;
    }

    function buildRow(a) {
        const gazette    = a.gazette;
        const gazetteCell = gazette
            ? `<a href="/admin/gazettes/${gazette.gazette_id}" class="text-indigo-600 hover:text-indigo-800 font-medium">${esc(gazette.gazette_no)}</a>`
            : `<span class="text-gray-300">—</span>`;
        const commDate = a.commencement_date ? fmtDate(a.commencement_date) : '—';
        const aTitle   = esc(a.title);
        const confirmMsg = esc(`Delete Act ${a.act_no}/${a.act_year} and all its nodes? This cannot be undone.`);

        return `
<tr id="act-row-${a.act_id}"
    data-act-id="${a.act_id}"
    data-gazette-id="${esc(a.gazette_id ?? '')}"
    data-act-no="${esc(a.act_no)}"
    data-act-year="${esc(a.act_year)}"
    data-title="${aTitle}"
    data-short-title="${esc(a.short_title ?? '')}"
    data-commencement-date="${esc(a.commencement_date ?? '')}"
    data-status="${esc(a.status)}"
    class="hover:bg-gray-50 transition-colors">
  <td class="whitespace-nowrap px-5 py-3.5">
    <span class="text-sm font-bold text-gray-900">${esc(a.act_no)}</span>
    <span class="text-xs text-gray-400 ml-1">/ ${esc(a.act_year)}</span>
  </td>
  <td class="px-5 py-3.5 max-w-xs">
    <p class="text-sm text-gray-800 line-clamp-2 cursor-help" data-tooltip="${aTitle}">${aTitle}</p>
  </td>
  <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">${gazetteCell}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">${commDate}</td>
  <td class="whitespace-nowrap px-4 py-3.5">${statusBadge(a.status)}</td>
  <td class="whitespace-nowrap px-4 py-3.5">
    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">${a.legal_nodes_count ?? 0}</span>
  </td>
  <td class="whitespace-nowrap px-4 py-3.5 text-right">
    <div class="flex items-center justify-end gap-3">
      <a href="/admin/acts/${a.act_id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
      <a href="/admin/acts/${a.act_id}/nodes" class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Nodes</a>
      <button type="button" onclick="actEditRow(this.closest('tr'))" class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
      <form method="POST" action="/admin/acts/${a.act_id}" class="inline" onsubmit="return confirm('${confirmMsg}')">
        <input type="hidden" name="_token" value="${esc(csrf)}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
      </form>
    </div>
  </td>
</tr>`;
    }

    submitBtn.addEventListener('click', async function () {
        clearErrors();
        submitBtn.disabled = true;

        const payload = {
            gazette_id:        fGazette.value || null,
            act_no:            fNo.value ? parseInt(fNo.value, 10) : null,
            act_year:          fYear.value ? parseInt(fYear.value, 10) : null,
            title:             fTitle.value.trim(),
            short_title:       fShortTitle.value.trim() || null,
            commencement_date: fCommencement.value || null,
            status:            fStatus.value,
        };

        const url    = editId ? `/admin/acts/${editId}` : '/admin/acts';
        const method = editId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });

            if (resp.ok) {
                const act    = await resp.json();
                const tbody  = document.getElementById('acts-tbody');
                const emptyRow = document.getElementById('acts-empty-row');

                if (editId) {
                    const existing = document.getElementById(`act-row-${editId}`);
                    if (existing) existing.outerHTML = buildRow(act);
                } else {
                    if (emptyRow) emptyRow.remove();
                    tbody.insertAdjacentHTML('afterbegin', buildRow(act));
                    const countEl = document.getElementById('act-count');
                    if (countEl) {
                        const cur = parseInt(countEl.textContent, 10) || 0;
                        countEl.textContent = (cur + 1) + ' act(s)';
                    }
                }
                resetForm();
            } else {
                showErrors(await resp.json());
            }
        } catch {
            errorsBox.innerHTML = '<p>Network error. Please try again.</p>';
            errorsBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });

    cancelBtn.addEventListener('click', resetForm);

    window.actEditRow = function (row) {
        editId = row.dataset.actId;
        fGazette.value      = row.dataset.gazetteId ?? '';
        fNo.value           = row.dataset.actNo;
        fYear.value         = row.dataset.actYear;
        fTitle.value        = row.dataset.title;
        fShortTitle.value   = row.dataset.shortTitle ?? '';
        fCommencement.value = row.dataset.commencementDate ?? '';
        fStatus.value       = row.dataset.status;

        heading.textContent = 'Edit Act';
        dot.classList.remove('bg-indigo-500');
        dot.classList.add('bg-amber-400');
        submitBtn.textContent = 'Update';
        cancelBtn.classList.remove('hidden');
        clearErrors();
        document.getElementById('act-form-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
})();
</script>

@endsection
