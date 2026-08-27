---
paths:
  - 'sgei_frontend/src/pages/**'
---

# Pages

## Roles territoriales/curriculares eliminados (sistema de 2 niveles)
El SGEI ya NO usa roles intermedios (jefe_provincial, jefe_regional, jefe_distrital, supervisor_curricular) ni tablas provincia_usuario/region_usuario/distrito_usuario. Solo hay 2 niveles: global (superuser/es_administrador) e institucional (EscuelaPersona con director/vicedirector/secretario/prosecretario/profesor/preceptor). No reintroducir esos roles ni sus servicios; esSuperUser = user?.es_administrador || roles.some(r=>r.name==='superuser'), y conducción = activeProfile.type==='school'.
