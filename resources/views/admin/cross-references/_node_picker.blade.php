{{--
  Reusable act → node picker with rich node list + inline context.
  Required vars:
    $pickerPrefix   : 'source' or 'target'
    $acts           : collection of Act
    $selectedActId  : pre-selected act_id (nullable)
    $selectedNodeId : pre-selected node_id (nullable)
--}}
@php
    $nodeField = $pickerPrefix . '_node_id';
    $actSelId  = 'picker_act_'    . $pickerPrefix;
    $searchId  = 'picker_search_' . $pickerPrefix;
    $listId    = 'picker_list_'   . $pickerPrefix;
    $hiddenId  = 'picker_hidden_' . $pickerPrefix;
    $ctxId     = 'picker_ctx_'    . $pickerPrefix;
    $hintId    = 'picker_hint_'   . $pickerPrefix;
    $label     = $pickerPrefix === 'source' ? 'Source' : 'Target';

    $isSource  = $pickerPrefix === 'source';
    $accentBg        = $isSource ? '#EFF6FF' : '#F0FDF4';
    $accentBorder    = $isSource ? '#BFDBFE' : '#BBF7D0';
    $accentBadgeBg   = $isSource ? '#DBEAFE' : '#D1FAE5';
    $accentBadgeText = $isSource ? '#1D4ED8' : '#065F46';
    $accentHover     = $isSource ? '#EFF6FF' : '#F0FDF4';
    $accentActiveBg  = $isSource ? '#DBEAFE' : '#D1FAE5';
    $accentActiveBorder = $isSource ? '#93C5FD' : '#6EE7B7';
    $accentHeadColor = $isSource ? '#1e3a5f' : '#14532d';
    $accentLineColor = $isSource ? '#93C5FD' : '#6EE7B7';
@endphp

<div class="space-y-2">

    {{-- Act selector --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }} Act <span class="text-red-500">*</span></label>
        <select id="{{ $actSelId }}"
                class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">— select act —</option>
            @foreach($acts as $act)
                <option value="{{ $act->act_id }}"
                    {{ (string)($selectedActId ?? '') === (string)$act->act_id ? 'selected' : '' }}>
                    Act {{ $act->act_no }}/{{ $act->act_year }} — {{ $act->short_title ?? Str::limit($act->title, 55) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Node picker --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }} Node <span class="text-red-500">*</span></label>

        {{-- Hidden input for form submission --}}
        <input type="hidden" id="{{ $hiddenId }}" name="{{ $nodeField }}" value="{{ $selectedNodeId ?? '' }}">

        {{-- Search --}}
        <div class="relative mb-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </span>
            <input type="text" id="{{ $searchId }}" placeholder="Filter nodes…" disabled
                   class="block w-full rounded-lg border-gray-300 shadow-sm text-sm pl-8
                          focus:ring-indigo-500 focus:border-indigo-500
                          disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
        </div>

        {{-- Node list --}}
        <div id="{{ $listId }}"
             style="border:1px solid #E5E7EB;border-radius:8px;overflow-y:auto;max-height:340px;background:#fff;">
            <div id="{{ $hintId }}" style="padding:24px;text-align:center;color:#9CA3AF;font-size:12px;">
                Select an act to load nodes
            </div>
        </div>
    </div>

    {{-- Inline ancestor-chain context panel --}}
    <div id="{{ $ctxId }}" style="display:none;border:1px solid {{ $accentBorder }};border-radius:10px;background:{{ $accentBg }};overflow:hidden;">
        <div id="{{ $ctxId }}_loading"
             style="display:none;padding:10px 14px;font-size:12px;color:#6B7280;align-items:center;gap:8px;">
            <svg style="width:14px;height:14px;animation:spin 1s linear infinite;color:#818CF8;flex-shrink:0;" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Loading context…
        </div>
        <div id="{{ $ctxId }}_tree"></div>
    </div>

