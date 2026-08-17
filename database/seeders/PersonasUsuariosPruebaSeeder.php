<?php

namespace Database\Seeders;

use App\Models\Calle;
use App\Models\Contacto;
use App\Models\Departamento;
use App\Models\DistritoUsuario;
use App\Models\DocumentoTipo;
use App\Models\Domicilio;
use App\Models\Escuela;
use App\Models\EscuelaPersona;
use App\Models\Localidad;
use App\Models\Nacion;
use App\Models\Persona;
use App\Models\Provincia;
use App\Models\ProvinciaUsuario;
use App\Models\Region;
use App\Models\RegionUsuario;
use App\Models\Usuario;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PersonasUsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Faker en español (es_AR)
        $faker = Faker::create('es_AR');

        $docTipo = DocumentoTipo::find(1); // DNI
        if (!$docTipo) {
            $this->command->error('Ejecutá DocumentoTipoSeeder antes.');
            return;
        }

        // Jefaturas: BUSCAR, no crear
        $provinciaBsAs = Provincia::where('nombre', 'BUENOS AIRES')->first();
        $departamentoGp = Departamento::where('nombre', 'GENERAL PUEYRREDÓN')->first();
        if (!$provinciaBsAs || !$departamentoGp) {
            $this->command->error('No se encontraron BUENOS AIRES / GENERAL PUEYRREDÓN. Abortando.');
            return;
        }

        // Roles globales y escolares
        $roles = Role::whereIn('name', [
            'superuser',
            'jefe_provincial',
            'jefe_regional',
            'jefe_distrital',
            'director',
            'vicedirector',
            'secretario',
            'prosecretario',
            'profesor',
            'preceptor',
        ])->pluck('id', 'name');

        // Escuelas para roles escolares
        $escuelas = Escuela::inRandomOrder()->limit(30)->get();

        $password = Hash::make(config('app.admin_pass'));

        // ===== OFFSETS DINÁMICOS (cada ejecución suma un lote nuevo) =====
        $proximoDni = $this->proximoDniPersona();
        $proximoIndiceContacto = $this->proximoIndiceContacto();
        $proximoIndiceUsuario = $this->proximoIndiceUsuario();
        $proximoIndiceAdmin = $this->proximoIndiceAdmin();


        DB::transaction(function () use ($faker, $docTipo, $provinciaBsAs, $departamentoGp, $roles, $escuelas, $password, $proximoDni, $proximoIndiceContacto, $proximoIndiceUsuario, $proximoIndiceAdmin) {
            // ===== PERSONAS (500) =====
            $personas = collect();
            for ($i = 0; $i < 500; $i++) {
                $personas->push($this->crearPersona($faker, $proximoDni + $i, $proximoIndiceContacto + $i));
            }

            // ===== USUARIOS CON ROLES (100, estado activo, vinculados) =====

            // Superusuarios (3) — es_administrador = true vía forceFill
            for ($i = 0; $i < 3; $i++) {
                $indiceAdmin = $proximoIndiceAdmin + $i;
                if ($indiceAdmin > 99) {
                    $this->command->warn('Se alcanzó el límite de 99 superusuarios admin@sgei.local. No se crean más.');
                    break;
                }
                $emailAdmin = sprintf('admin%02d@sgei.local', $indiceAdmin);
                $usuario = $this->crearUsuarioVinculado($personas->shift(), $password, true, $emailAdmin);
                $usuario->assignRole($roles['superuser']);
            }

            // Jefes Provinciales (2) — 1 garantizado en BUENOS AIRES
            for ($i = 0; $i < 2; $i++) {
                $usuario = $this->crearUsuarioVinculado($personas->shift(), $password);
                $usuario->assignRole($roles['jefe_provincial']);
                ProvinciaUsuario::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'provincia_id' => $i === 0 ? $provinciaBsAs->id : Provincia::inRandomOrder()->value('id'),
                ]);
            }

            // Jefes Regionales (5)
            for ($i = 0; $i < 5; $i++) {
                $usuario = $this->crearUsuarioVinculado($personas->shift(), $password);
                $usuario->assignRole($roles['jefe_regional']);
                RegionUsuario::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'region_id' => Region::inRandomOrder()->value('id'),
                ]);
            }

            // Jefes Distritales (10) — 1 garantizado en GENERAL PUEYRREDÓN
            for ($i = 0; $i < 10; $i++) {
                $usuario = $this->crearUsuarioVinculado($personas->shift(), $password);
                $usuario->assignRole($roles['jefe_distrital']);
                DistritoUsuario::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'departamento_id' => $i === 0 ? $departamentoGp->id : Departamento::inRandomOrder()->value('id'),
                ]);
            }

            // Equipos Directivos (30) — roles escolares vía escuela_persona
            $rolesDirectivos = ['director', 'vicedirector', 'secretario', 'prosecretario'];
            for ($i = 0; $i < 30; $i++) {
                $persona = $personas->shift();
                $this->crearUsuarioVinculado($persona, $password);
                $this->asignarRolEscolar($persona, $escuelas->get($i % $escuelas->count()), $roles[$rolesDirectivos[$i % 4]]);
            }

            // Docentes y Preceptores (50)
            $rolesDocentes = ['profesor', 'preceptor'];
            for ($i = 0; $i < 50; $i++) {
                $persona = $personas->shift();
                $this->crearUsuarioVinculado($persona, $password);
                $this->asignarRolEscolar($persona, $escuelas->get($i % $escuelas->count()), $roles[$rolesDocentes[$i % 2]]);
            }

            // ===== USUARIOS SIN ROLES (145) =====

            // vinculacion_pendiente (45): DNI/email coinciden con persona, sin vincular
            for ($i = 0; $i < 45; $i++) {
                $this->crearUsuarioPendienteVinculacion($personas->shift(), $password);
            }

            // email_verificado (40)
            for ($i = 0; $i < 40; $i++) {
                $this->crearUsuarioSueltos('email_verificado', now(), $password, $proximoIndiceUsuario + $i);
            }

            // email_pendiente (35)
            for ($i = 0; $i < 35; $i++) {
                $this->crearUsuarioSueltos('email_pendiente', null, $password, $proximoIndiceUsuario + 40 + $i);
            }

            // esperando_activacion (25)
            for ($i = 0; $i < 25; $i++) {
                $this->crearUsuarioSueltos('esperando_activacion', null, $password, $proximoIndiceUsuario + 40 + 35 + $i);
            }
        });

        $this->command->info(
            '✅ Lote agregado: 500 personas + 245 usuarios. ' .
            'Totales: ' . Persona::count() . ' personas, ' . Usuario::count() . ' usuarios.'
        );
    }

    // ===== OFFSETS DINÁMICOS =====

    private function proximoDniPersona(): int
    {
        $max = Persona::where('documento_numero', 'like', '90%')
            ->max(DB::raw('CAST(documento_numero AS UNSIGNED)'));

        return $max ? (int) $max + 1 : 90000000;
    }

    private function proximoIndiceContacto(): int
    {
        return Contacto::where('email', 'like', 'persona%@test.local')->count() + 1;
    }

    private function proximoIndiceUsuario(): int
    {
        return Usuario::where('email', 'like', 'usuario%@test.local')->count() + 1;
    }
    private function proximoIndiceAdmin(): int
    {
        $max = Usuario::where('email', 'like', 'admin%@sgei.local')
            ->get()
            ->map(fn($u) => (int) substr($u->email, 5, 2))
            ->max();

        return $max ? $max + 1 : 1;
    }

    // ===== HELPERS =====

    private function crearPersona($faker, int $dni, int $indiceContacto): Persona
    {
        $nacion = Nacion::inRandomOrder()->first();
        $email = sprintf('persona%06d@test.local', $indiceContacto);

        $persona = Persona::firstOrCreate(
            ['documento_tipo_id' => 1, 'documento_numero' => (string) $dni],
            [
                'documento_situacion_id' => $faker->randomElement([1, 2, 3, 5]),
                'sexo_id' => $faker->randomElement([1, 2]),
                'genero_id' => $faker->randomElement([1, 2]),
                'nacionalidad_nacion_id' => $nacion?->id,
                'nacion_id' => $nacion?->id,
                'apellido' => $faker->lastName(),
                'nombre' => $faker->firstName(),
                'vive_si' => true,
                'nacimiento_fecha' => $faker->date(),
            ]
        );

        // Contacto
        Contacto::firstOrCreate(
            ['persona_id' => $persona->id],
            [
                'telefono_codigo_area' => $faker->numerify('####'),
                'telefono' => $faker->numerify('#######'),
                'celular_codigo_area' => $faker->numerify('####'),
                'celular' => $faker->numerify('#######'),
                'email' => $email,
            ]
        );

        // Domicilio (calle_id es FK a calles)
        $localidad = Localidad::inRandomOrder()->first();
        $calle = Calle::inRandomOrder()->first();
        Domicilio::firstOrCreate(
            ['persona_id' => $persona->id],
            [
                'localidad_id' => $localidad?->id,
                'calle_id' => $calle?->id,
                'numero' => (string) $faker->numberBetween(1, 5000),
                'piso' => $faker->optional(0.5)->numberBetween(1, 15),
                'departamento' => $faker->optional(0.5)->randomLetter(),
                'codigo_postal' => $faker->numerify('####'),
            ]
        );

        return $persona;
    }

    private function crearUsuarioVinculado(Persona $persona, string $password, bool $esAdmin = false, ?string $email = null): Usuario
    {
        $contacto = $persona->contacto;
        $email = $email ?? $contacto->email;

        $usuario = Usuario::firstOrCreate(
            ['email' => $email],
            [
                'nombre' => $persona->nombre . ' ' . $persona->apellido . ' (' . $persona->documentoNumeroRaw() . ')',
                'documento_tipo_id' => $persona->documento_tipo_id,
                'documento_numero' => $persona->documentoNumeroRaw(),
                'email' => $email,

                'email_verified_at' => now(),
                'password' => $password,
                'password_set' => true,
                'estado' => 'activo',
            ]
        );

        // es_administrador NO es mass-assignable → forceFill
        if ($esAdmin) {
            $usuario->forceFill(['es_administrador' => true])->save();
        }

        // Vincular persona
        $persona->update(['usuario_id' => $usuario->id]);

        return $usuario;
    }

    private function crearUsuarioPendienteVinculacion(Persona $persona, string $password): Usuario
    {
        $contacto = $persona->contacto;

        return Usuario::firstOrCreate(
            ['email' => $contacto->email],
            [
                'nombre' => $persona->nombre . ' ' . $persona->apellido,
                'documento_tipo_id' => $persona->documento_tipo_id,
                'documento_numero' => $persona->documentoNumeroRaw(),
                'email' => $contacto->email,
                'email_verified_at' => now(),
                'password' => $password,
                'password_set' => true,
                'estado' => 'vinculacion_pendiente',
            ]
        );
        // NO se vincula persona.usuario_id (queda null)
    }

    private function crearUsuarioSueltos(string $estado, ?\DateTimeInterface $emailVerifiedAt, string $password, int $indiceUsuario): Usuario
    {
        $email = sprintf('usuario%06d@test.local', $indiceUsuario);

        $data = [
            'nombre' => fake()->name() . ' (' . (91000000 + $indiceUsuario) . ')',
            'documento_tipo_id' => 1,
            'documento_numero' => (string) (91000000 + $indiceUsuario),
            'email' => $email,
            'email_verified_at' => $emailVerifiedAt,
            'password' => $password,
            'password_set' => $emailVerifiedAt !== null,
            'estado' => $estado,
        ];

        if ($estado === 'esperando_activacion') {
            $data['verification_token'] = Str::random(40);
            $data['verification_token_created_at'] = now();
        }

        return Usuario::firstOrCreate(['email' => $email], $data);
    }

    private function asignarRolEscolar(Persona $persona, ?Escuela $escuela, int $roleId): void
    {
        if (!$escuela) {
            return;
        }
        EscuelaPersona::firstOrCreate(
            ['escuela_id' => $escuela->id, 'persona_id' => $persona->id],
            [
                'role_id' => $roleId,
                'verified_at' => now(),
            ]
        );
    }
}
