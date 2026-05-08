# Checklist de requisitos x2,5 (dónde está y dónde se llama)

> He multiplicado **los mínimos numéricos** por **2,5** y he dejado intacta la parte de ubicación/uso en código.

## 1. Debe tener una parte pública y otra privada (pública sin autenticación)

**Dónde está**
- `routes/web.php`
  - Pública: `/`, `/contact`, `/privacy-policy`, `/terms-of-service`, `cars.index`, `cars.show`, `seller.show`
  - Privada: grupo `Route::middleware('auth')->group(...)`
- `routes/auth.php`
  - Público: `login`, `register`, `forgot-password` (`middleware('guest')`)
  - Privado: `logout`, `verify-email`, etc. (`middleware('auth')`)
- `routes/api.php`
  - Público: `POST /api/login`, `GET index/show` de catálogos y coches
  - Privado: grupo `Route::middleware('auth:sanctum')->group(...)`

**Dónde se llama / aplica**
- Middleware en rutas (`auth`, `auth:sanctum`, `verified`, `role:*`)
- Además en controladores, por ejemplo `CarsController::__construct()` protege todo excepto `index/show`.

---

## 2. Las rutas deberán estar ordenadas

**Dónde está**
- `routes/web.php` organizado por bloques:
  - públicas generales
  - resource coches
  - grupo `auth`
  - subgrupos por rol (`admin`, `supervisor|admin`, `soporte|admin`)
- `routes/api.php` organizado por:
  - auth pública (`/login`)
  - lectura pública (`index/show`)
  - bloque protegido `auth:sanctum` con CRUDs completos

**Dónde se llama / aplica**
- Laravel carga rutas desde `bootstrap/app.php` (`withRouting(web:..., api:...)`).

---

## 3. Roles y/o permisos (Laravel Permission) → **5 roles** (antes 2)

individual, dealership, admin, supervisor, soporte

permisos:
- `create cars`
- `crud own cars`
- `buy cars`
- `crud all cars`
- `all access`
- `offers for companies`
- `offers for individuals`
- `view cars`
- `view users data`
- `view customers data`

**Dónde está**
- Definición de roles/permisos: `database/seeders/RolesAndPermissionsSeeder.php`
- Trait de roles en usuario: `app/Models/User.php` (`HasRoles`)
- Alias middleware Spatie: `bootstrap/app.php` (`role`, `permission`, `role_or_permission`)

**Dónde se llama / aplica**
- Asignación de rol al registrar: `app/Actions/Fortify/CreateNewUser.php`
- Asignación desde soporte: `app/Http/Controllers/SupportController.php`
- Restricción por rol en rutas: `routes/web.php` (`role:admin`, etc.)
- Uso en policies: `app/Policies/*.php` (`hasRole`, `can`)

---

## 4. Policies de acceso con contenido → **5 policies** (antes 2)

**Dónde está**
- `app/Policies/CarsPolicy.php`
- `app/Policies/OfferPolicy.php`
- `app/Policies/RentalPolicy.php`
- `app/Policies/UserPolicy.php`
- `app/Policies/SalesPolicy.php`
- `app/Policies/CustomersPolicy.php`

**Dónde se llama / aplica**
- Desde controladores con `authorize(...)`:
  - `app/Http/Controllers/CarsController.php`
  - `app/Http/Controllers/OfferController.php`
  - `app/Http/Controllers/SupportController.php`
- Desde FormRequest con `authorize()` + `can(...)`:
  - `app/Http/Requests/StoreCarRequest.php`
  - `app/Http/Requests/UpdateCarRequest.php`

---

## 5. API en paralelo → **8 modelos**, **5 CRUDs completos**, **3 recursos con autenticación** (antes 3,2,1)

**Dónde está**
- Rutas API: `routes/api.php`
- Controladores API con CRUD completo (`index/store/show/update/destroy`):
  - `app/Http/Controllers/Api/CarsController.php`
  - `app/Http/Controllers/Api/OfferController.php`
  - `app/Http/Controllers/Api/SalesController.php`
  - `app/Http/Controllers/Api/CustomersController.php`
  - `app/Http/Controllers/Api/BrandsController.php`
  - `app/Http/Controllers/Api/CarModelsController.php`
  - `app/Http/Controllers/Api/FuelsController.php`
  - `app/Http/Controllers/Api/ColorsController.php`
  - `app/Http/Controllers/Api/GearsController.php`

**Dónde se llama / aplica**
- Público: `POST /api/login`, `GET index/show` públicos
- Protegido: grupo `auth:sanctum` en `routes/api.php` para create/update/delete y otros endpoints privados
- Auth API: `app/Http/Controllers/Api/AuthController.php` (`login/logout` con token Sanctum)

