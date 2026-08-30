---
paths:
  - 'database/migrations/**'
---

# Migrations

## Sincronizar _ide_helper_models.php tras agregar columnas
Después de una migración que agrega columnas a un modelo (p. ej. `personas.foto_path`), mantener actualizado `_ide_helper_models.php` (raíz del BE) con las nuevas `@property`. El comando `php artisan ide-helper:models Persona --write` puede no reflejar el esquema real del entorno local, así que verificar manualmente el docblock del modelo y añadir la propiedad faltante. Si no es difícil, se regenera el helper al final.
