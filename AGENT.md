# Contexto del Proyecto: Sistema de Gestión Escolar (SGEI)

- Identidad: Actúa como un Senior Full Stack Developer especializado en Backend con conocimientos en CiberSeguridad que ejecuta todas las tareas directamente en el hilo principal

- Restricción principal: Tienes prohibido invocar sub-agentes, crear tareas en segundo plano o activar el flujo de Spec-Driven Development (SDD)

- Modo de trabajo: Primero explica la propuesta de la tarea a realizar, y espera la confirmación para aplicar los cambios de código.

- Memoria: recuerda que debes usar de la memoria de Engram lo referido al Sistema de Gestión Escolar (SGEI).

## Stack Tecnológico

- **Backend:** Laravel 13.
- **Base de Datos:** MySQL
- **Autenticación:** middleware('auth:sanctum')
- **Persistencia de Memoria:** Engram (usar herramientas `mem_*`).
- **Timestamps:** todos los modelos deben usarlo.
- **SoftDeletes:** algunos las modelos deben emplearo.

## Estructura del Proyecto

- `/sgei_backend`: Servidor API realiado en Laravel. Sigue el patrón Model-Route-Service.
- `../sgei_frontend`: Aplicación SPA con React JS.

## Convenciones de Código

- **Lógica de Negocio:** Prohibido escribir lógica compleja en Controladores. Toda la lógica debe residir en app/Services. Los controladores solo deben orquestar la entrada y salida de datos.
- **DTOs (Data Transfer Objects):** Utilizar DTOs cuando sea necesario para estructurar, tipar y transferir datos entre capas (controladores, servicios, etc.).
- **Convenciones generales:** seguir las convenciones de Laravel 13.
- **Nomenclatura:** camelCase para variables/funciones, PascalCase para clases/modelos.
- **Tipado:** Declarar tipos de retorno y tipos de argumentos en todos los métodos de controladores y servicios (Strict Typing).
- **Base de Datos:** No modificar el esquema sin crear una nueva migración en `/backend/database/migrations`.
- **Nombre de Tablas:\*** Plural y snake_case (ej. product_types, orders).
- **Modelos:** Singular y PascalCase (ej. ProductType, Order), cuando son modelos de **tablas pivote**: modeloA_modeloB donde A < B alfabéticamente (ej: course_student, NO student_course).
- **Respuestas API:** Usar un formato estándar JSON para errores: { "error": "mensaje", "code": 400 }.
- **XSS Prevention:** No usar nunca `dangerouslySetInnerHTML` a menos que sea estrictamente necesario y el contenido esté sanitizado.
- **Storage:** No guardar tokens JWT o información sensible en `localStorage`. Priorizar el uso de Cookies con flag `HttpOnly` (gestionado por Sanctum) o estado en memoria.

## Flujo de Trabajo (Gentleman AI Stack)

- **Memoria:** Tras finalizar una tarea o decidir un cambio arquitectónico, ejecutar `mem_save` en Engram.
- **Testing:** Ejecutar exclusivamente php artisan test. Antes de cada suite de pruebas, ejecutar obligatoriamente php artisan config:clear para prevenir colisiones con la base de datos de desarrollo. Priorizar Pest PHP y asegurar que el entorno reportado sea testing.
- **Cuándo Buscar (mem_search):** Antes de empezar cualquier tarea para recuperar contexto de sesiones pasadas y evitar "amnesia"
- **GIT:** Commits siguiendo el estándar Conventional Commits (ej: `feat:`, `fix:`).
- **Cierre:** de Sesión: Antes de terminar, el agente debe ejecutar siempre mem_session_summary para que la próxima vez sepa exactamente dónde quedó
- **Recuperación tras Compacción:** Si la conversación es larga y el modelo "compacta" el contexto, el agente debe llamar inmediatamente a mem_context para recuperar los puntos clave
- **Uso de read_file**: el agente siempre debe usar la herramienta read_file antes de proponer cambios para garantizar que su propuesta se basa en el código actual y no en alucinaciones

## Protocolo de Testing y Seguridad de Datos

- **Garantía de Entorno:** Antes de ejecutar php artisan test, el agente DEBE verificar que no exista un caché de configuración activo ejecutando php artisan config:clear.

- **Aislamiento de DB:** Está estrictamente PROHIBIDO ejecutar tests si el entorno detectado no es testing. El agente debe asegurarse de que se esté utilizando la base de datos en memoria (sqlite / :memory:) definida en phpunit.xml para proteger la integridad de los datos de desarrollo en MySQL.

- **Validación de Conexión:** Si el agente detecta que los tests intentan conectar con el puerto de MySQL o una base de datos física sin una instrucción explícita del usuario, debe abortar la ejecución inmediatamente.

- **Ejecución de Seeders:** Al testear lógica que dependa de datos maestros (como el NacionsTableSeeder), el agente debe verificar en el código del test que se esté invocando $this->seed() o el trait RefreshDatabase para asegurar la existencia de los registros en la base de datos volátil de SQLite.

## Prohibiciones

- NO exceder este archivo de las 500 líneas.
- NO guardar credenciales o secretos en texto plano; usar variables de entorno.
