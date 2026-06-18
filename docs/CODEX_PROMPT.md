# Prompt: build a legal document browser page

> Paste everything below this line into Codex (or any AI coding assistant) to
> generate the page. It is self-contained — the assistant does not need access
> to this repo.

---

You are an expert frontend engineer. Build a single static web page (plain
HTML, CSS, and vanilla JavaScript — no build step, no framework, no backend
code) that consumes a read-only public JSON API and displays Sri Lankan
consumer-protection legal Acts and their sections, with the relationships
between sections visible and clickable.

## API

- Base URL: `https://<DOMAIN>/api/public/v1` — put `<DOMAIN>` behind a single
  `const API_BASE` constant at the top of the JS so it's easy to repoint.
- Auth: none. Every endpoint is public.
- Methods: `GET` only.
- Always send header `Accept: application/json`.
- No rate limiting is enforced server-side, but don't poll — fetch on demand
  (page load / user action), not on a timer.

### Endpoint 1 — `GET /acts`
List Acts, paginated, 50 per page.

Query params: `status` (optional, one of `active` | `repealed` | `amended`),
`page` (optional, integer).

Response shape:
```ts
{
  data: Array<{
    act_id: number;
    act_no: number;
    act_year: number;
    title: string;
    short_title: string | null;
    commencement_date: string | null; // "YYYY-MM-DD"
    status: "active" | "repealed" | "amended";
    current_version: boolean;
    replaces_act_id: number | null;
    replaced_by_act_id: number | null;
    gazette: {
      gazette_id: number;
      gazette_no: string;
      gazette_date: string | null; // "YYYY-MM-DD"
      title: string;
      status: "active" | "inactive";
    };
  }>;
  links: { first: string; last: string; prev: string | null; next: string | null };
  meta: { current_page: number; last_page: number; total: number; per_page: number; [k: string]: unknown };
}
```

### Endpoint 2 — `GET /acts/{act_id}`
A single Act, same item shape as one entry in `/acts.data` (no pagination
wrapper — the object is returned directly).

### Endpoint 3 — `GET /acts/{act_id}/tree`
The full nested section tree for one Act — this is what you render as the
document body. Note this tree-node shape is **smaller** than the node shape
used by the standalone node endpoints below (no `parent_node_id`, `act_id`,
`full_path`, or date fields here):

```ts
{
  act_id: number;
  title: string;
  status: "active" | "repealed" | "amended";
  nodes: TreeNode[];
}

type TreeNode = {
  node_id: number;
  node_type: "PART" | "CHAPTER" | "SECTION" | "SUBSECTION" | "PARAGRAPH"
           | "SUBPARAGRAPH" | "ITEM" | "SCHEDULE" | "PREAMBLE" | "DEFINITIONS";
  node_no: string | null;       // e.g. "I", "1", "(1)", "(a)"
  heading: string | null;
  text_content: string | null;  // the actual legal text, can be long; null on container nodes (e.g. a PART that just holds SECTIONs)
  level: number;                // depth, 1-based
  sort_order: number;
  has_children: boolean;
  status: "active" | "repealed" | "amended";
  children: TreeNode[];         // recursive, empty array on leaves
};
```
Render recursively: a node with `children.length > 0` is a container (show
heading, indent, recurse); a node with no children and non-null
`text_content` is the actual provision text.

### Endpoint 4 — `GET /gazettes`
List Gazettes, paginated, newest first. Query params: `status` (`active` |
`inactive`), `page`. Same `{ data, links, meta }` wrapper as `/acts`, where
each item is the `gazette` shape from Endpoint 1 (without nested acts).

### Endpoint 5 — `GET /gazettes/{gazette_id}`
A single Gazette, with its Acts embedded:
```ts
{
  gazette_id: number; gazette_no: string; gazette_date: string | null;
  title: string; status: "active" | "inactive";
  acts: Array<{ /* same Act shape as Endpoint 1, minus nested gazette */ }>;
}
```