</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
(function () {
    var byActEndpoint = '{{ route('admin.nodes.by-act') }}';
    var lawPathBase   = '{{ url('/api/v1/nodes') }}';
    var prefix        = '{{ $pickerPrefix }}';
    var actSelId      = '{{ $actSelId }}';
    var searchId      = '{{ $searchId }}';
    var listId        = '{{ $listId }}';
    var hiddenId      = '{{ $hiddenId }}';
    var ctxId         = '{{ $ctxId }}';
    var hintId        = '{{ $hintId }}';
    var preselect     = {{ $selectedNodeId ? (int) $selectedNodeId : 'null' }};
    var nodeMap       = {};
    var allNodes      = [];

    var BADGE_BG   = '{{ $accentBadgeBg }}';
    var BADGE_TEXT = '{{ $accentBadgeText }}';
    var HEAD_COLOR = '{{ $accentHeadColor }}';
    var LINE_COLOR = '{{ $accentLineColor }}';
    var ACTIVE_BG     = '{{ $accentActiveBg }}';
    var ACTIVE_BORDER = '{{ $accentActiveBorder }}';

    // ── Helpers ─────────────────────────────────────────────────────────────

    function esc(s) {
        return String(s || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function truncate(s, n) {
        s = (s || '').replace(/\s+/g,' ').trim();
        return s.length > n ? s.slice(0, n) + '…' : s;
    }

    // Walk up nodeMap to build "PART I > SECTION 3 > SUBSECTION (3)" path
    function getFullPath(nodeId) {
        var parts   = [];
        var current = nodeMap[nodeId];
        while (current) {
            var seg = ((current.node_type || '') + (current.node_no ? ' ' + current.node_no : '')).trim();
            if (seg) { parts.unshift(seg.toUpperCase()); }
            current = current.parent_node_id ? nodeMap[current.parent_node_id] : null;
        }
        return parts.join(' > ');
    }

    // ── Node list rendering ─────────────────────────────────────────────────

    function buildItem(n, selected) {
        var depth    = Math.max(0, (n.level || 1) - 1);
        var indent   = depth * 16;
        var fullPath = getFullPath(n.node_id);   // e.g. "PART I > SECTION 3 > SUBSECTION (3)"
        var snippet  = truncate(n.text_content, 150);
        var isActive = selected && n.node_id === selected;

        var useBorder = depth > 0;
        var wrapStyle = 'padding:7px 12px 7px ' + (10 + indent) + 'px;cursor:pointer;'
            + 'border-bottom:1px solid #F3F4F6;'
            + (isActive ? 'background:' + ACTIVE_BG + ';' : '');
        var borderStyle = useBorder
            ? 'border-left:2px solid ' + (isActive ? ACTIVE_BORDER : LINE_COLOR) + ';padding-left:8px;'
            : '';

        var html = '<div class="lx-node-item" data-node-id="' + n.node_id + '" style="' + wrapStyle + '">';
        if (useBorder) html += '<div style="' + borderStyle + '">';

        // Full path label: "PART I > SECTION 3 > SUBSECTION (3):"
        html += '<div style="margin-bottom:' + (snippet ? '3px' : '0') + ';">';
        html += '<span style="font-size:11px;font-weight:700;color:' + BADGE_TEXT + ';letter-spacing:.02em;">'
              + esc(fullPath) + ':</span>';
        if (n.heading) {
            html += '<br><span style="font-size:12px;font-weight:500;color:#1F2937;">' + esc(n.heading) + '</span>';
        }
        html += '</div>';

        // Content snippet
        if (snippet) {
            html += '<div style="font-size:11px;color:#6B7280;line-height:1.55;">' + esc(snippet) + '</div>';
        }

        if (useBorder) html += '</div>';
        html += '</div>';
        return html;
    }

    function renderList(nodes, selectedId, filter) {
        var list = document.getElementById(listId);
        var q    = (filter || '').toLowerCase().trim();
        var html = '';
        var shown = 0;

        nodes.forEach(function (n) {
            if (q) {
                var searchable = ((n.node_type || '') + ' ' + (n.node_no || '') + ' ' + (n.heading || '') + ' ' + (n.text_content || '')).toLowerCase();
                if (searchable.indexOf(q) === -1) return;
            }
            html += buildItem(n, selectedId);
            shown++;
        });

        if (!shown) {
            html = '<div style="padding:24px;text-align:center;color:#9CA3AF;font-size:12px;">No nodes match</div>';
        }

        list.innerHTML = html;
        attachListeners();

        // Scroll selected item into view
        if (selectedId) {
            var el = list.querySelector('[data-node-id="' + selectedId + '"]');
            if (el) { el.scrollIntoView({ block: 'nearest' }); }
        }
    }

    function attachListeners() {
        var list = document.getElementById(listId);
        list.querySelectorAll('.lx-node-item').forEach(function (el) {
            el.addEventListener('mouseenter', function () {
                if (el.dataset.nodeId != document.getElementById(hiddenId).value) {
                    el.style.background = '#F9FAFB';
                }
            });
            el.addEventListener('mouseleave', function () {
                if (el.dataset.nodeId != document.getElementById(hiddenId).value) {
                    el.style.background = '';
                }
            });
            el.addEventListener('click', function () {
                selectNode(parseInt(el.dataset.nodeId));
            });
        });
    }

    function selectNode(nodeId) {
        document.getElementById(hiddenId).value = nodeId || '';
        var filter = document.getElementById(searchId).value;
        renderList(allNodes, nodeId, filter);
        loadContext(nodeId);
    }

    // ── Context panel ────────────────────────────────────────────────────────

    function renderTree(ancestors) {
        if (!ancestors || !ancestors.length) return '';
        var last = ancestors[ancestors.length - 1];

        // Build full path string from the ancestors array
        var pathParts = ancestors.map(function (a) {
            return ((a.node_type || '') + (a.node_no ? ' ' + a.node_no : '')).trim().toUpperCase();
        }).filter(Boolean);
        var fullPath = pathParts.join(' > ');

        var html = '<div style="padding:10px 14px;">';

        // Full path header: "PART I > SECTION 3 > SUBSECTION (3):"
        html += '<div style="margin-bottom:6px;">';
        html += '<span style="font-size:11px;font-weight:700;color:' + BADGE_TEXT + ';letter-spacing:.02em;">'
              + esc(fullPath) + ':</span>';
        if (last.heading) {
            html += '<br><span style="font-size:13px;font-weight:600;color:' + HEAD_COLOR + ';">' + esc(last.heading) + '</span>';
        }
        html += '</div>';

        // Show parent context rows (ancestors above the selected node) in a lighter style
        if (ancestors.length > 1) {
            html += '<div style="border-left:2px solid ' + LINE_COLOR + ';padding-left:10px;margin-bottom:8px;">';
            ancestors.slice(0, -1).forEach(function (a) {
                var seg = ((a.node_type || '') + (a.node_no ? ' ' + a.node_no : '')).trim().toUpperCase();
                html += '<div style="font-size:11px;color:#6B7280;margin-bottom:2px;">';
                if (seg) html += '<span style="font-weight:600;color:' + BADGE_TEXT + ';opacity:.7;">' + esc(seg) + '</span>';
                if (a.heading) html += '<span style="color:#9CA3AF;"> — ' + esc(a.heading) + '</span>';
                html += '</div>';
            });
            html += '</div>';
        }

        // Full text of the selected (leaf) node
        if (last.text_content) {
            html += '<div style="font-size:12px;color:#1F2937;line-height:1.75;white-space:pre-wrap;'
                  + 'border-left:2px solid ' + LINE_COLOR + ';padding-left:10px;">'
                  + esc(last.text_content) + '</div>';
        }

        html += '</div>';
        return html;
    }

    function loadContext(nodeId) {
        var panel   = document.getElementById(ctxId);
        var loading = document.getElementById(ctxId + '_loading');
        var tree    = document.getElementById(ctxId + '_tree');

        if (!nodeId) {
            panel.style.display = 'none';
            tree.innerHTML = '';
            return;
        }

        loading.style.display = 'flex';
        tree.innerHTML = '';
        panel.style.display = 'block';

        fetch(lawPathBase + '/' + nodeId + '/law-path', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            loading.style.display = 'none';
            tree.innerHTML = renderTree(data.ancestors);
        })
        .catch(function () {
            panel.style.display = 'none';
        });
    }

    // ── Act loading ──────────────────────────────────────────────────────────

    function loadNodes(actId) {
        var list      = document.getElementById(listId);
        var searchEl  = document.getElementById(searchId);
        var hint      = document.getElementById(hintId);
        var hiddenEl  = document.getElementById(hiddenId);

        allNodes = [];
        nodeMap  = {};
        searchEl.value    = '';
        searchEl.disabled = true;
        hiddenEl.value    = '';
        loadContext(null);

        if (!actId) {
            list.innerHTML = '<div id="' + hintId + '" style="padding:24px;text-align:center;color:#9CA3AF;font-size:12px;">Select an act to load nodes</div>';
            return;
        }

        list.innerHTML = '<div style="padding:24px;text-align:center;color:#9CA3AF;font-size:12px;">Loading…</div>';

        fetch(byActEndpoint + '?act_id=' + actId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (nodes) {
                allNodes = nodes;
                nodes.forEach(function (n) { nodeMap[n.node_id] = n; });
                searchEl.disabled = false;

                var selectedId = preselect || null;
                if (preselect) {
                    hiddenEl.value = preselect;
                    preselect = null;
                }
                renderList(allNodes, selectedId, '');
                if (selectedId) { loadContext(selectedId); }
            })
            .catch(function () {
                list.innerHTML = '<div style="padding:24px;text-align:center;color:#EF4444;font-size:12px;">Failed to load nodes</div>';
            });
    }

    // ── Init ─────────────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        var actSel   = document.getElementById(actSelId);
        var searchEl = document.getElementById(searchId);

        actSel.addEventListener('change', function () { loadNodes(this.value); });

        searchEl.addEventListener('input', function () {
            var selectedId = parseInt(document.getElementById(hiddenId).value) || null;
            renderList(allNodes, selectedId, this.value);
        });

        if (actSel.value) { loadNodes(actSel.value); }
    });
})();
</script>
