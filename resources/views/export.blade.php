<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Metropolis stadsoverzicht') }} - {{ $datum }}</title>
    <style>
        @php
            $cols = (int) ceil(sqrt($gridSize));
        @endphp

        body {
            font-family: Arial, sans-serif;
            color: #1f1f1f;
            margin: 20px;
            background: #fff;
        }
        h1 {
            color: #2d4a22;
            border-bottom: 3px solid #2d4a22;
            padding-bottom: 8px;
            margin: 0 0 4px;
        }
        .meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }
        h2 {
            color: #2d4a22;
            margin: 24px 0 12px;
            font-size: 1.2rem;
        }
        .totalen {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }
        .totaal {
            border: 1.5px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .totaal-label {
            font-size: 0.8rem;
            color: #555;
            text-transform: uppercase;
            font-weight: 600;
        }
        .totaal-score {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 4px;
        }
        .pos { color: #15803d; border-color: #15803d; background: #f0fdf4; }
        .neg { color: #c0392b; border-color: #c0392b; background: #fef2f2; }
        .neu { color: #888; }

        .grid {
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            gap: 6px;
            margin-bottom: 16px;
        }
        .cel {
            border: 1.5px solid #ccc;
            border-radius: 5px;
            min-height: 70px;
            padding: 6px;
            text-align: center;
            background: #fafafa;
            font-size: 0.85rem;
            page-break-inside: avoid;
        }
        .cel.bezet {
            border-color: #2d4a22;
            background: #e8f5e9;
        }
        .cel-icon {
            font-size: 1.6rem;
            display: block;
        }
        .cel-naam {
            font-weight: 600;
            margin-top: 2px;
            font-size: 0.78rem;
        }
        .cel-leeg {
            color: #aaa;
            font-style: italic;
            font-size: 0.75rem;
        }
        .knoppen {
            margin-bottom: 20px;
        }
        .btn {
            background: #2d4a22;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            margin-right: 8px;
        }
        .btn-grijs { background: #777; }
        .btn:hover { opacity: 0.9; }

        @media print {
            .knoppen { display: none; }
            body { margin: 10px; }
        }
    </style>
</head>
<body>

    <div class="knoppen">
        <button class="btn" onclick="window.print()">{{ __('Opslaan als PDF') }}</button>
        <a class="btn btn-grijs" href="{{ url('/') }}" style="text-decoration:none; display:inline-block;">{{ __('Terug') }}</a>
    </div>

    <h1>{{ __('Metropolis stadsoverzicht') }}</h1>
    <p class="meta">{{ __('Geëxporteerd op') }} {{ $datum }} {{ __('door') }} {{ auth()->user()->name }}</p>

    <h2>{{ __('Totale effecten') }}</h2>
    <div class="totalen">
        @foreach($categories as $cat)
            @php
                $score = $effecten[$cat] ?? 0;
                $klasse = $score > 0 ? 'pos' : ($score < 0 ? 'neg' : 'neu');
                $teken  = $score > 0 ? '+' . $score : (string) $score;
            @endphp
            <div class="totaal {{ $klasse }}">
                <div class="totaal-label">{{ __($cat) }}</div>
                <div class="totaal-score">{{ $teken }}</div>
            </div>
        @endforeach
    </div>

    <h2>{{ __('Stadsgebied') }} ({{ count($gridCells) }} {{ __('cellen') }})</h2>
    <div class="grid">
        @foreach($gridCells as $cell)
            <div class="cel {{ $cell->destination ? 'bezet' : '' }}">
                @if($cell->destination)
                    @if(str_starts_with($cell->destination->icon, '/'))
                        <img src="{{ public_path() . $cell->destination->icon }}" alt="" style="height:1.6rem;">
                    @else
                        <span class="cel-icon">{{ $cell->destination->icon }}</span>
                    @endif
                    <div class="cel-naam">{{ $cell->destination->getNaam() }}</div>
                @else
                    <span class="cel-leeg">{{ __('leeg') }}</span>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        // Open de print-dialog meteen bij laden van de pagina
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>

</body>
</html>
