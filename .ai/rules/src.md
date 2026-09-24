---
paths:
  - 'fe/src/**'
---

# Src

## set-state-in-effect: useRef/archivos grandes producen bailout (lint limpio falso)
El compilador de React hace bailout (no analiza) en componentes grandes o con muchísimos hooks/refs (p. ej. PersonaDomicilioModal, 1.244 líneas): setState sincrónico en un efecto NO marca react-hooks/set-state-in-effect (líneas 294-302 usan ese patrón y el lint global da limpio). No confiar en "lint limpio" de un componente sin verificarlo con `eslint --stdin` (pipe del archivo) o partiéndolo en subcomponentes chicos — si se parte y aparecen avisos, es el análisis volviendo a correr, no una regresión. Contraste útil: SearchableSelect.jsx (chico) SÍ marca el mismo patrón.
