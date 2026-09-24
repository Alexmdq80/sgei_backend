---
paths:
  - 'fe/src/utils/**'
---

# Utils

## MiniSearch: usar distancia absoluta (fuzzy >= 1), no fracción
En minisearch@7.2.0, `fuzzy < 1` se convierte en `Math.round(term.length*fuzzy)` topado a 6: la tolerancia es inconsistente por largo del término ("tandli"→1 nunca hallaba TANDIL; "rivadvaia"→2 sí hallaba RIVADAVIA) y Levenshtein no implementa Damerau, así que una transposición adyacente cuesta 2 y la modalidad fraccionaria no la cubre. Usar `fuzzyPorLargo` (distancia absoluta 2, desactivada en términos <4 letras donde `prefix` ya alcanza) y protegerlo con minisearch.smoke.test.js. No volver a `fuzzy: 0.2`.