### Endpoint 6 — `GET /nodes/{node_id}`
One section, standalone (e.g. when you only have a node id from a search
result or cross-reference and need to show/jump to it):
```ts
{
  node_id: number; act_id: number; parent_node_id: number | null;
  node_type: "PART" | "CHAPTER" | "SECTION" | "SUBSECTION" | "PARAGRAPH"
           | "SUBPARAGRAPH" | "ITEM" | "SCHEDULE" | "PREAMBLE" | "DEFINITIONS";
  node_no: string | null; heading: string | null; text_content: string | null;
  level: number; sort_order: number; has_children: boolean;
  status: "active" | "repealed" | "amended";
  effective_date: string | null; repealed_date: string | null;
  full_path: string; // e.g. "PART I > SECTION 1"
}
```

### Endpoint 7 — `GET /nodes/{node_id}/children`
A flat array (not a `{data:...}` wrapper) of the immediate children, each in
the same shape as Endpoint 6.

### Endpoint 8 — `GET /nodes/{node_id}/references`
The relations for one section, split by direction:
```ts
{
  outgoing: Array<{
    reference_id: number;
    reference_type: "refers_to" | "subject_to" | "as_amended_by" | "repeals" | "saves" | "cross_applies";
    reference_text: string | null;
    target_node: { node_id: number; node_no: string | null; heading: string | null; full_path: string } | null;
  }>;
  incoming: Array<{ /* same shape, but key is source_node instead of target_node */ }>;
}
```
`outgoing` = this section refers to other sections. `incoming` = other
sections refer to this one.

### Endpoint 9 — `GET /search?q={query}`
`q` must be 3+ characters or you get `{ query, results: [] }`. Up to 50
results:
```ts
{
  query: string;
  results: Array<{
    node_id: number; act_id: number; act_title: string;
    node_type: string; node_no: string | null; heading: string | null;
    full_path: string;
    snippet: string; // plain text, may start/end with "…", may contain the matched substring — NOT HTML, escape it before inserting into the DOM
  }>;
}
```

### Errors
404s and validation errors come back as `{ message: string, ... }` with the
matching HTTP status — only guaranteed JSON if you sent `Accept:
application/json`. Treat any non-2xx as an error state in the UI.

## Pages / views to build (single HTML file, client-side view switching is fine)

1. **Acts list** (default view) — title, short title, year, status badge,
   gazette title; a status filter (`All` / `Active` / `Repealed` / `Amended`);
   pagination controls using `meta`/`links`. Clicking an Act opens its tree
   view.
2. **Act tree view** — fetch `/acts/{id}/tree`, render the nested structure
   as a collapsible outline (Parts collapse/expand their Sections, etc.).
   Visually distinguish `status: "repealed"` and `"amended"` nodes from
   `"active"` ones (e.g. muted color + a small badge) — don't hide them.
   Clicking a leaf section opens the section detail panel.
3. **Section detail panel** (modal or side panel, not a full page reload) —
   show the section's `full_path`, `text_content`, then fetch
   `/nodes/{id}/references` and list "Refers to" (outgoing) and "Referred to
   by" (incoming) as clickable links. Clicking a related section loads that
   node (`/nodes/{id}`) into the same panel — keep a small back-stack so the
   user can navigate through a chain of cross-references and go back.
4. **Search** — a search box in the page header, visible from every view;
   live or on-submit query against `/search`; results list shows
   `act_title`, `full_path`, and the `snippet` (escaped, not raw HTML);
   clicking a result opens the section detail panel for that `node_id`.

## Non-functional requirements

- No write operations anywhere — this is a read-only consumer.
- No login/auth UI — there is none on the API.
- Handle three states everywhere data is fetched: loading, empty, error
  (network failure or non-2xx) — never leave the user looking at a blank
  screen with no feedback.
- Mobile-responsive layout.
- Accessible: real heading hierarchy, sufficient color contrast for status
  badges, keyboard-operable modal/panel (focus trap + Escape to close).
- Long `text_content` values are plain legal prose (may contain line breaks)
  — preserve whitespace/line breaks when rendering, and escape before
  inserting into the DOM (don't use `innerHTML` on API text).

## Non-goals

- No backend/server code — everything runs client-side against the existing
  API.
- No build tooling (webpack/vite/npm) — plain `<script>`/`<link>` includes
  only.
- No data caching layer / service worker required (nice-to-have, not
  requested).

## Deliverable

A single self-contained `index.html` (inline or linked `style.css` /
`app.js`) that can be dropped into any static host, with `API_BASE` as the
one line to edit to point it at a real deployment.
