# Grid Dominion — Version 1

A playable browser energy-management tycoon built with Laravel, Blade, vanilla JavaScript, layered SVG/CSS graphics, and Chart.js.

## Requirements

- PHP 8.3+
- Composer 2
- MySQL 8+
- Node.js 20+ and npm (used to compile the SCSS game interface)

## Installation

```bash
composer install
npm install
npm run build
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Create a MySQL database named `grid_dominion` before migrating, or adjust the `DB_*` values in `.env`. For quick local evaluation, SQLite is also supported by setting `DB_CONNECTION=sqlite` and creating `database/database.sqlite`.

The seeder creates a ready-to-play demo operator, Aurora City, twelve build sites, initial gas/solar/battery infrastructure, and historical operating data. Open the root URL to play; no login is required for the V1 demo.

## Play

- Time advances one game hour per real second. Pause it or choose 1×, 2×, or 4×.
- Click gold `+` markers to inspect allowed technologies and begin construction.
- Battery auto mode absorbs surplus and discharges into deficits before market imports.
- Open Analytics for 24-hour energy and financial charts.
- Reach 50,000 population, 98% reliability, 50% renewable energy, and €20M cash.

## Architecture

Simulation orchestration lives in `app/Services/Game/GameEngine.php`, with focused demand, production, battery, market, weather, finance, construction, growth, and event services. Persistence uses indexed relational models for games, cities, plants, batteries, build locations, projects, snapshots, weather, markets, and events.

## Tests

```bash
php artisan test
```

The suite covers demand, all V1 generation types, battery charging/discharging, imports/exports, finance, city growth, construction completion, shortage handling, dashboard rendering, live ticks, and construction requests.

### XAMPP fallback

Some older Windows/XAMPP CLI builds cannot keep PHP's built-in server alive. In that case, the included local CGI bridge can be used for development only:

```bash
node server-dev.cjs
```

Then open `http://127.0.0.1:8017`.

For live SCSS editing during development, run `npm run dev` in a separate terminal. For a static local preview, run `npm run build` after SCSS changes.
