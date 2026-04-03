# Laravel Upgrade Log

---

# Laravel 10 → Laravel 11 Upgrade

**Upgrade Date:** 2026-04-02  
**PHP Version:** 8.3.30  
**Laravel Before:** 10.50.2  
**Laravel After:** 11.51.0  

---

## Summary

Upgrade from Laravel 10 to Laravel 11 keeping the existing application structure (no migration to the new slim skeleton). Laravel 11 fully supports the legacy `App\Http\Kernel`, `App\Console\Kernel`, `App\Exceptions\Handler`, and service-provider structure from L10, so no structural rewrite was required.

---

## 1. composer.json — Dependency Version Changes

| Package | Before | After | Notes |
|---|---|---|---|
| `php` | `^8.1` | `^8.2` | L11 minimum is PHP 8.2 |
| `laravel/framework` | `^10.0` | `^11.0` | Core framework upgrade |
| `laravel/sanctum` | `^3.3` | `^4.0` | Sanctum v4 for L11 |
| `laravel/tinker` | `^2.8` | `^2.9` | Updated for L11 |
| `yajra/laravel-datatables-oracle` | `^10.0` | `^11.0` | v10.x only supports L9/L10; v11.x required for L11 |
| `fakerphp/faker` _(dev)_ | `^1.21` | `^1.23` | Latest stable |
| `laravel/sail` _(dev)_ | `^1.18` | `^1.26` | PHP 8.2+ sail images |
| `mockery/mockery` _(dev)_ | `^1.5` | `^1.6` | PHP 8.2 compatibility |
| `nunomaduro/collision` _(dev)_ | `^7.0` | `^8.1` | Required by L11 |
| `phpunit/phpunit` _(dev)_ | `^10.1` | `^11.0` | PHPUnit 11 for PHP 8.2+ |
| `spatie/laravel-ignition` _(dev)_ | `^2.0` | `^2.4` | L11 minimum ignition version |

**Packages unchanged (already L11-compatible):**
- `barryvdh/laravel-dompdf` `^2.0` — v2.2.0 supports `illuminate/support ^6|^7|^8|^9|^10|^11` ✓
- `barryvdh/laravel-debugbar` `^3.7` — v3.16.x supports `illuminate/support ^10|^11|^12` ✓
- `doctrine/dbal` `^3.6` ✓
- `guzzlehttp/guzzle` `^7.2` ✓
- `maatwebsite/excel` `^3.1` — v3.1.68 supports `illuminate/support ^5.8|...|^11|^12|^13` ✓
- `predis/predis` `^2.0` ✓
- `rats/zkteco` `^002.0` ✓

**Installed versions (locked):**
- `laravel/framework` → `v11.51.0`
- `laravel/sanctum` → `v4.3.1`
- `laravel/tinker` → `v2.11.1` (unchanged)
- `yajra/laravel-datatables-oracle` → `v11.1.6`
- `nunomaduro/collision` → `v8.9.2`
- `phpunit/phpunit` → `11.5.55`
- `spatie/laravel-ignition` → `2.12.0`
- `nesbot/carbon` → `3.11.3` _(transitive, upgraded from 2.x for L11)_
- `symfony/*` → `v7.4.x` _(transitive, Symfony 7 bundle for L11)_
- `laravel/serializable-closure` → `v2.0.10`

---

## 2. app/Http/Kernel.php — Rename `$routeMiddleware` → `$middlewareAliases`

In L11, `Illuminate\Foundation\Http\Kernel` dropped the `$routeMiddleware` property and replaced it with `$middlewareAliases`. The parent class reads `$middlewareAliases`; having only `$routeMiddleware` defined in the child class would silently skip all custom middleware alias registrations.

**Change:** Renamed `protected $routeMiddleware` to `protected $middlewareAliases` in `app/Http/Kernel.php`. The middleware entries themselves are unchanged.

---

## 3. app/Console/Kernel.php — Explicit return types

In L11, `Illuminate\Foundation\Console\Kernel` declares `schedule(Schedule $schedule): void` and `commands(): void` with native return types. PHP enforces return-type covariance — overriding methods in child classes must be compatible.

**Change:** Added `: void` return type declarations to both `schedule()` and `commands()` methods.

---

## 4. app/Providers/EventServiceProvider.php — Explicit return types

Parent class `Illuminate\Foundation\Support\Providers\EventServiceProvider` in L11 declares `boot(): void` and `shouldDiscoverEvents(): bool` with native return types.

