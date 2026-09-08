<style>
    body.login-page.fi-body {
        margin: 0;
        min-height: 100vh;
        background: #ffffff;
        color: #222222;
    }

    .dark body.login-page.fi-body {
        background: #0f0f0f;
        color: #f7f7f7;
    }

    .login-root {
        position: relative;
        min-height: 100vh;
        min-height: 100dvh;
    }

    .login-shell {
        display: grid;
        min-height: 100vh;
        min-height: 100dvh;
        grid-template-rows: minmax(14rem, 34vh) minmax(0, 1fr);
    }

    .login-visual {
        position: relative;
        overflow: hidden;
        color: #ffffff;
        min-height: 14rem;
    }

    .login-visual img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .login-visual-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            180deg,
            rgba(12, 12, 12, 0.42) 0%,
            rgba(12, 12, 12, 0.28) 40%,
            rgba(12, 12, 12, 0.78) 100%
        );
    }

    .login-visual-copy {
        position: relative;
        z-index: 1;
        display: flex;
        height: 100%;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .login-kicker {
        margin: 0;
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
    }

    .login-headline {
        margin: 0.5rem 0 0;
        max-width: 22rem;
        font-size: 1.375rem;
        font-weight: 700;
        line-height: 1.3;
        text-wrap: balance;
        color: #ffffff;
    }

    .login-lede {
        display: none;
        margin: 0.75rem 0 0;
        max-width: 28rem;
        font-size: 0.95rem;
        line-height: 1.55;
        color: rgba(255, 255, 255, 0.85);
    }

    .login-credit {
        position: relative;
        z-index: 1;
        margin: 0;
        padding: 0 1.25rem 0.85rem;
        font-size: 0.6875rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .login-credit a {
        color: inherit;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .login-panel {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .dark .login-panel {
        background: #0f0f0f;
    }

    .login-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #222222;
        text-decoration: none;
    }

    .dark .login-back {
        color: #f7f7f7;
    }

    .login-back:hover {
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .login-card {
        width: 100%;
        max-width: 24rem;
        margin: 0 auto;
    }

    .login-brand {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        margin-bottom: 1.5rem;
    }

    .login-seal {
        display: inline-flex;
        height: 2.25rem;
        width: 2.25rem;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        background: #222222;
    }

    .dark .login-seal {
        background: #f7f7f7;
    }

    .login-seal span {
        height: 0.4rem;
        width: 0.4rem;
        border-radius: 9999px;
        background: #ffffff;
    }

    .dark .login-seal span {
        background: #0f0f0f;
    }

    .login-brand-name {
        display: block;
        font-size: 0.9375rem;
        font-weight: 600;
        line-height: 1.2;
        color: #222222;
    }

    .login-brand-office {
        display: block;
        font-size: 0.75rem;
        color: #6a6a6a;
    }

    .dark .login-brand-name {
        color: #f7f7f7;
    }

    .dark .login-brand-office {
        color: #a3a3a3;
    }

    .login-heading {
        margin: 0;
        font-size: 1.375rem;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -0.02em;
        color: #222222;
    }

    .login-subheading {
        margin: 0.5rem 0 1.5rem;
        font-size: 0.9375rem;
        line-height: 1.5;
        color: #6a6a6a;
    }

    .dark .login-heading {
        color: #f7f7f7;
    }

    .dark .login-subheading {
        color: #a3a3a3;
    }

    .login-form .fi-fo-field-wrp + .fi-fo-field-wrp {
        margin-top: 0.25rem;
    }

    .login-page .login-submit,
    .login-page .fi-ac-btn-action.fi-btn-color-primary {
        min-height: 3rem;
        border-radius: 8px;
        background-color: #ff385c !important;
        --c-400: 255, 56, 92;
        --c-500: 255, 56, 92;
        --c-600: 224, 11, 65;
    }

    .login-page .login-submit:hover,
    .login-page .fi-ac-btn-action.fi-btn-color-primary:hover {
        background-color: #e00b41 !important;
    }

    .login-legal {
        margin-top: auto;
        padding-top: 1.75rem;
        font-size: 0.75rem;
        line-height: 1.45;
        color: #6a6a6a;
    }

    .dark .login-legal {
        color: #a3a3a3;
    }

    .login-skip {
        position: absolute;
        left: -9999px;
        top: 0.75rem;
        z-index: 50;
        border-radius: 8px;
        background: #222222;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        font-weight: 500;
        color: #ffffff;
    }

    .login-skip:focus {
        left: 0.75rem;
    }

    @media (min-width: 960px) {
        .login-shell {
            grid-template-columns: minmax(0, 1.15fr) minmax(26rem, 28rem);
            grid-template-rows: 1fr;
        }

        .login-visual {
            min-height: 100vh;
            min-height: 100dvh;
        }

        .login-visual-overlay {
            background: linear-gradient(
                180deg,
                rgba(12, 12, 12, 0.38) 0%,
                rgba(12, 12, 12, 0.22) 38%,
                rgba(12, 12, 12, 0.78) 100%
            );
        }

        .login-visual-copy {
            padding: 2.5rem 2.5rem 1.25rem;
        }

        .login-headline {
            max-width: 28rem;
            font-size: 2.25rem;
            line-height: 1.18;
        }

        .login-lede {
            display: block;
        }

        .login-credit {
            padding: 0 2.5rem 1.5rem;
        }

        .login-panel {
            position: relative;
            padding: 2rem 2.5rem;
        }

        .login-card {
            margin: auto 0;
        }

        .login-back {
            position: absolute;
            top: 1.5rem;
            right: 2.5rem;
            margin-bottom: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .login-page .login-submit,
        .login-page .fi-ac-btn-action {
            transition: none;
        }
    }
</style>
