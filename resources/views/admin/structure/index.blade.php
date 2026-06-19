@extends('admin.layouts.app')
@section('title', 'Full Legal Structure')
@section('heading', 'Full Legal Structure')

@section('breadcrumb')
    <span class="text-gray-600">Full Structure</span>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Search bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input id="struct-search" type="text" autocomplete="off"
                   placeholder="Search acts, headings, section numbers, content…"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 pl-9 pr-9 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <button id="struct-clear"
                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 text-xs font-bold leading-none transition-colors">×</button>
        </div>
    </div>

    {{-- Search results panel --}}
    <div id="search-panel" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"></div>

    {{-- Document tree --}}
    <div id="tree-panel" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Legal Acts &amp; Full Text</h2>
                <p class="text-xs text-gray-400 mt-0.5">Click <strong>›</strong> to expand — text content shown inline · <strong>→</strong> to manage</p>
            </div>
            <span class="shrink-0 text-xs text-gray-400">{{ $acts->count() }} act(s)</span>
        </div>

        <div id="acts-list">
            @forelse($acts as $act)
            @php
            $sCls = match($act->status ?? 'active') {
                'active'   => 'bg-green-100 text-green-700',
                'repealed' => 'bg-red-100 text-red-700',
                default    => 'bg-amber-100 text-amber-700',
            };
            @endphp
            <div class="s-item border-b border-gray-100 last:border-0"
                 data-kind="act" data-id="{{ $act->act_id }}"
                 data-has-ch="{{ $act->legal_nodes_count > 0 ? '1' : '0' }}">

                {{-- Act header row --}}
                <div class="s-row group flex items-start gap-3 px-6 py-4 cursor-pointer hover:bg-indigo-50/50 transition-colors select-none"
                     onclick="sToggle(this.closest('.s-item'))">
                    <span class="s-chev mt-1 inline-block shrink-0 w-4 text-center font-bold text-indigo-400 text-lg leading-none transition-transform duration-200"
                          style="{{ $act->legal_nodes_count > 0 ? '' : 'visibility:hidden' }}">›</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">Act No. {{ $act->act_no }} / {{ $act->act_year }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $sCls }}">{{ ucfirst($act->status ?? 'active') }}</span>
                            @if($act->legal_nodes_count)
                            <span class="text-xs text-gray-400">{{ $act->legal_nodes_count }} nodes</span>
                            @endif
                        </div>
                        <p class="text-base font-semibold text-gray-900 leading-snug">{{ $act->title }}</p>
                        @if($act->short_title && $act->short_title !== $act->title)
                        <p class="text-xs text-gray-400 mt-0.5 italic">{{ $act->short_title }}</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.acts.show', $act) }}" onclick="event.stopPropagation()"
                       class="shrink-0 mt-1 text-xs font-medium text-indigo-600 hover:text-indigo-900 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                       Manage →
                    </a>
                </div>

                {{-- Act children --}}
                <div class="s-children hidden ml-7 pl-4 border-l-2 border-indigo-100"></div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-sm text-gray-400">No acts yet.</div>
            @endforelse
        </div>
    </div>

</div>

