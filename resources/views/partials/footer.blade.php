<footer class="border-t border-hairline bg-canvas py-12 lg:py-16">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>
            <h3 class="text-base font-medium text-ink">PBO Monitoring</h3>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Transparent project lifecycle and budget oversight platform.
            </p>
        </div>
        <div>
            <h3 class="text-base font-medium text-ink">Resources</h3>
            <ul class="mt-3 space-y-3 text-sm text-body">
                <li><a href="#features" class="hover:text-ink">Features</a></li>
                <li><a href="#dev-team" class="hover:text-ink">Dev Team</a></li>
                <li><a href="#contact" class="hover:text-ink">Contact</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-base font-medium text-ink">Legal</h3>
            <ul class="mt-3 space-y-3 text-sm text-body">
                <li><a href="#" class="hover:text-ink">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-ink">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="mx-auto mt-10 max-w-7xl border-t border-hairline px-4 pt-6 sm:px-6 lg:px-8">
        <p class="text-center text-[13px] text-muted">
            © {{ now()->year }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </p>
    </div>
</footer>