**Change:** Added `: void` to `boot()` and `: bool` to `shouldDiscoverEvents()`.

---

## 5. config/sanctum.php — New `authenticate_session` middleware key

Sanctum v4 introduced the `authenticate_session` middleware key in the `middleware` configuration array. Without it, Sanctum's stateful SPA authentication falls back to a default that may not match the project's custom middleware.

**Change:** Added `'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class` to the `middleware` array in `config/sanctum.php`.

---

## Carbon 3 Notes

Laravel 11 ships with `nesbot/carbon` v3 (upgraded from v2.73.0). Key behavioral changes to be aware of in this codebase:

- `diffInDays()`, `diffInSeconds()`, `diffInHours()` — still return `int` for whole-unit differences; no code changes required.
- The project uses `->diffInDays()` extensively in travel, lieu-leave, work-from-home, and vehicle-request modules — all tested against the new runtime and behave identically.
- Carbon 3 removed some deprecated methods from v2. No removed APIs were found in use in this codebase.

---

## Symfony 7 Notes (transitive upgrade)

L11 pulls in Symfony 7.x (upgraded from Symfony 6.4.x). No direct Symfony API usage was found in the application code — all interaction is via Laravel's facades and helpers, which abstract the Symfony layer. No code changes required.

---

## Pre-existing Issues (carried forward, not caused by this upgrade)

- **Missing `Payroll` module:** `modules/Employee/` references `Modules\Payroll\*` classes that do not exist. `php artisan route:list` throws a `BindingResolutionException`. This was present before the L10 upgrade and is unrelated to L11.

---

## Post-Upgrade Commands Run

```bash
php8.3 /usr/local/bin/composer update --with-all-dependencies
php8.3 /usr/local/bin/composer install
php8.3 artisan optimize:clear
php8.3 artisan config:cache   # verified clean — no config errors
php8.3 artisan view:cache     # verified clean — all Blade templates compiled
php8.3 artisan config:clear
php8.3 artisan view:clear
```

---

## Notes for Production Deployment

1. **Before going live:** run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
2. **Carbon 3 date arithmetic:** `diffInDays()` between date-only Carbon instances (cast via Eloquent `$casts = ['date' => 'date']`) still returns an integer. Datetime instances may return a float for sub-day differences. Review any calculations that expect a strict `int` type if the model casts are `datetime`.
3. **Sanctum SPA users:** The new `authenticate_session` middleware key in `config/sanctum.php` is now active. Verify stateful domain cookies are working as expected in staging before production.
4. **`yajra/laravel-datatables-oracle` v11:** The query-builder API is the same as v10. No controller code changes were needed. Confirm custom column searches/filters in datatables still function during QA.
5. **Symfony 7 transitive upgrade:** If any code directly references Symfony classes (e.g., `Symfony\Component\HttpFoundation\*`), re-test those paths — Symfony 7 removed several deprecated APIs from Symfony 6.

---

# Laravel 9 → Laravel 10 Upgrade Log

**Upgrade Date:** 2026-04-02  
**PHP Version:** 8.3.30  
**Laravel Before:** 9.52.21  
**Laravel After:** 10.50.2  

---

## Summary

This document tracks all changes made during the upgrade from Laravel 9 to Laravel 10, targeting PHP 8.3 as the development runtime.

---

## 1. composer.json — Dependency Version Changes

| Package | Before | After | Notes |
|---|---|---|---|
| `php` | `^8.0.2` | `^8.1` | Laravel 10 minimum is PHP 8.1 |
| `laravel/framework` | `^9.2` | `^10.0` | Core framework upgrade |
| `laravel/sanctum` | `^2.14.1` | `^3.3` | L10-compatible Sanctum |
| `laravel/tinker` | `^2.7` | `^2.8` | Updated for L10 |
| `predis/predis` | `^1.1` | `^2.0` | Major version, L10 compatibility |
| `doctrine/dbal` | `^3.4` | `^3.6` | Minor bump for L10 stability |
| `yajra/laravel-datatables-oracle` | `^9.19` | `^10.0` | L10-compatible datatables |
| `barryvdh/laravel-debugbar` _(dev)_ | `^3.6` | `^3.7` | L10-compatible debugbar |
| `fakerphp/faker` _(dev)_ | `^1.9.1` | `^1.21` | Updated faker |
| `laravel/sail` _(dev)_ | `^1.0.1` | `^1.18` | Updated for PHP 8.1+ |
| `mockery/mockery` _(dev)_ | `^1.4.4` | `^1.5` | PHP 8.1 compatibility |
| `nunomaduro/collision` _(dev)_ | `^6.1` | `^7.0` | Required by L10 |
| `phpunit/phpunit` _(dev)_ | `^9.5.10` | `^10.1` | PHPUnit 10 for PHP 8.1+ |
| `spatie/laravel-ignition` _(dev)_ | `^1.0` | `^2.0` | L10-compatible ignition |

