{{-- Link naar de aparte effecten-beheren pagina --}}
<div id="effects-manager-wrapper">
    <a
        href="{{ route('effects.manager') }}"
        class="effects-manager-btn"
        aria-label="{{ __('Effecten beheren') }}"
    >
        <span class="trigger-icon" aria-hidden="true">⚙️</span>
        <span class="trigger-label">{{ __('Effecten beheren') }}</span>
        <span class="trigger-arrow" aria-hidden="true">→</span>
    </a>
</div>
