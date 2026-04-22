# Metropolis van de Toekomst

Laravel webapplicatie voor het simuleren van ruimtelijke planning in een stadsgebied.
Gemaakt voor Metropolis casus (LU3).

## Wat het doet

- **Stadsgebied**: 3×4 grid van gebiedsdelen (US.1)
- **Functiebibliotheek**: bestemmingen gegroepeerd per categorie (US.2)
- **Functie plaatsen**: via dropdown per lege cel (US.4)
- **Opslaan**: alles wordt opgeslagen in MySQL (US.5 + US.9)
- **Accessibility**: semantische HTML + ARIA labels (US.6)
- **Responsive**: werkt op mobiel 320px+ (US.7)
- **Verwijderen**: met knop op bezette cel (US.8)
- **Zoeken**: filter op naam (US.10)

## Setup lokaal

1. Zorg dat je Laravel 13 / PHP 8.4 hebt
2. Maak een nieuw Laravel project: `composer create-project laravel/laravel metropolis`
3. Kopieer de bestanden uit dit project over de standaard Laravel structuur heen
4. Kopieer `.env.example` naar `.env` en vul je database-gegevens in
5. Run:
   ```
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```

## Upload via FileZilla naar de server

1. Maak op de server via je hosting-panel een MySQL database aan (bv. `metropolis`)
2. Open FileZilla en verbind met de server
3. Upload **alle** mappen en bestanden naar de hoofdmap van je server
   (vaak `public_html/` of `httpdocs/`)

   **LET OP**: Upload ook de standaard Laravel mappen die al op je project staan:
   - `app/` (controllers + models staan hierin)
   - `bootstrap/`
   - `config/`
   - `database/` (migraties + seeders staan hierin)
   - `public/` (hier staat de CSS én de index.php)
   - `resources/` (blade views staan hierin)
   - `routes/`
   - `storage/`
   - `vendor/` (vanuit je lokale composer install)
   - `.env` (met ingevulde database-gegevens!)
   - `artisan`
   - `composer.json`

4. Op de server: open de terminal (SSH) of gebruik de artisan via je hosting-panel:
   ```
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:clear
   php artisan cache:clear
   ```

5. Zorg dat de `storage/` en `bootstrap/cache/` mappen schrijfrechten hebben (chmod 775)

6. Als de server niet naar `public/` wijst: verplaats inhoud van `public/` naar de webroot
   en pas de paden in `public/index.php` aan.

## Bestanden die je moet kopiëren

```
app/Http/Controllers/MetropolisController.php
app/Models/Destination.php
app/Models/GridCell.php
database/migrations/2025_01_01_000001_create_destinations_table.php
database/migrations/2025_01_01_000002_create_grid_cells_table.php
database/seeders/DatabaseSeeder.php
database/seeders/DestinationSeeder.php
database/seeders/GridCellSeeder.php
resources/views/layouts/app.blade.php
resources/views/metropolis.blade.php
resources/views/partials/grid.blade.php
resources/views/partials/library.blade.php
resources/views/partials/effecten.blade.php
public/css/style.css
routes/web.php
.env (zelf maken op basis van .env.example)
```

## Testen

1. Ga naar de hoofdpagina → je ziet een leeg 3×4 grid + de functiebibliotheek
2. Kies in een lege cel een functie uit de dropdown en klik "Plaats"
3. De functie verschijnt in de cel en de effecten rechts updaten
4. Klik op de "−" knop op een bezette cel om de functie weg te halen
5. Typ in de zoekbalk om functies te filteren
6. Vernieuw de pagina → alles blijft staan (opslaan werkt!)

## User Stories → code mapping

| US | Waar in de code |
|---|---|
| US.1 | `grid.blade.php` + `style.css` (.grid klasse) |
| US.2 | `library.blade.php` + `MetropolisController::index` |
| US.4 | `MetropolisController::place` + route `/place` |
| US.5 | Automatisch: alle wijzigingen slaan op in MySQL via Eloquent |
| US.6 | ARIA labels + semantische HTML in alle views |
| US.7 | Media queries onderaan `style.css` |
| US.8 | `MetropolisController::remove` + route `/remove` |
| US.9 | Migraties + seeders + Eloquent models |
| US.10 | Zoekfunctie in `MetropolisController::index` + zoekbalk in `library.blade.php` |
