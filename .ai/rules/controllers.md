---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## response()->json($paginator) no emite meta.* (claves en la raíz)
Serializar un LengthAwarePaginator directo (`return response()->json($service->getAll(...))`) vuelca sus claves en la RAÍZ del JSON: `data`, `current_page`, `last_page`, `total`, `per_page`. El wrapper `meta` solo aparece si se usan API Resources. Caso real: CalleController::index serializa el LengthAwarePaginator de CalleService::getAll. Un test que espere `response["meta"]["last_page"]` se rompe: leer las claves de la raíz. No introducir `per_page`/`limit` sueltos: pasar por el VO TamanoPagina.