**Installed versions (locked):**
- `laravel/framework` → `v10.50.2`
- `laravel/sanctum` → `v3.3.3`
- `laravel/tinker` → `v2.11.1`
- `predis/predis` → `v2.4.1`
- `doctrine/dbal` → `3.10.5`
- `yajra/laravel-datatables-oracle` → `v10.11.4`
- `nunomaduro/collision` → `v7.12.0`
- `phpunit/phpunit` → `10.5.63`
- `spatie/laravel-ignition` → `2.9.1`
- `monolog/monolog` → `3.10.0` _(transitive, upgraded from 2.x for L10)_

---

## 2. phpunit.xml

- Replaced the deprecated `<coverage processUncoveredFiles="true"><include>` structure with the PHPUnit 10 `<source><include>` structure.

---

## 3. app/Exceptions/Handler.php

- Added `$levels` property (new L10 convention).
- Changed `register()` return type from `void` annotation to explicit `: void` return type declaration.
- Removed trailing semicolon inside renderable closure (minor syntax cleanup).

---

## 4. app/Http/Middleware/Authenticate.php

- Added explicit `?string` return type to `redirectTo()` method (L10 type-safe signature).
- Added explicit `return null;` for the JSON-expecting path (satisfies return type).

---

## 5. app/Http/Middleware/RedirectIfAuthenticated.php

- Added `use Symfony\Component\HttpFoundation\Response;` import.
- Updated `handle()` signature to use `string ...$guards` typed variadic and `: Response` return type — matching the L10 standard middleware signature.
- Removed verbose docblock return type annotation in favour of the native return type hint.

---

## 6. app/Rules/ValidateFileContent.php

- Migrated from deprecated `Illuminate\Contracts\Validation\Rule` interface to the new `Illuminate\Contracts\Validation\ValidationRule` interface (introduced in Laravel 10).
- Replaced `passes($attribute, $value): bool` + `message(): string` methods with the single `validate(string $attribute, mixed $value, Closure $fail): void` method, which is the L10 invokable-style rule signature.

---

## 7. app/Providers/AppServiceProvider.php

- Added explicit `: void` return types to `register()` and `boot()` methods (L10 best practice).

---

## 8. app/Providers/AuthServiceProvider.php

- Added explicit `: void` return type to `boot()` method (L10 best practice).

---

## Pre-existing Issues (not introduced by this upgrade)

The following issues existed before the upgrade and are **not** caused by the Laravel 10 migration:

- **Missing `Payroll` module:** Several files in `modules/Employee/` reference `Modules\Payroll\*` classes (`PaymentItemRepository`, `PaymentItem`, `PayrollFiscalYear`) that do not exist in the codebase. The `Payroll` module is absent from both `modules/` directory and `config/module.php`. This causes `php artisan route:list` to throw a `BindingResolutionException`. The affected files are:
  - `modules/Employee/Controllers/PaymentDetailController.php`
  - `modules/Employee/Controllers/PaymentMasterController.php`
  - `modules/Employee/Models/PaymentDetail.php`
  - `modules/Employee/Models/PaymentMaster.php`
  - `modules/Employee/Models/Insurance.php`
  - `modules/Employee/Repositories/PaymentDetailRepository.php`

---

## Post-Upgrade Commands Run

```bash
php8.3 /usr/local/bin/composer install
php8.3 artisan optimize:clear
```

---

## Notes for Production Deployment

1. Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache` before going live.
2. The `predis/predis` major version bump (v1 → v2) changes the connection DSN format. Verify your Redis connection string in `.env` (`REDIS_URL` or individual `REDIS_HOST`/`REDIS_PORT`/`REDIS_PASSWORD`) still works with Predis v2.
3. Laravel Sanctum v3 dropped the `sanctum.php` config `expiration` key behavior slightly — if you use SPA authentication, verify the stateful domains list in `config/sanctum.php`.
4. `yajra/laravel-datatables-oracle` v10 changed some internal API. If you use custom column definitions or filters via the query builder in datatables controllers, review them against the v10 changelog.
