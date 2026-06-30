# AGENTS.md — project-pi

## What this is

Laravel 10 (PHP 8.1+) university academic system: KRS (course registration) + face-recognition attendance.

## Setup & dev commands

```bash
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed     # seed order: Roles → Permissions → User
npm install && npm run dev                      # Vite dev server
```

| Command | Purpose |
|---|---|
| `php artisan serve` | Laravel dev server |
| `./vendor/bin/phpunit` | Run tests (Unit + Feature suites) |
| `./vendor/bin/pint` | PHP code style fixer |
| `npm run build` | Vite production build |

## Seeded super admin

`sa@gmail.com` / `password` — created by `UserTableSeeder`, assigned the `super admin` role (created in `RolesTableSeeder`).

## Route layout

All internal routes live under `/internal` (prefix group in `routes/web.php`). **Auth middleware is commented out** — not enforced. `Auth::routes()` is called (Laravel UI scaffold). Post-login redirect: `/internal/dashboard`.

## Key packages & their use

- **yajra/laravel-datatables** — server-side AJAX tables; every `data()` controller method returns `datatables()->of(...)->toJson()`
- **spatie/laravel-permission** — RBAC via `Role`/`Permission` models; middleware aliases `role`, `permission`, `role_or_permission` registered in `Kernel.php`
- **laravel/ui** — Bootstrap auth scaffolding (`php artisan ui bootstrap --auth`)
- **laravel/sanctum** — API token auth (`routes/api.php` has one guarded route)

## Domain models

```
Fakultas → hasMany Jurusan
Jurusan  → belongsTo Fakultas, hasMany Mahasiswa, hasMany Dosen, hasMany MataKuliah
Mahasiswa → belongsTo Jurusan, hasMany Krs; stores face_descriptor (JSON)
Dosen     → belongsTo Jurusan
MataKuliah → belongsTo Jurusan, belongsTo Dosen
Krs       → belongsTo Mahasiswa, belongsTo MataKuliah
RiwayatAbsensi → (no model; raw DB queries on table `riwayat_absensi`)
```

## Developer gotchas

- **Timezone is `Asia/Jakarta`**, locale `id` — semester auto-determination in `KrsController::tentukanSemester()` uses these
- **Mix of Eloquent and raw DB queries** — some controllers use `DB::table()`/`DB::insert()` directly (especially `FaceController`, `KrsController`)
- **KRS quota logic** — `MataKuliah.kuota_orang` is decremented on KRS insert, incremented on delete
- **Face attendance** — `face_descriptor` stored as JSON in `mahasiswa` table; geo-radius check (50m) in `FaceController`; `/descriptors` endpoint returns all for client-side matching
- **Select2 patterns** — dependent dropdowns via AJAX endpoints (`listJurusan`, `listFakultas`, `listDosenByJurusan`, `listFakultasByJurusan`) returning `{id, text}` format
- **Test DB** — SQLite in-memory config commented out in `phpunit.xml`; `DB_CONNECTION=mysql` by default
- **Frontend** — Bootstrap 5, SASS (`resources/sass/app.scss`), Vite with `laravel-vite-plugin`
- **Permission seeder** (`PermissionsTableSeeder`) is mostly commented out — only 3 permissions are active (pengaturan wajah, krs, absensi)
- **No CI/CD, no pre-commit hooks, no type checking** configured
