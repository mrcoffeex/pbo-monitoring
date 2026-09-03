<section id="faq" class="border-t border-hairline bg-surface-soft py-16" aria-labelledby="faq-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 id="faq-heading" class="landing-section-title">Frequently asked questions</h2>
            <p class="landing-section-sub">Quick answers for staff who are new to the system.</p>
        </div>

        <dl class="mt-10 grid gap-4 md:grid-cols-2">
            @php
                $faqs = [
                    [
                        'q' => 'Who can sign in?',
                        'a' => 'Only authorized Provincial Budget Office staff and offices given an account. Access follows your assigned role.',
                    ],
                    [
                        'q' => 'Is project data public?',
                        'a' => 'No. This site is an official office system. Visitors can read this page; project records stay behind sign-in.',
                    ],
                    [
                        'q' => 'How do I get an account?',
                        'a' => 'Ask your office head or contact the Provincial Budget Office during working hours. We do not offer self-registration.',
                    ],
                    [
                        'q' => 'What can I see after I sign in?',
                        'a' => 'Projects you are allowed to open, plus budget, implementation, and payment records tied to those projects.',
                    ],
                ];
            @endphp
            @foreach($faqs as $faq)
                <div class="landing-card p-5">
                    <dt class="text-base font-semibold text-ink">{{ $faq['q'] }}</dt>
                    <dd class="mt-2 text-sm leading-relaxed text-body">{{ $faq['a'] }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