<script>
(function () {
    const TYPE_CLS = {
        PART:'bg-indigo-100 text-indigo-800', CHAPTER:'bg-purple-100 text-purple-800',
        SECTION:'bg-blue-100 text-blue-800', SUBSECTION:'bg-cyan-100 text-cyan-800',
        PARAGRAPH:'bg-teal-100 text-teal-800', SUBPARAGRAPH:'bg-green-100 text-green-800',
        ITEM:'bg-lime-100 text-lime-800', SCHEDULE:'bg-amber-100 text-amber-800',
        PREAMBLE:'bg-orange-100 text-orange-800', DEFINITIONS:'bg-gray-100 text-gray-600',
    };
    const STATUS_CLS = {
        active:'bg-green-100 text-green-700', repealed:'bg-red-100 text-red-700', amended:'bg-amber-100 text-amber-700',
    };
    // Left-border accent per type
    const TYPE_BORDER = {
        PART:'border-indigo-200', CHAPTER:'border-purple-200',
        SECTION:'border-blue-200', SUBSECTION:'border-cyan-200',
        PARAGRAPH:'border-teal-200', SUBPARAGRAPH:'border-green-200',
        ITEM:'border-lime-200', SCHEDULE:'border-amber-200',
        PREAMBLE:'border-orange-200', DEFINITIONS:'border-gray-200',
    };

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── expand / collapse with animation ─────────────────────── */
    window.sToggle = async function (item) {
        if (item.dataset.hasCh === '0') return;

        const isOpen   = item.classList.contains('s-open');
        const chev     = item.querySelector(':scope > .s-row .s-chev');
        const children = item.querySelector(':scope > .s-children');

        if (isOpen) {
            item.classList.remove('s-open');
            chev.style.transform = '';
            children.style.maxHeight = children.scrollHeight + 'px';
            children.style.overflow  = 'hidden';
            children.style.transition = 'max-height 0.22s ease, opacity 0.18s ease';
            requestAnimationFrame(() => {
                children.style.maxHeight = '0';
                children.style.opacity   = '0';
            });
            children.addEventListener('transitionend', () => {
                children.classList.add('hidden');
                children.style.cssText = '';
            }, { once: true });
            return;
        }

        // Load children lazily
        if (!item.dataset.loaded) {
            children.innerHTML = spinner('Loading…');
            children.classList.remove('hidden');
            const param = item.dataset.kind === 'act'
                ? `act_id=${item.dataset.id}`
                : `parent_node_id=${item.dataset.id}`;
            try {
                const resp  = await fetch(`/admin/structure/children?${param}`, { headers: { Accept: 'application/json' } });
                const nodes = await resp.json();
                if (!Array.isArray(nodes) || nodes.length === 0) {
                    children.innerHTML = '<div class="py-2 text-xs text-gray-400 italic">No content.</div>';
                    item.dataset.hasCh = '0';
                    if (chev) chev.style.visibility = 'hidden';
                } else {
                    children.innerHTML = nodes.map(buildNodeItem).join('');
                }
                item.dataset.loaded = '1';
            } catch {
                children.innerHTML = '<div class="py-2 text-xs text-red-400">Failed to load.</div>';
            }
        } else {
            children.classList.remove('hidden');
        }

        // Animate open
        item.classList.add('s-open');
        chev.style.transform = 'rotate(90deg)';
        const h = children.scrollHeight;
        children.style.maxHeight  = '0';
        children.style.opacity    = '0';
        children.style.overflow   = 'hidden';
        children.style.transition = 'max-height 0.28s ease, opacity 0.22s ease';
        requestAnimationFrame(() => {
            children.style.maxHeight = h + 'px';
            children.style.opacity   = '1';
            children.addEventListener('transitionend', () => {
                children.style.maxHeight  = '';
                children.style.overflow   = '';
                children.style.transition = '';
            }, { once: true });
        });
    };

    function spinner(msg) {
        return `<div class="py-3 text-xs text-gray-400 flex items-center gap-2">
            <svg class="animate-spin w-3 h-3 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>${esc(msg)}</div>`;
    }

    /* ── build node item (shows full text inline) ──────────────── */
    function buildNodeItem(n) {
        const typeCls   = TYPE_CLS[n.node_type]    ?? 'bg-gray-100 text-gray-600';
        const sCls      = STATUS_CLS[n.status]     ?? 'bg-gray-100 text-gray-500';
        const borderCls = TYPE_BORDER[n.node_type] ?? 'border-gray-200';
        const hasCh     = n.has_children ? '1' : '0';

        // Compose the label line
        const numPart  = n.node_no  ? `<span class="font-bold text-gray-800">${esc(n.node_no)}</span>` : '';
        const hdgPart  = n.heading  ? `<span class="font-semibold text-gray-700">${esc(n.heading)}</span>` : '';
        const labelInner = [numPart, hdgPart].filter(Boolean).join('<span class="mx-1 text-gray-400">—</span>');

        // Full text block
        const textBlock = n.text_content
            ? `<div class="mt-2 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap font-[Georgia,serif]">${esc(n.text_content)}</div>`
            : '';

        // Children container border matches parent type
        return `
<div class="s-item py-0" data-kind="node" data-id="${n.node_id}" data-has-ch="${hasCh}">
  <div class="s-row group">
    <div class="flex items-start gap-2.5 py-3 pr-3 cursor-pointer hover:bg-gray-50 transition-colors select-none rounded-lg"
         onclick="sToggle(this.closest('.s-item'))">
      <span class="s-chev mt-0.5 inline-block shrink-0 w-4 text-center font-bold text-gray-400 text-base leading-none transition-transform duration-200"
            style="${hasCh === '1' ? '' : 'visibility:hidden'}">›</span>
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-1.5">
          <span class="inline-flex rounded px-1.5 py-0.5 text-xs font-semibold ${typeCls}">${esc(n.node_type)}</span>
          ${labelInner || `<span class="text-sm text-gray-500 italic">untitled</span>`}
          <span class="inline-flex rounded-full px-1.5 py-0.5 text-xs font-semibold ${sCls} opacity-0 group-hover:opacity-100 transition-opacity">
              ${esc((n.status ?? '').charAt(0).toUpperCase() + (n.status ?? '').slice(1))}
          </span>
          <a href="/admin/nodes/${n.node_id}" onclick="event.stopPropagation()"
             class="text-xs font-medium text-indigo-500 hover:text-indigo-900 opacity-0 group-hover:opacity-100 transition-opacity ml-1">→</a>
        </div>
        ${textBlock}
      </div>
    </div>
  </div>
  <div class="s-children hidden ml-5 pl-3 border-l-2 ${borderCls}"></div>
</div>`;
    }

    /* ── search ────────────────────────────────────────────────── */
    const searchEl  = document.getElementById('struct-search');
    const clearBtn  = document.getElementById('struct-clear');
    const searchPan = document.getElementById('search-panel');
    const treePan   = document.getElementById('tree-panel');
    let debounce;

    searchEl.addEventListener('input', function () {
        const q = this.value.trim();
        clearBtn.classList.toggle('hidden', !q);
        clearTimeout(debounce);
        if (!q) { searchPan.classList.add('hidden'); treePan.classList.remove('hidden'); return; }
        if (q.length < 2) return;
        debounce = setTimeout(() => doSearch(q), 380);
    });

    clearBtn.addEventListener('click', () => {
        searchEl.value = '';
        searchEl.dispatchEvent(new Event('input'));
        searchEl.focus();
    });

    async function doSearch(q) {
        treePan.classList.add('hidden');
        searchPan.classList.remove('hidden');
        searchPan.innerHTML = `<div class="px-5 py-10 text-center text-sm text-gray-400 flex items-center justify-center gap-2">${spinner('Searching…')}</div>`;
        try {
            const resp = await fetch(`/admin/structure/search?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } });
            if (!resp.ok) throw new Error();
            const data = await resp.json();
            renderSearch(q, data.acts ?? [], data.nodes ?? []);
        } catch {
            searchPan.innerHTML = '<div class="px-5 py-8 text-center text-sm text-red-400">Search failed. Try again.</div>';
        }
    }

    function highlight(text, q) {
        return esc(text).replace(
            new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'),
            '<mark class="bg-yellow-200 text-gray-900 rounded-sm px-0.5">$1</mark>'
        );
    }

    function renderSearch(q, acts, nodes) {
        const total = acts.length + nodes.length;
        if (total === 0) {
            searchPan.innerHTML = `<div class="px-5 py-12 text-center text-sm text-gray-400">No results for <strong>${esc(q)}</strong>.</div>`;
            return;
        }

        let html = `<div class="px-5 py-3 border-b border-gray-100 text-xs text-gray-500 font-medium">
            ${total} result${total !== 1 ? 's' : ''} for <strong>${esc(q)}</strong></div>
        <div class="divide-y divide-gray-50">`;

        acts.forEach(a => {
            const sCls = STATUS_CLS[a.status] ?? 'bg-gray-100 text-gray-500';
            html += `
<div class="flex items-start gap-3 px-5 py-4 hover:bg-indigo-50/40 transition-colors">
  <svg class="w-4 h-4 shrink-0 mt-0.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
  </svg>
  <div class="flex-1 min-w-0">
    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide">Act No. ${esc(a.act_no)}/${esc(a.act_year)}</p>
    <p class="text-sm font-semibold text-gray-800 mt-0.5 leading-snug">${highlight(a.title ?? '', q)}</p>
  </div>
  <span class="shrink-0 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ${sCls}">${esc((a.status ?? '').charAt(0).toUpperCase()+(a.status ?? '').slice(1))}</span>
  <a href="/admin/acts/${a.act_id}" class="shrink-0 text-xs font-medium text-indigo-600 hover:text-indigo-900">View →</a>
</div>`;
        });

        nodes.forEach(n => {
            const typeCls = TYPE_CLS[n.node_type]  ?? 'bg-gray-100 text-gray-600';
            const sCls    = STATUS_CLS[n.status]   ?? 'bg-gray-100 text-gray-500';
            const label   = [n.node_no, n.heading].filter(Boolean).join(' — ') || n.node_type;
            const crumb   = `Act ${esc(n.act_no)}/${esc(n.act_year)}${n.act_title ? ' — ' + esc(n.act_title.substring(0,50)) + (n.act_title.length > 50 ? '…' : '') : ''}`;
            html += `
<div class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50 transition-colors">
  <span class="shrink-0 mt-0.5 inline-flex rounded px-1.5 py-0.5 text-xs font-semibold ${typeCls}">${esc(n.node_type)}</span>
  <div class="flex-1 min-w-0">
    <p class="text-sm font-semibold text-gray-800 leading-snug">${highlight(label, q)}</p>
    <p class="text-xs text-gray-400 mt-0.5">${crumb}</p>
    ${n.preview ? `<p class="text-sm text-gray-600 mt-1.5 leading-relaxed font-[Georgia,serif]">${highlight(n.preview, q)}</p>` : ''}
  </div>
  <span class="shrink-0 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ${sCls}">${esc((n.status ?? '').charAt(0).toUpperCase()+(n.status ?? '').slice(1))}</span>
  <a href="/admin/nodes/${n.node_id}" class="shrink-0 text-xs font-medium text-indigo-600 hover:text-indigo-900">View →</a>
</div>`;
        });

        searchPan.innerHTML = html + '</div>';
    }
})();
</script>

@endsection