---

## 6. Comandos → **8 comandos** y **3 llamados desde código** (antes 3 y 1)

**Dónde está**
- Comandos en `app/Console/Commands/` (hay 10)

**Dónde se llama / aplica**
- Llamadas desde código (`Artisan::call(...)`):
  - `app/Http/Controllers/SalesController.php` (`sales:export`)
  - `app/Http/Controllers/SupervisorController.php` (`cars:approve`)
  - `app/Http/Controllers/AdminController.php` (varios comandos)
- Programación (scheduler):
  - `routes/console.php` (`Schedule::command('rentals:process-daily')->dailyAt('08:00')`)

---

## 7. Eventos y listeners asociados → **5 eventos** + **5 listeners** (antes 2 y 2)

**Dónde está**
- Eventos: `app/Events/` (`CarCreated`, `OfferCreated`, `SaleCompleted`, `RentalPaid`, `CarRejected`)
- Listeners: `app/Listeners/` (`LogCarCreation`, `NotifySeller`, `NotifySaleParticipants`, `NotifyRentalParticipants`, `NotifyCarRejection`)
- Registro evento-listener: `app/Providers/AppServiceProvider.php` (método `boot`)

**Dónde se llama / aplica**
- Dispatch en controladores:
  - `CarsController` -> `CarCreated::dispatch(...)`
  - `OfferController` -> `OfferCreated::dispatch(...)`, `SaleCompleted::dispatch(...)`
  - `RentalController` -> `RentalPaid::dispatch(...)`
  - `SupervisorController` -> `CarRejected::dispatch(...)`

---

## 8. Jobs (colas) → **5 jobs** y **3 usando colas** (antes 2 y 1)

**Dónde está**
- Jobs en `app/Jobs/` (`SendOfferNotificationJob`, `SendSaleProcessedJob`, `ProcessCarImageJob`, etc.)

**Dónde se llama / aplica**
- Dispatch en listeners/controladores/comandos:
  - Listeners: `NotifySeller`, `NotifySaleParticipants`, `NotifyRentalParticipants`, `NotifyCarRejection`
  - Controladores: `CarsController`, `OfferController`, `AdminController`
  - Comandos: `ApproveCar`, `CheckOverdueRentals`, `AutoRejectLowOffers`
- Uso de cola:
  - Config de testing usa `QUEUE_CONNECTION=sync` en `phpunit.xml`, pero la arquitectura está preparada para jobs en cola.

---

## 9. Enviar emails → **5 emails** (antes 2)

**Dónde está**
- Mailables: `app/Mail/` (`SaleProcessed`, `RentalProcessed`, `OfferAccepted`, `OfferRejected`, `NewOfferReceived`, etc.)
- Vistas email: `resources/views/emails/`

**Dónde se llama / aplica**
- Vía jobs/listeners (patrón principal)
- En directo desde `RentalController` con `Mail::to(...)->send(...)` (`NewRentalRequest`, `RentalAccepted`, `RentalRejected`)

---

## 10. Livewire → **8 clases**, **5 CRUD** (antes 3 y 2)

**Dónde está**
- Clases Livewire en `app/Livewire/` (hay 10):
  - Admin CRUD: `Admin/BrandManager`, `Admin/ModelManager`, `Admin/FuelManager`, `Admin/ColorManager`, `Admin/GearManager`, `Admin/UserManager`
  - Otras: `MakeOffer`, `ToggleFavorite`, `CarFilter`, `Admin/Dashboard`

**Dónde se llama / aplica**
- En vistas:
  - `resources/views/livewire/admin/dashboard.blade.php` (`<livewire:admin.*-manager />`)
  - `resources/views/cars/show.blade.php` (`<livewire:toggle-favorite>`, `<livewire:make-offer>`)

---

## 11. Usar Pest (cobertura al menos 85%)

**Dónde está**
- Config Pest: `tests/Pest.php`
- Suite de tests amplia: `tests/Feature/**`, `tests/Unit/**`
- Cobertura sobre `app`: `phpunit.xml` (`<source><include><directory>app</directory>`)

**Dónde se llama / aplica**
- Se ejecuta con Pest/PHPUnit en el pipeline local (`tests/`).
- Nota: el umbral exacto `>=85%` no está fijado explícitamente en `phpunit.xml`; se verifica al ejecutar cobertura.

---

## 12. Traducciones → **5 idiomas** (antes 2)

