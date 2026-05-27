<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <style>
        body        { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .card       { background: #fff; border-radius: 8px; padding: 32px; max-width: 520px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header     { font-size: 22px; font-weight: bold; color: #2d3748; margin-bottom: 8px; }
        .label      { font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: .05em; margin-top: 20px; }
        .value      { font-size: 16px; color: #2d3748; margin-top: 4px; }
        .icon-box   { font-size: 40px; margin-bottom: 16px; }
        .footer     { margin-top: 32px; font-size: 12px; color: #a0aec0; }
        hr          { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">{{ $icon }}</div>
        <div class="header">Nieuwe functie toegevoegd aan de bibliotheek</div>
        <p style="color:#4a5568;">Een administrator heeft een nieuwe functie toegevoegd. Pas indien nodig je tabellen aan.</p>

        <hr>

        <div class="label">Naam</div>
        <div class="value">{{ $functionName }}</div>

        <div class="label">Categorie</div>
        <div class="value">{{ ucfirst($category) }}</div>

        @if($description)
        <div class="label">Omschrijving</div>
        <div class="value">{{ $description }}</div>
        @endif

        <hr>

        <div class="footer">
            Dit bericht is automatisch verzonden door het Metropolis-systeem.<br>
            Je ontvangt deze mail omdat je als stadsplanningexpert bent geregistreerd.
        </div>
    </div>
</body>
</html>
