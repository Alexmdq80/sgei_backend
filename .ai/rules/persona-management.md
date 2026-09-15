---
paths:
  - 'fe/src/pages/Admin/PersonaManagement/**'
---

# Persona Management

## PersonaDomicilioModal: precarga de cascada geográfica pendiente (backend no expone provincia/depto)
PENDIENTE (decisión de diseño) — Precarga de la cascada Provincia→Departamento→Localidad en PersonaDomicilioModal: el `DomicilioResource` del backend NO expone `provincia_id` ni `departamento_id` (solo `localidad_id`), así que hoy la precarga de edición no puede reconstruir los combos geográficos. Opciones: A) mantener precarga solo con localidad_id (actual, sin backend), B) ampliar DomicilioResource para incluir provincia_id/departamento_id y repoblar con loadDepartamentos/loadLocalidades del hook useGeografiaCascade, C) prefiltro por localidad_nombre. Requiere acuerdo sobre cuál elegir.
