<div class="login-root">
    @include('filament.pages.auth.login-styles')

    <a href="#login-form" class="login-skip">Skip to sign in form</a>

    <div class="login-shell">
        <section class="login-visual" aria-label="Photograph of Davao del Sur">
            <img src="{{ asset('images/hero/kapatagan-mt-apo.jpg') }}"
                 alt="Mount Apo seen from Kapatagan, Digos City, Davao del Sur"
                 width="1920"
                 height="1080"
                 fetchpriority="high" />
            <div class="login-visual-overlay" aria-hidden="true"></div>
            <div class="login-visual-copy">
                <p class="login-kicker">Provincial Budget Office · Davao del Sur</p>
                <p class="login-headline">Follow every project from budget to payment</p>
                <p class="login-lede">
                    Authorized staff can track procurement, obligations, implementation, and disbursements in one official record.
                </p>
            </div>
            <p class="login-credit">
                Photo of Davao del Sur via
                <a href="https://commons.wikimedia.org/wiki/Category:Davao_del_Sur" target="_blank" rel="noopener">Wikimedia Commons</a>
                (CC BY-SA)
            </p>
        </section>

        <section class="login-panel">
            <a href="{{ url('/') }}" class="login-back">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Back to website
            </a>

            <div class="login-card" id="login-form">
                <div class="login-brand">
                    <span class="login-seal" aria-hidden="true"><span></span></span>
                    <span>
                        <span class="login-brand-name">{{ config('app.name', 'Infra Monitoring') }}</span>
                        <span class="login-brand-office">Provincial Budget Office</span>
                    </span>
                </div>

                <h1 class="login-heading">{{ $this->getHeading() }}</h1>
                <p class="login-subheading">{{ $this->getSubheading() }}</p>

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

                <x-filament-panels::form id="form" class="login-form" wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>

            <p class="login-legal">An official website of the Provincial Government of Davao del Sur</p>
        </section>
    </div>
</div>
