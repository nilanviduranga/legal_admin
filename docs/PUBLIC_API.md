# Public Legal Data API

A read-only, unauthenticated JSON API for consuming Sri Lankan consumer-protection
legislation (Gazettes, Acts, their hierarchical sections, and the cross-references
between sections). Intended for external sites that want to display this data on
their own pages.

- **Base URL:** `https://<your-domain>/api/public/v1`
- **Auth:** none — every endpoint below is public.
- **Methods:** `GET` only. There is no way to create, edit, or delete data through
  this API.
- **Rate limiting:** not currently enforced server-side. Please cache responses on
  your end (the underlying legal text changes rarely) rather than polling.
- **Content negotiation:** send `Accept: application/json`. Error responses (404,
  422, etc.) are only guaranteed to be JSON when this header is present.

## Data model

```
Gazette ──< Act ──< LegalNode (tree: PART > SECTION > SUBSECTION > ...)
                         │
                         └──< CrossReference >── LegalNode (another section)
```

- **Gazette** — the official publication an Act was issued in.
- **Act** — a piece of legislation (e.g. "Consumer Affairs Authority Act, No. 9 of
  2003"). Has a `status` (`active`, `repealed`, `amended`) and may point at the Act
  that replaced or preceded it.
- **LegalNode** — one node in an Act's structure (Part, Section, Subsection,
  Paragraph, Schedule, ...). Nodes nest under a `parent_node_id`, forming a tree.
  Leaf nodes carry the actual `text_content`.
- **CrossReference** — a directed relation between two LegalNodes (e.g. "Section 73
  repeals Section 1 of the old Act"), typed via `reference_type`
  (`refers_to`, `subject_to`, `as_amended_by`, `repeals`, `saves`, `cross_applies`).

## Endpoints

### `GET /acts`
List Acts, paginated (50 per page), each with its Gazette embedded.

Query params: `status` (`active` | `repealed` | `amended`), `page`.

```json
{
  "data": [
    {
      "act_id": 1,
      "act_no": 9,
      "act_year": 2003,
      "title": "Consumer Affairs Authority Act",
      "short_title": "Consumer Affairs Authority Act, No. 9 of 2003",
      "commencement_date": "2003-03-17",
      "status": "active",
      "current_version": true,
      "replaces_act_id": null,
      "replaced_by_act_id": null,
      "gazette": { "gazette_id": 1, "gazette_no": "Supplement to Part II", "gazette_date": "2003-03-21", "title": "...", "status": "active" }
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "last_page": 1, "total": 1, "...": "..." }
}
```

### `GET /acts/{act_id}`
A single Act with its Gazette embedded. Same shape as one item in `/acts`.

### `GET /acts/{act_id}/tree`
The full nested section tree for an Act — everything needed to render the document.

```json
{
  "act_id": 1,
  "title": "Consumer Affairs Authority Act",
  "status": "active",
  "nodes": [
    {
      "node_id": 2,
      "node_type": "PART",
      "node_no": "I",
      "heading": "Establishment of the Consumer Affairs Authority",
      "text_content": null,
      "level": 1,
      "sort_order": 2,
      "has_children": true,
      "status": "active",
      "children": [
        { "node_id": 3, "node_type": "SECTION", "node_no": "1", "heading": "Short title", "...": "...", "children": [] }
      ]
    }
  ]
}
```

### `GET /gazettes`
List Gazettes, paginated, newest first. Query params: `status`, `page`.

### `GET /gazettes/{gazette_id}`
A single Gazette with its Acts embedded.

### `GET /nodes/{node_id}`
A single section/node with its computed `full_path` (e.g.
`"PART I > SECTION 1"`), useful when you only have a node id (e.g. from a
cross-reference or search result) and need to render it standalone.

### `GET /nodes/{node_id}/children`
The immediate children of a node (flat array, not recursive — use `/acts/{id}/tree`
if you need the whole document at once).

### `GET /nodes/{node_id}/references`
The relations for one node, split by direction:

```json
{
  "outgoing": [
    { "reference_id": 1, "reference_type": "refers_to", "reference_text": "...", "target_node": { "node_id": 10, "node_no": "5", "heading": null, "full_path": "PART II > SECTION 5" } }
  ],
  "incoming": [
    { "reference_id": 15, "reference_type": "repeals", "reference_text": "...", "source_node": { "node_id": 251, "node_no": "(1)", "heading": null, "full_path": "PART VII > SECTION 73 > SUBSECTION (1)" } }
  ]
}
```

### `GET /search?q={query}`
Full-text-ish search (substring match on heading + body) across all sections of
all Acts. Requires `q` to be at least 3 characters; returns up to 50 results with a
highlighted snippet.

```json
{
  "query": "consumer",
  "results": [
    { "node_id": 125, "act_id": 1, "act_title": "Consumer Affairs Authority Act", "node_type": "SUBSECTION", "node_no": "(1)", "heading": null, "full_path": "PART II > SECTION 33 > SUBSECTION (1)", "snippet": "… of traders in any sector of trade for the purpose of promoting competition and consumer awareness." }
  ]
}
```

## Rendering an Act on your site (suggested flow)

1. `GET /acts?status=active` — list what's available, show titles/short titles.
2. When a user opens one, `GET /acts/{act_id}/tree` — render the `nodes` array
   recursively: heading from `node_type` + `node_no` + `heading`, body from
   `text_content`, indent by `level`, recurse into `children`.
3. For any node the user clicks on, `GET /nodes/{node_id}/references` to show
   "this section refers to / is referred to by" links, and link to the related
   section using its `full_path` and `node_id` (fetch it via
   `GET /nodes/{node_id}`).
4. Use `GET /search?q=...` to power an on-site search box over this data without
   mirroring the whole database.

## Versioning

The `public/v1` prefix is stable — fields will not be removed or repurposed within
v1. New optional fields may be added. A breaking change would ship under
`public/v2` instead.
