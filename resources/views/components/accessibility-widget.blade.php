<div id="accessibility-widget-root">
    <!-- Trigger Flottante (Sopra l'icona Cookie a sinistra) -->
    <button id="accessibility-trigger" title="Impostazioni di accessibilità" aria-label="Apri impostazioni accessibilità" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="5" r="3"/>
            <path d="M9 22V12h6v10"/>
            <path d="M5 12V7.5a4.5 4.5 0 0 1 9 0V12h3"/>
        </svg>
    </button>

    <!-- Overlay Sfondo -->
    <div id="accessibility-overlay" aria-hidden="true"></div>

    <!-- Pannello Laterale Drawer (Stile OneTap / Maldarizzi) -->
    <div id="accessibility-panel" role="dialog" aria-modal="true" aria-label="Pannello impostazioni di accessibilità">
        <!-- Header Blu -->
        <div class="onetap-header">
            <div class="onetap-header-top">
                <div class="onetap-lang-pill">
                    <span>🇮🇹 Italiano</span>
                </div>
                <button class="onetap-close-btn" title="Chiudi pannello" aria-label="Chiudi pannello">&times;</button>
            </div>
            <div class="onetap-header-main">
                <div class="onetap-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="5" r="3"/>
                        <path d="M9 22V12h6v10"/>
                        <path d="M5 12V7.5a4.5 4.5 0 0 1 9 0V12h3"/>
                    </svg>
                </div>
                <h2>Impostazioni di accessibilità</h2>
                <div class="onetap-header-sublinks">
                    <span>Dichiarazione di accessibilità</span>
                    <span class="sep">•</span>
                    <span class="hide-trigger" id="onetap-hide-trigger">Nascondi barra degli strumenti</span>
                </div>
            </div>
        </div>

        <div class="onetap-body">
            <!-- Sezione 1: Profili di accessibilità -->
            <div class="onetap-section">
                <h3 class="onetap-section-title">Seleziona il tuo profilo di accessibilità</h3>
                <div class="onetap-profiles-list">
                    <!-- Profilo 1: Disabilità Visive -->
                    <div class="onetap-profile-item">
                        <div class="onetap-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div class="onetap-profile-text">
                            <strong>Modalità per disabilità visive</strong>
                            <span>Migliora gli elementi visivi del sito web</span>
                        </div>
                        <label class="onetap-switch">
                            <input type="checkbox" data-profile="visionImpaired" aria-label="Modalità per disabilità visive">
                            <span class="onetap-slider"></span>
                        </label>
                    </div>

                    <!-- Profilo 2: Profilo Sicuro per Crisi -->
                    <div class="onetap-profile-item">
                        <div class="onetap-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="onetap-profile-text">
                            <strong>Profilo sicuro per crisi</strong>
                            <span>Riduce i lampi e migliora i colori</span>
                        </div>
                        <label class="onetap-switch">
                            <input type="checkbox" data-profile="seizureSafe" aria-label="Profilo sicuro per crisi">
                            <span class="onetap-slider"></span>
                        </label>
                    </div>

                    <!-- Profilo 3: Modalità ADHD -->
                    <div class="onetap-profile-item">
                        <div class="onetap-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                        </div>
                        <div class="onetap-profile-text">
                            <strong>Modalità adatta per ADHD</strong>
                            <span>Navigazione concentrata senza distrazioni</span>
                        </div>
                        <label class="onetap-switch">
                            <input type="checkbox" data-profile="adhdMode" aria-label="Modalità per ADHD">
                            <span class="onetap-slider"></span>
                        </label>
                    </div>

                    <!-- Profilo 4: Modalità per Cecità / Screen Reader -->
                    <div class="onetap-profile-item">
                        <div class="onetap-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 10v3"/><path d="M6 6v11"/><path d="M10 3v18"/><path d="M14 8v7"/><path d="M18 5v13"/><path d="M22 10v3"/></svg>
                        </div>
                        <div class="onetap-profile-text">
                            <strong>Modalità per cecità</strong>
                            <span>Riduce le distrazioni e migliora la concentrazione</span>
                        </div>
                        <label class="onetap-switch">
                            <input type="checkbox" data-profile="blindnessMode" aria-label="Modalità per cecità">
                            <span class="onetap-slider"></span>
                        </label>
                    </div>

                    <!-- Profilo 5: Sicura per Epilessia -->
                    <div class="onetap-profile-item">
                        <div class="onetap-profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </div>
                        <div class="onetap-profile-text">
                            <strong>Modalità sicura per epilessia</strong>
                            <span>Riduce i colori e ferma i lampeggiamenti</span>
                        </div>
                        <label class="onetap-switch">
                            <input type="checkbox" data-profile="epilepsyMode" aria-label="Modalità sicura per epilessia">
                            <span class="onetap-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sezione 2: Moduli di contenuto -->
            <div class="onetap-section">
                <h3 class="onetap-section-title">Moduli di contenuto</h3>
                <div class="onetap-modules-grid">
                    <!-- Dimensione del carattere -->
                    <div class="onetap-module-card">
                        <span class="onetap-card-title">Dimensione del carattere</span>
                        <div class="onetap-font-controls">
                            <button data-action="fontMinus" class="onetap-circle-btn" title="Riduci carattere" aria-label="Riduci dimensione testo">-</button>
                            <span id="onetap-font-indicator">Predefinito</span>
                            <button data-action="fontPlus" class="onetap-circle-btn" title="Aumenta carattere" aria-label="Aumenta dimensione testo">+</button>
                        </div>
                    </div>

                    <!-- Evidenzia i link -->
                    <div class="onetap-module-card" data-setting="highlightLinks">
                        <button class="onetap-card-btn" data-action="toggleHighlightLinks" type="button" aria-pressed="false">
                            <span class="onetap-card-icon">🔗</span>
                            <span class="onetap-card-label">Evidenzia i link</span>
                        </button>
                    </div>

                    <!-- Alto Contrasto -->
                    <div class="onetap-module-card" data-setting="highContrast">
                        <button class="onetap-card-btn" data-action="toggleHighContrast" type="button" aria-pressed="false">
                            <span class="onetap-card-icon">🌓</span>
                            <span class="onetap-card-label">Alto Contrasto</span>
                        </button>
                    </div>

                    <!-- Font Leggibile (Dislessia) -->
                    <div class="onetap-module-card" data-setting="readableFont">
                        <button class="onetap-card-btn" data-action="toggleReadableFont" type="button" aria-pressed="false">
                            <span class="onetap-card-icon">Aa</span>
                            <span class="onetap-card-label">Font Leggibile</span>
                        </button>
                    </div>

                    <!-- Ferma Animazioni -->
                    <div class="onetap-module-card" data-setting="stopAnimations">
                        <button class="onetap-card-btn" data-action="toggleStopAnimations" type="button" aria-pressed="false">
                            <span class="onetap-card-icon">⏹️</span>
                            <span class="onetap-card-label">Ferma Animazioni</span>
                        </button>
                    </div>

                    <!-- Cursore Grande -->
                    <div class="onetap-module-card" data-setting="bigCursor">
                        <button class="onetap-card-btn" data-action="toggleBigCursor" type="button" aria-pressed="false">
                            <span class="onetap-card-icon">👆</span>
                            <span class="onetap-card-label">Cursore Grande</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="onetap-footer">
            <button class="onetap-reset-btn" data-action="resetAll" type="button">
                Ripristina Impostazioni
            </button>
        </div>
    </div>

    <!-- Riga Guida di Lettura per ADHD (Reading Ruler OneTap) -->
    <div id="onetap-reading-line" aria-hidden="true"></div>
</div>

<style>
    /* Scope del widget */
    #accessibility-widget-root {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
        font-size: 16px !important;
        line-height: 1.4 !important;
        color: #1e293b !important;
    }
    #accessibility-widget-root * {
        box-sizing: border-box !important;
    }

    /* Floating Trigger Button */
    #accessibility-trigger {
        position: fixed !important;
        bottom: 86px !important;
        left: 24px !important;
        width: 52px !important;
        height: 52px !important;
        background: #0052ff !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 6px 20px rgba(0, 82, 255, 0.45) !important;
        z-index: 2147483640 !important;
        transition: transform 0.2s ease, background 0.2s ease !important;
        outline: none !important;
    }
    #accessibility-trigger:hover {
        transform: scale(1.1) !important;
        background: #0040cc !important;
    }

    /* Overlay Backdrop */
    #accessibility-overlay {
        position: fixed !important;
        top: 0 !important; left: 0 !important;
        width: 100vw !important; height: 100vh !important;
        background: rgba(0, 0, 0, 0.35) !important;
        backdrop-filter: blur(2px) !important;
        z-index: 2147483641 !important;
        display: none;
    }
    #accessibility-overlay.active {
        display: block !important;
    }

    /* Drawer Laterale */
    #accessibility-panel {
        position: fixed !important;
        top: 0 !important;
        left: -440px !important;
        width: 410px !important;
        max-width: 92vw !important;
        height: 100vh !important;
        background: #f8fafc !important;
        z-index: 2147483642 !important;
        transition: left 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: 8px 0 35px rgba(0, 0, 0, 0.18) !important;
    }
    #accessibility-panel.open {
        left: 0 !important;
    }

    /* Header */
    .onetap-header {
        background: #0052ff !important;
        color: #ffffff !important;
        padding: 20px 24px 24px 24px !important;
        position: relative !important;
    }
    .onetap-header-top {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 12px !important;
    }
    .onetap-lang-pill {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        padding: 4px 12px !important;
        border-radius: 20px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .onetap-close-btn {
        background: rgba(255, 255, 255, 0.18) !important;
        border: none !important;
        color: #ffffff !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        font-size: 1.5rem !important;
        line-height: 1 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: background 0.15s ease !important;
    }
    .onetap-close-btn:hover {
        background: rgba(255, 255, 255, 0.35) !important;
    }

    .onetap-header-main {
        text-align: center !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .onetap-header-icon {
        width: 58px !important;
        height: 58px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #ffffff !important;
        margin-bottom: 10px !important;
        box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.15) !important;
    }
    .onetap-header-main h2 {
        font-size: 1.38rem !important;
        font-weight: 700 !important;
        margin: 0 0 6px 0 !important;
        color: #ffffff !important;
        letter-spacing: -0.3px !important;
    }
    .onetap-header-sublinks {
        font-size: 0.75rem !important;
        color: rgba(255, 255, 255, 0.85) !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .onetap-header-sublinks .sep {
        opacity: 0.5 !important;
    }
    .onetap-header-sublinks .hide-trigger {
        text-decoration: underline !important;
        cursor: pointer !important;
    }

    /* Content Body */
    .onetap-body {
        flex: 1 !important;
        overflow-y: auto !important;
        padding: 20px !important;
    }
    .onetap-section {
        margin-bottom: 22px !important;
    }
    .onetap-section-title {
        font-size: 0.82rem !important;
        font-weight: 700 !important;
        color: #334155 !important;
        margin: 0 0 10px 0 !important;
        text-transform: none !important;
        letter-spacing: 0.2px !important;
    }

    /* Profiles List */
    .onetap-profiles-list {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
    }
    .onetap-profile-item {
        padding: 13px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }
    .onetap-profile-item:last-child {
        border-bottom: none !important;
    }
    .onetap-profile-icon {
        width: 34px !important;
        height: 34px !important;
        border-radius: 8px !important;
        background: #f1f5f9 !important;
        color: #3b82f6 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .onetap-profile-text {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
    }
    .onetap-profile-text strong {
        font-size: 0.86rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    .onetap-profile-text span {
        font-size: 0.72rem !important;
        color: #64748b !important;
        line-height: 1.3 !important;
    }

    /* Toggle Switches (iOS style) */
    .onetap-switch {
        position: relative !important;
        display: inline-block !important;
        width: 44px !important;
        height: 24px !important;
        flex-shrink: 0 !important;
    }
    .onetap-switch input {
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
    }
    .onetap-slider {
        position: absolute !important;
        cursor: pointer !important;
        top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
        background-color: #cbd5e1 !important;
        transition: 0.28s ease !important;
        border-radius: 24px !important;
    }
    .onetap-slider:before {
        position: absolute !important;
        content: "" !important;
        height: 18px !important;
        width: 18px !important;
        left: 3px !important;
        bottom: 3px !important;
        background-color: #ffffff !important;
        transition: 0.28s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        border-radius: 50% !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }
    .onetap-switch input:checked + .onetap-slider {
        background-color: #0052ff !important;
    }
    .onetap-switch input:checked + .onetap-slider:before {
        transform: translateX(20px) !important;
    }

    /* Moduli Cards Grid */
    .onetap-modules-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 10px !important;
    }
    .onetap-module-card {
        background: #ffffff !important;
        border-radius: 14px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 10px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-align: center !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.2s ease !important;
    }
    .onetap-module-card.active {
        border-color: #0052ff !important;
        background: #eff6ff !important;
        box-shadow: 0 0 0 2px rgba(0, 82, 255, 0.22) !important;
    }
    .onetap-card-title {
        font-size: 0.73rem !important;
        color: #475569 !important;
        font-weight: 600 !important;
        text-transform: none !important;
    }
    .onetap-font-controls {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .onetap-circle-btn {
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        border: none !important;
        background: #0052ff !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1.15rem !important;
        line-height: 1 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: transform 0.15s ease, background 0.15s ease !important;
    }
    .onetap-circle-btn:hover {
        background: #0040cc !important;
        transform: scale(1.08) !important;
    }
    #onetap-font-indicator {
        font-size: 0.82rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        min-width: 60px !important;
        text-align: center !important;
    }

    .onetap-card-btn {
        background: none !important;
        border: none !important;
        width: 100% !important;
        cursor: pointer !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 6px !important;
        outline: none !important;
        padding: 0 !important;
    }
    .onetap-card-icon {
        font-size: 1.35rem !important;
        line-height: 1 !important;
    }
    .onetap-card-label {
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
    }

    /* Footer */
    .onetap-footer {
        padding: 16px 20px !important;
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0 !important;
    }
    .onetap-reset-btn {
        width: 100% !important;
        padding: 12px !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 0.88rem !important;
        color: #334155 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .onetap-reset-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }

    /* ==========================================================================
       EFFETTI SULLA PAGINA GLOBALE
       ========================================================================== */

    /* 1. ADHD Reading Line */
    #onetap-reading-line {
        display: none;
        position: fixed;
        left: 0;
        width: 100vw;
        height: 14px;
        background: #000000;
        border-top: 3px solid #fedb00;
        border-bottom: 3px solid #fedb00;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.45);
        pointer-events: none;
        z-index: 2147483630;
        transform: translateY(-50%);
        transition: top 0.03s linear;
    }
    html.acc-adhd-active #onetap-reading-line {
        display: block !important;
    }

    /* 2. Stop Animazioni */
    html.acc-stop-anims *,
    html.acc-stop-anims *::before,
    html.acc-stop-anims *::after {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
    }

    /* 3. Disabilità Visive */
    html.acc-vision-impaired body {
        filter: saturate(1.7) contrast(1.22) !important;
    }

    /* 4. Profilo Sicuro per Crisi / Epilessia */
    html.acc-seizure-safe body {
        filter: saturate(0.65) brightness(0.95) !important;
    }

    /* 5. Cecità / Screen Reader Focus Outline */
    html.acc-blindness a:focus,
    html.acc-blindness button:focus,
    html.acc-blindness input:focus,
    html.acc-blindness select:focus,
    html.acc-blindness [tabindex]:focus {
        outline: 4px solid #0052ff !important;
        outline-offset: 3px !important;
        box-shadow: 0 0 14px rgba(0, 82, 255, 0.9) !important;
    }

    /* 6. Evidenzia Link */
    html.acc-links-highlight a:not(#accessibility-widget-root *):not(#cookie-consent-master-wrapper *),
    html.acc-links-highlight button:not(#accessibility-widget-root *):not(#cookie-consent-master-wrapper *),
    html.acc-links-highlight [role="button"]:not(#accessibility-widget-root *),
    html.acc-links-highlight [role="link"]:not(#accessibility-widget-root *) {
        background-color: #fedb00 !important;
        color: #000000 !important;
        text-decoration: underline !important;
        text-decoration-thickness: 2px !important;
        text-underline-offset: 3px !important;
        font-weight: 700 !important;
        box-shadow: 0 0 0 2px #000000 !important;
        border-radius: 2px !important;
    }
    html.acc-links-highlight a:not(#accessibility-widget-root *):not(#cookie-consent-master-wrapper *) * {
        color: #000000 !important;
    }

    /* 7. Alto Contrasto Scuro */
    html.acc-contrast-dark body {
        background-color: #121212 !important;
        color: #ffffff !important;
    }
    html.acc-contrast-dark *:not(#accessibility-widget-root *):not(#cookie-consent-master-wrapper *):not(img):not(svg):not(video):not(canvas):not(i) {
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        border-color: #fedb00 !important;
    }
    html.acc-contrast-dark a:not(#accessibility-widget-root *):not(#cookie-consent-master-wrapper *) {
        color: #fedb00 !important;
    }
    html.acc-contrast-dark img,
    html.acc-contrast-dark video {
        filter: contrast(120%) brightness(105%) !important;
    }

    /* 8. Font Leggibile */
    html.acc-font-readable,
    html.acc-font-readable *:not(#accessibility-widget-root *):not(i):not(svg) {
        font-family: Arial, Helvetica, Verdana, sans-serif !important;
        letter-spacing: 0.035em !important;
        word-spacing: 0.08em !important;
        line-height: 1.7 !important;
    }

    /* 9. Cursore Grande */
    html.acc-cursor-big,
    html.acc-cursor-big * {
        cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 24 24' fill='%23000000' stroke='%23ffffff' stroke-width='1.5'%3E%3Cpath d='M3 3l7 17 2.5-6.5L19 11 3 3z'/%3E%3C/svg%3E"), auto !important;
    }
</style>

<script>
(function() {
    function initOneTapAccessibility() {
        const root = document.getElementById('accessibility-widget-root');
        if (!root) return;

        const STORAGE_KEY = 'bicap_onetap_acc_state_v5';
        const docEl = document.documentElement;
        const ruler = document.getElementById('onetap-reading-line');
        const originalFontSizes = new Map();

        let state = {
            fontSizeLevel: 0,
            visionImpaired: false,
            seizureSafe: false,
            adhdMode: false,
            blindnessMode: false,
            epilepsyMode: false,
            highlightLinks: false,
            highContrast: false,
            readableFont: false,
            stopAnimations: false,
            bigCursor: false,
            hiddenTrigger: false
        };

        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) state = Object.assign(state, JSON.parse(saved));
        } catch (e) {}

        const fontScaleMap = {
            '-2': { pct: 75, label: '75%' },
            '-1': { pct: 85, label: '85%' },
            '0':  { pct: 100, label: 'Predefinito' },
            '1':  { pct: 115, label: '115%' },
            '2':  { pct: 130, label: '130%' },
            '3':  { pct: 150, label: '150%' },
            '4':  { pct: 175, label: '175%' },
            '5':  { pct: 200, label: '200%' }
        };

        function updateDOMFontSizes(factor) {
            if (factor === 1) {
                docEl.style.fontSize = '';
            } else {
                docEl.style.fontSize = (factor * 100) + '%';
            }

            const textSelector = 'p, h1, h2, h3, h4, h5, h6, a, span, button, li, td, th, label, input, select';
            const elements = document.querySelectorAll(textSelector);

            elements.forEach(el => {
                if (el.closest('#accessibility-widget-root') || el.closest('#cookie-consent-master-wrapper')) {
                    return;
                }

                if (!originalFontSizes.has(el)) {
                    const comp = window.getComputedStyle(el).fontSize;
                    const parsed = parseFloat(comp);
                    if (parsed && parsed > 0) {
                        originalFontSizes.set(el, parsed);
                    }
                }

                const baseSize = originalFontSizes.get(el);
                if (baseSize) {
                    if (factor === 1) {
                        el.style.fontSize = '';
                    } else {
                        el.style.fontSize = Math.round(baseSize * factor) + 'px';
                    }
                }
            });
        }

        const applyState = () => {
            const scaleInfo = fontScaleMap[String(state.fontSizeLevel)] || fontScaleMap['0'];
            const fontFactor = scaleInfo.pct / 100;
            updateDOMFontSizes(fontFactor);

            const fontIndicator = root.querySelector('#onetap-font-indicator');
            if (fontIndicator) {
                fontIndicator.innerText = scaleInfo.label;
            }

            docEl.classList.toggle('acc-vision-impaired', !!state.visionImpaired);
            docEl.classList.toggle('acc-seizure-safe', !!state.seizureSafe || !!state.epilepsyMode);
            docEl.classList.toggle('acc-adhd-active', !!state.adhdMode);
            docEl.classList.toggle('acc-blindness', !!state.blindnessMode);
            docEl.classList.toggle('acc-stop-anims', !!state.stopAnimations || !!state.seizureSafe || !!state.epilepsyMode || !!state.adhdMode);
            docEl.classList.toggle('acc-links-highlight', !!state.highlightLinks);
            docEl.classList.toggle('acc-contrast-dark', !!state.highContrast);
            docEl.classList.toggle('acc-font-readable', !!state.readableFont);
            docEl.classList.toggle('acc-cursor-big', !!state.bigCursor);

            root.querySelectorAll('[data-profile]').forEach(sw => {
                const key = sw.dataset.profile;
                sw.checked = !!state[key];
            });

            root.querySelectorAll('.onetap-module-card[data-setting]').forEach(card => {
                const key = card.dataset.setting;
                const active = !!state[key];
                card.classList.toggle('active', active);
                const btn = card.querySelector('button');
                if (btn) btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            const triggerBtn = root.querySelector('#accessibility-trigger');
            if (triggerBtn) {
                triggerBtn.style.display = state.hiddenTrigger ? 'none' : 'flex';
            }

            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            } catch (e) {}
        };

        const trigger = root.querySelector('#accessibility-trigger');
        const panel = root.querySelector('#accessibility-panel');
        const overlay = root.querySelector('#accessibility-overlay');
        const closeBtn = root.querySelector('.onetap-close-btn');

        const openDrawer = () => {
            panel.classList.add('open');
            overlay.classList.add('active');
        };

        const closeDrawer = () => {
            panel.classList.remove('open');
            overlay.classList.remove('active');
        };

        if (trigger) trigger.onclick = openDrawer;
        if (closeBtn) closeBtn.onclick = closeDrawer;
        if (overlay) overlay.onclick = closeDrawer;

        const hideTriggerBtn = root.querySelector('#onetap-hide-trigger');
        if (hideTriggerBtn) {
            hideTriggerBtn.onclick = () => {
                state.hiddenTrigger = true;
                applyState();
                closeDrawer();
                alert('Pannello accessibilità nascosto. Puoi riattivarlo ricaricando la pagina o cancellando i dati di navigazione.');
            };
        }

        root.querySelectorAll('[data-action]').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const action = btn.dataset.action;

                if (action === 'fontPlus') {
                    if (state.fontSizeLevel < 5) state.fontSizeLevel++;
                } else if (action === 'fontMinus') {
                    if (state.fontSizeLevel > -2) state.fontSizeLevel--;
                } else if (action === 'toggleHighlightLinks') {
                    state.highlightLinks = !state.highlightLinks;
                } else if (action === 'toggleHighContrast') {
                    state.highContrast = !state.highContrast;
                } else if (action === 'toggleReadableFont') {
                    state.readableFont = !state.readableFont;
                } else if (action === 'toggleStopAnimations') {
                    state.stopAnimations = !state.stopAnimations;
                } else if (action === 'toggleBigCursor') {
                    state.bigCursor = !state.bigCursor;
                } else if (action === 'resetAll') {
                    state = {
                        fontSizeLevel: 0,
                        visionImpaired: false,
                        seizureSafe: false,
                        adhdMode: false,
                        blindnessMode: false,
                        epilepsyMode: false,
                        highlightLinks: false,
                        highContrast: false,
                        readableFont: false,
                        stopAnimations: false,
                        bigCursor: false,
                        hiddenTrigger: false
                    };
                }

                applyState();
            };
        });

        root.querySelectorAll('[data-profile]').forEach(sw => {
            sw.onchange = () => {
                const key = sw.dataset.profile;
                state[key] = sw.checked;

                if (key === 'visionImpaired' && sw.checked) {
                    state.fontSizeLevel = Math.max(state.fontSizeLevel, 2);
                    state.readableFont = true;
                }
                if (key === 'adhdMode' && sw.checked) {
                    state.stopAnimations = true;
                    state.highlightLinks = true;
                }
                if (key === 'seizureSafe' && sw.checked) {
                    state.stopAnimations = true;
                }
                if (key === 'epilepsyMode' && sw.checked) {
                    state.stopAnimations = true;
                }
                if (key === 'blindnessMode' && sw.checked) {
                    state.highlightLinks = true;
                }

                applyState();
            };
        });

        window.addEventListener('mousemove', (e) => {
            if (state.adhdMode && ruler) {
                ruler.style.top = e.clientY + 'px';
            }
        }, { passive: true });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && panel.classList.contains('open')) {
                closeDrawer();
            }
        });

        applyState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOneTapAccessibility);
    } else {
        initOneTapAccessibility();
    }
})();
</script>
