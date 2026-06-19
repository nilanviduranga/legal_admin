@extends('admin.layouts.app')
@section('title', ($act->short_title ?? $act->title))
@section('heading', 'Act ' . $act->act_no . ' / ' . $act->act_year)

@section('breadcrumb')
    <a href="{{ route('admin.acts.index') }}" class="hover:text-indigo-600 transition-colors">Acts</a>
    <span class="mx-1">›</span>
    <span class="text-gray-600 truncate max-w-xs">Act {{ $act->act_no }}/{{ $act->act_year }}</span>
@endsection

@section('action_button')
<div class="flex gap-2">
    <a href="{{ route('admin.acts.edit', $act) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Edit Act
    </a>
    <a href="{{ route('admin.acts.nodes.index', $act) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Full Tree
    </a>
</div>
@endsection

@section('content')

{{-- Act metadata strip --}}
<div class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <h1 class="text-base font-semibold text-gray-900 mb-3 leading-snug">{{ $act->title }}</h1>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <dt class="text-xs font-medium text-gray-400 mb-0.5">Gazette</dt>
            <dd class="text-gray-800 font-medium">
                @if($act->gazette)
                    <a href="{{ route('admin.gazettes.show', $act->gazette) }}"
                       class="text-indigo-600 hover:text-indigo-800">{{ $act->gazette->gazette_no }}</a>
                @else
                    <span class="text-gray-300">—</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-400 mb-0.5">Commencement</dt>
            <dd class="text-gray-800">{{ $act->commencement_date?->format('d M Y') ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-400 mb-0.5">Status</dt>
            <dd>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                    {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
                       ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ ucfirst($act->status) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-400 mb-0.5">Short Title</dt>
            <dd class="text-gray-800">{{ $act->short_title ?? '—' }}</dd>
        </div>
    </div>
</div>

{{-- ── Inline Add / Edit Form ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" id="part-form-panel">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-2 h-2 rounded-full bg-indigo-500" id="pf-dot"></div>
        <h2 class="text-sm font-semibold text-gray-700" id="pf-heading">Add Part / Top-Level Node</h2>
    </div>

    <div id="pf-errors" class="hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type <span class="font-normal text-gray-400">(auto)</span></label>
            <div class="flex items-center h-9 px-3 rounded-xl border border-gray-200 bg-gray-100">
                <span id="pf-type-badge" class="inline-flex rounded px-2 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-800">PART</span>
            </div>
            <input type="hidden" id="pf-type" value="PART">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_type"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">No. <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="pf-no" placeholder="e.g. I, 1, A"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_no"></p>
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Heading <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="pf-heading-input" placeholder="e.g. Preliminary Provisions"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="heading"></p>
        </div>
        <div class="col-span-2 sm:col-span-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Text Content <span class="font-normal text-gray-400">(for leaf nodes)</span></label>
            <textarea id="pf-text" rows="3" placeholder="Substantive legal text…"
                      class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors resize-y font-mono"></textarea>
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="text_content"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sort Order</label>
            <input type="number" id="pf-sort" value="{{ $nextSortOrder }}" min="0"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="sort_order"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select id="pf-status"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="active">Active</option>
                <option value="amended">Amended</option>
                <option value="repealed">Repealed</option>
            </select>
        </div>
        <div class="col-span-2 flex items-end gap-3 pt-1">
            <button type="button" id="pf-submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Add
            </button>
            <button type="button" id="pf-cancel"
                    class="hidden rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- ── Top-level parts / nodes ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Parts &amp; Top-Level Nodes</h2>
            <p class="text-xs text-gray-400 mt-0.5">Click <strong>Edit</strong> to update inline · Click <strong>Manage →</strong> to add and manage children</p>
        </div>
        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600" id="part-count">
            {{ $act->topLevelNodes->count() }}
        </span>
    </div>

    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-36">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">No.</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Heading / Content</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Order</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Status</th>
                <th class="px-4 py-3 w-48"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="parts-tbody">
            @forelse($act->topLevelNodes as $node)
            @php
            $badge = match($node->node_type) {
                'PART'         => 'bg-indigo-100 text-indigo-800',
                'CHAPTER'      => 'bg-purple-100 text-purple-800',
                'SECTION'      => 'bg-blue-100 text-blue-800',
                'SUBSECTION'   => 'bg-cyan-100 text-cyan-800',
                'PARAGRAPH'    => 'bg-teal-100 text-teal-800',
                'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
                'ITEM'         => 'bg-lime-100 text-lime-800',
                'SCHEDULE'     => 'bg-amber-100 text-amber-800',
                'PREAMBLE'     => 'bg-orange-100 text-orange-800',
                default        => 'bg-gray-100 text-gray-600',
            };
            $preview = $node->heading ?? Str::limit($node->text_content ?? '', 60);
            $tooltip = $node->heading
                ? ($node->text_content ? $node->heading . "\n\n" . $node->text_content : $node->heading)
                : ($node->text_content ?? '');
            @endphp
            <tr id="part-row-{{ $node->node_id }}"
                class="hover:bg-gray-50 transition-colors"
                data-node="{{ json_encode([
                    'node_id'      => $node->node_id,
                    'node_type'    => $node->node_type,
                    'node_no'      => $node->node_no,
                    'heading'      => $node->heading,
                    'text_content' => $node->text_content,
                    'sort_order'   => $node->sort_order,
                    'level'        => $node->level,
                    'status'       => $node->status,
                ]) }}">
                <td class="whitespace-nowrap px-5 py-3.5">
                    <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $node->node_type }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">{{ $node->node_no ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs">
                    <span class="line-clamp-2 cursor-help" data-tooltip="{{ $tooltip }}">{{ $preview ?: '—' }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">{{ $node->sort_order }}</td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $node->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($node->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($node->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.nodes.show', $node) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Manage →</a>
                        <button type="button" onclick="partEdit(this.closest('tr'))"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.nodes.destroy', $node) }}" class="inline"
                              onsubmit="return confirm('Delete this node and all its children? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="parts-empty">
                <td colspan="6" class="px-5 py-14 text-center text-sm text-gray-400">
                    No parts or top-level nodes yet.<br>
                    <span class="text-xs text-gray-300">Use the form above to add the first one.</span>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
(function () {
    const csrf      = document.querySelector('meta[name="csrf-token"]').content;
    const ACT_ID    = {{ $act->act_id }};
    const BASE_SORT = {{ $nextSortOrder }};
    let editId      = null;
    let nextSort    = BASE_SORT;

    const fType      = document.getElementById('pf-type');
    const fTypeBadge = document.getElementById('pf-type-badge');
    const fNo     = document.getElementById('pf-no');
    const fHdg    = document.getElementById('pf-heading-input');
    const fText   = document.getElementById('pf-text');
    const fSort   = document.getElementById('pf-sort');
    const fStatus = document.getElementById('pf-status');
    const submitBtn = document.getElementById('pf-submit');
    const cancelBtn = document.getElementById('pf-cancel');
    const formHdg   = document.getElementById('pf-heading');
    const dot       = document.getElementById('pf-dot');
    const errBox    = document.getElementById('pf-errors');

    const TYPE_CLS = {
        PART:'bg-indigo-100 text-indigo-800', CHAPTER:'bg-purple-100 text-purple-800',
        SECTION:'bg-blue-100 text-blue-800', SUBSECTION:'bg-cyan-100 text-cyan-800',
        PARAGRAPH:'bg-teal-100 text-teal-800', SUBPARAGRAPH:'bg-green-100 text-green-800',
        ITEM:'bg-lime-100 text-lime-800', SCHEDULE:'bg-amber-100 text-amber-800',
        PREAMBLE:'bg-orange-100 text-orange-800', DEFINITIONS:'bg-gray-100 text-gray-600',
    };

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function setTypeBadge(type) {
        fType.value = type;
        fTypeBadge.textContent = type;
        fTypeBadge.className = 'inline-flex rounded px-2 py-0.5 text-xs font-semibold ' + (TYPE_CLS[type] ?? 'bg-gray-100 text-gray-600');
    }

    function typeBadge(type) {
        const cls = TYPE_CLS[type] ?? 'bg-gray-100 text-gray-600';
        return `<span class="inline-flex rounded px-2 py-0.5 text-xs font-medium ${cls}">${esc(type)}</span>`;
    }

    function statusBadge(s) {
        const map = {active:'bg-green-100 text-green-700', repealed:'bg-red-100 text-red-700', amended:'bg-amber-100 text-amber-700'};
        const cls = map[s] ?? 'bg-gray-100 text-gray-500';
        return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${cls}">${esc(s.charAt(0).toUpperCase()+s.slice(1))}</span>`;
    }

    function clearErrors() {
        errBox.classList.add('hidden'); errBox.innerHTML = '';
        document.querySelectorAll('#part-form-panel .field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
    }

    function showErrors(data) {
        const fe = data.errors ?? {};
        const all = Object.values(fe).flat();
        if (all.length) { errBox.innerHTML = all.map(m => `<p>${esc(m)}</p>`).join(''); errBox.classList.remove('hidden'); }
        document.querySelectorAll('#part-form-panel .field-error').forEach(el => {
            const msgs = fe[el.dataset.field];
            if (msgs?.length) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
        });
    }

    function resetForm() {
        setTypeBadge('PART'); fNo.value = ''; fHdg.value = ''; fText.value = '';
        fSort.value = nextSort; fStatus.value = 'active';
        editId = null;
        formHdg.textContent = 'Add Part / Top-Level Node';
        dot.className = 'w-2 h-2 rounded-full bg-indigo-500';
        submitBtn.textContent = 'Add';
        cancelBtn.classList.add('hidden');
        clearErrors();
    }

    function buildRow(n) {
        const preview  = n.heading ?? (n.text_content ? n.text_content.substring(0, 60) + (n.text_content.length > 60 ? '…' : '') : '—');
        const tooltip  = (n.heading && n.text_content) ? n.heading + '\n\n' + n.text_content : (n.heading ?? n.text_content ?? '');
        const nodeJson = esc(JSON.stringify({
            node_id: n.node_id, node_type: n.node_type, node_no: n.node_no,
            heading: n.heading, text_content: n.text_content,
            sort_order: n.sort_order, level: n.level, status: n.status,
        }));
        const delMsg = esc('Delete this node and all its children? This cannot be undone.');
        return `
<tr id="part-row-${n.node_id}" class="hover:bg-gray-50 transition-colors" data-node="${nodeJson}">
  <td class="whitespace-nowrap px-5 py-3.5">${typeBadge(n.node_type)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">${esc(n.node_no ?? '—')}</td>
  <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs"><span class="line-clamp-2 cursor-help" data-tooltip="${esc(tooltip)}">${esc(preview)}</span></td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">${esc(n.sort_order)}</td>
  <td class="whitespace-nowrap px-4 py-3.5">${statusBadge(n.status)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-right">
    <div class="flex items-center justify-end gap-3">
      <a href="/admin/nodes/${n.node_id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Manage →</a>
      <button type="button" onclick="partEdit(this.closest('tr'))" class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
      <form method="POST" action="/admin/nodes/${n.node_id}" class="inline" onsubmit="return confirm('${delMsg}')">
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
            act_id:         ACT_ID,
            parent_node_id: null,
            node_type:      fType.value,
            node_no:        fNo.value.trim() || null,
            heading:        fHdg.value.trim() || null,
            text_content:   fText.value.trim() || null,
            sort_order:     parseInt(fSort.value, 10) || 0,
            level:          1,
            status:         fStatus.value,
        };

        const url    = editId ? `/admin/nodes/${editId}` : '/admin/nodes';
        const method = editId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });

            if (resp.ok) {
                const node  = await resp.json();
                const tbody = document.getElementById('parts-tbody');
                const empty = document.getElementById('parts-empty');

                if (editId) {
                    const existing = document.getElementById(`part-row-${editId}`);
                    if (existing) existing.outerHTML = buildRow(node);
                } else {
                    if (empty) empty.remove();
                    tbody.insertAdjacentHTML('beforeend', buildRow(node));
                    nextSort = payload.sort_order + 1;
                    const cnt = document.getElementById('part-count');
                    if (cnt) cnt.textContent = (parseInt(cnt.textContent, 10) || 0) + 1;
                }
                resetForm();
            } else {
                showErrors(await resp.json());
            }
        } catch {
            errBox.innerHTML = '<p>Network error. Please try again.</p>';
            errBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });

    cancelBtn.addEventListener('click', resetForm);

    window.partEdit = function (row) {
        const n   = JSON.parse(row.dataset.node);
        editId    = n.node_id;
        setTypeBadge(n.node_type);
        fNo.value     = n.node_no ?? '';
        fHdg.value    = n.heading ?? '';
        fText.value   = n.text_content ?? '';
        fSort.value   = n.sort_order;
        fStatus.value = n.status;

        formHdg.textContent = 'Edit Node';
        dot.className = 'w-2 h-2 rounded-full bg-amber-400';
        submitBtn.textContent = 'Update';
        cancelBtn.classList.remove('hidden');
        clearErrors();
        document.getElementById('part-form-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
})();
</script>

@endsection