**Dónde está**
- Cambio de idioma: `routes/web.php` (`lang/{locale}` con `en` y `es`)
- Middleware de locale: `app/Http/Middleware/SetLocale.php`
- Archivo de traducción actual: `lang/es.json`
- Strings con `__()` en vistas/controladores

**Dónde se llama / aplica**
- Ruta `lang.switch` guarda cookie de locale
- Middleware `SetLocale` aplica locale por cookie/sesión en cada request web

---

## 13. Componentes de vistas (Input, Fechas, Select, labels, checkbox) → **13 componentes mínimos**

**Dónde está**
- Componentes: `resources/views/components/`
  - `input.blade.php`, `date.blade.php`, `select.blade.php`, `label.blade.php`, `checkbox.blade.php`, etc. (hay más de 13)

**Dónde se llama / aplica**
- `resources/views/auth/register.blade.php`
- `resources/views/rentals/create.blade.php`
- `resources/views/support/users/edit.blade.php`
- Formularios de perfil/auth usan múltiples `x-*` componentes

---

## 14. PDFs → **5 PDFs**, **3 complejos** (antes 2 y 1)

**Dónde está**
- Plantillas PDF: `resources/views/pdf/`
  - `sale_receipt`, `rental_receipt`, `sale_terms`, `rental_terms`, `supervisor_report`, `certificate`

**Dónde se llama / aplica**
- `app/Http/Controllers/SalesController.php` (`Pdf::loadView(...)`)
- `app/Http/Controllers/RentalController.php` (`rental_terms`)
- `app/Http/Controllers/SupervisorController.php` (`supervisor_report`, complejo)

---

## 15. FormRequest para formularios > 2 campos (se mantiene)

**Dónde está**
- Web Requests: `app/Http/Requests/`
- API Requests: `app/Http/Requests/Api/`

**Dónde se llama / aplica**
- Inyección tipada en controladores web/API.
- Nota: aún hay validación inline con `$request->validate(...)` en algunos controladores.

---

## 16. Scopes → **8 scopes**, **5 complejos** (antes 3 y 2)

**Dónde está**
- `app/Models/Cars.php`: `scopeAvailable`, `scopeBySeller`, `scopeFilter`, `scopeSearch`, `scopeRecent`, `scopeCheap`
- `app/Models/Rental.php`: `scopeOverlapping`, `scopeActive`
- `app/Models/Offer.php`: `scopePending`, `scopeForSeller`
- `app/Models/Sales.php`: `scopeMonthlyReport`
- `app/Models/User.php`: `scopeActiveTraders`

**Dónde se llama / aplica**
- `CarsController@index` usa varios scopes
- `CarsController@myCars` usa `bySeller`
- `RentalController@store` usa `Rental::overlapping(...)`
- Flujos de ofertas usan scopes de `Offer`

---

## 17. Relaciones 1:N y N:N (pivote con columnas extra) → **al menos 3 pivotes con extras**

**Dónde está**
- 1:N:
  - `Customers::cars()` y `Cars::vendedor()`
  - `Cars::offers()` / `Offer::car()`
  - `Cars::rentals()` / `Rental::car()`
  - `Brands::models()`
- N:N con pivote:
  - `Cars::bidders()` <-> `Customers::bidCars()` con `offers`
    - pivote: `cantidad`, `estado`, `id_vendedor`
  - `Cars::renters()` <-> `Customers::rentedCars()` con `rentals`
    - pivote: `fecha_inicio`, `fecha_fin`, `precio_total`, `id_estado`
- Migraciones:
  - `database/migrations/2026_01_10_000000_offers.php`
  - `database/migrations/2026_01_18_000000_create_rentals_table.php`
  - `database/migrations/2026_01_18_000004_create_rental_statuses_table.php`

**Dónde se llama / aplica**
- `OfferController` crea/actualiza estado de ofertas
- `RentalController` crea/actualiza fechas/precio/estado de rentals
- Favoritos N:N adicional: `User::favorites()` <-> `Cars::favoritedBy()` (`favorites`)

---

## 18. Documentación funcional de API → **cobertura ampliada de endpoints**

**Dónde está**
- Configuración Scribe: `config/scribe.php`
- Vista docs: `resources/views/scribe/index.blade.php`
- Anotaciones en controladores API (`@group`, `@authenticated`)
- Salida en `public/docs` y ruta `/docs`

**Dónde se llama / aplica**
- Scribe escanea rutas `api/*`
- Genera docs HTML/Postman/OpenAPI

---

## 19. Usar GIT

**Dónde está**
- Proyecto versionado en Git.

**Dónde se llama / aplica**
- Flujo de desarrollo y control de cambios (commits/branches/histórico).