---
paths:
    - "fe/src/services/**"
    - "fe/src/**/__tests__/**"
---

# Frontend Cache (catálogos, geografía y calles)

## Nunca referenciar el singleton `catalogCache` dentro de sus propios métodos

`CatalogCacheService` ejecuta `this.checkVersion()` en el constructor y el singleton se asigna al final del archivo (`const catalogCache = new CatalogCacheService()`): referenciar `catalogCache` dentro de un método de instancia lanza ReferenceError (TDZ) en la primera carga. Usar `this`. Los imports del módulo (p. ej. `callesCacheService`) sí están inicializados. `clearAll()` purga localStorage y los almacenes de IndexedDB vía `callesCacheService.purge()` en fire-and-forget: no convertirlo en `async` (lo llama el constructor) y no dejar que un fallo de la purga impida el `localStorage.setItem` de la versión.

## `sgei_calles_db`: la versión de esquema de IndexedDB sólo sube

La base (DB_VERSION 2) tiene dos stores: `localidades_calles` (keyPath `localidad_id`) y `departamentos_localidades` (keyPath `departamento_id`). Agregar o cambiar un store exige subir DB_VERSION (3, 4…): con el mismo número `onupgradeneeded` no corre y el store nunca se crea, con falla silenciosa (`getLocalidades` devuelve null sin loguear; `saveLocalidades` deja NotFoundError en consola) y sólo se ve en perfiles limpios. Nunca bajar el número (VersionError); para resetear: `indexedDB.deleteDatabase("sgei_calles_db")`. Toda apertura necesita `onblocked` + timeout (`DB_OPEN_TIMEOUT_MS`), cerrar la conexión al terminar cada transacción y `db.onversionchange = () => db.close()`; normalizar claves con `Number()` y degradar a red si son inválidas.

## Localidades: cache-first en IndexedDB y frescura por manifiesto

`geografiaService.getLocalidades()` sólo usa IndexedDB en la rama estándar (sin params custom: `region_id`, `search`, `per_page` distinto de 500); debe devolver siempre el array crudo (contrato de 4 consumidores: EscuelaManagement, CupofManagement, useGeografiaCascade, PersonaDomicilioModal) y nunca bloquear: si el caché falla, cae a `/localidades`. La frescura depende de `manifest.localidades` (fila `localidads` de `catalogo_versions`) → `callesCacheService.checkLocalidadesVersion` purga el store y guarda `sgei_localidades_version`. No agregar `localidades` a los fetchers de `AuthContext` (14.431 registros del país no van a localStorage).

## `vi.resetModules()` no reinstancia los mocks

Con `vi.mock(factory)`, `vi.resetModules()` limpia el registro de módulos pero no el de mocks: seguís recibiendo los mismos `vi.fn()`, así que re-importar para re-ejecutar el constructor de un singleton y observar su efecto da falsos resultados. Alternativas: llamar al método público en el que delega el constructor, o `mockReset()`/`mockClear()` inmediatamente después del primer import. Si un servicio importa otro por transitividad, el mock del test debe incluir todos los métodos que usa el constructor (p. ej. `callesCacheService.purge`), o el TypeError se traga en un `try/catch` y altera lo que estás midiendo. jsdom no implementa IndexedDB (`window.indexedDB === undefined`), por eso los métodos de purga salen temprano con `if (!window.indexedDB) return;`.
