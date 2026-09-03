<footer class="border-t border-hairline bg-canvas pb-24 pt-12 md:pb-12 lg:pt-16">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>
            <h3 class="text-base font-medium text-ink">{{ config('app.name', 'Infra Monitoring') }}</h3>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Official project and budget monitoring for the Provincial Budget Office of Davao del Sur.
            </p>
        </div>
        <div>
            <h3 class="text-base font-medium text-ink">On this page</h3>
            <ul class="mt-3 space-y-3">
                <li><a href="#how-it-works" class="footer-link">How it works</a></li>
                <li><a href="#features" class="footer-link">What you can do</a></li>
                <li><a href="#audience" class="footer-link">Who it's for</a></li>
                <li><a href="#faq" class="footer-link">FAQ</a></li>
                <li><a href="#team" class="footer-link">The team</a></li>
                <li><a href="#contact" class="footer-link">Contact</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-base font-medium text-ink">For staff</h3>
            <ul class="mt-3 space-y-3">
                <li>
                    <a href="{{ route('filament.admin.auth.login') }}" class="footer-link">
                        Sign in to the dashboard
                    </a>
                </li>
                <li><a href="#" class="footer-link">Privacy Policy</a></li>
                <li><a href="#" class="footer-link">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="mx-auto mt-10 max-w-7xl border-t border-hairline px-4 pt-6 sm:px-6 lg:px-8">
        <p class="text-[13px] text-muted">
            © {{ now()->year }} Provincial Budget Office, Province of Davao del Sur. All rights reserved.
        </p>
    </div>
</footer>

<div class="sticky-cta">
    <a href="{{ route('filament.admin.auth.login') }}" class="btn-rausch w-full">
        Sign in to the dashboard
    </a>
</div>
