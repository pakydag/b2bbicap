
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $favicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
    @endphp

    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @endif

    <title>{{ $seo['title'] ?? config('app.name', 'CRM Laravel') }}</title>

    <!-- Meta SEO Standard -->
    <meta name="description" content="{{ $seo['description'] ?? 'Benvenuti sul nostro sito ufficiale' }}">

    <!-- Open Graph (Facebook, LinkedIn, Pinterest, WhatsApp ecc.) -->
    <meta property="og:title" content="{{ $seo['title'] ?? config('app.name', 'CRM Laravel') }}" />
    <meta property="og:description" content="{{ $seo['description'] ?? 'Benvenuti sul nostro sito ufficiale' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}" />
    @if(!empty($seo['image']))
        <meta property="og:image" content="{{ asset($seo['image']) }}" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? config('app.name', 'CRM Laravel') }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'Benvenuti sul nostro sito ufficiale' }}">
    @if(!empty($seo['image']))
        <meta name="twitter:image" content="{{ asset($seo['image']) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Scoped Bicap Public Site Typography - Exactly matching bicap.shop */
        body.bicap-public-site,
        body.bicap-public-site input,
        body.bicap-public-site select,
        body.bicap-public-site textarea {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        body.bicap-public-site {
            font-size: 13px;
            color: #222222;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-bebas,
        .bicap-bebas-title {
            font-family: 'Bebas Neue', serif !important;
        }
        body.bicap-public-site .bicap-nav-link,
        .bicap-nav-link {
            font-family: 'Bebas Neue', serif !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            color: #ffffff !important;
            padding: 0 10px !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            transition: color 0.2s ease, opacity 0.2s ease !important;
        }
        body.bicap-public-site .bicap-nav-link:hover,
        body.bicap-public-site a.bicap-nav-link:hover,
        .bicap-nav-link:hover {
            color: #fedb00 !important;
        }
        body.bicap-public-site .bicap-nav-link.bicap-nav-link-yellow,
        body.bicap-public-site a.bicap-nav-link.bicap-nav-link-yellow,
        body.bicap-public-site .bicap-nav-link-yellow,
        .bicap-nav-link-yellow,
        body.bicap-public-site .bicap-nav-link.bicap-nav-link-yellow span,
        .bicap-nav-link-yellow span {
            color: #fedb00 !important;
        }
        body.bicap-public-site .bicap-nav-link.bicap-nav-link-yellow:hover,
        body.bicap-public-site a.bicap-nav-link.bicap-nav-link-yellow:hover,
        body.bicap-public-site .bicap-nav-link-yellow:hover,
        .bicap-nav-link-yellow:hover,
        body.bicap-public-site .bicap-nav-link.bicap-nav-link-yellow:hover span,
        .bicap-nav-link-yellow:hover span {
            color: #fedb00 !important;
            opacity: 0.85 !important;
        }

        body.bicap-public-site .bicap-filter-btn,
        body.bicap-public-site button.bicap-filter-btn,
        body.bicap-public-site .bicap-filter-header,
        .bicap-filter-header,
        .bicap-filter-btn {
            font-family: 'Bebas Neue', serif !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            letter-spacing: normal !important;
            text-transform: uppercase !important;
            color: #000000 !important;
            padding: 10px 20px !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border: none !important;
            background: none !important;
            cursor: pointer !important;
            text-align: left !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }
        body.bicap-public-site .bicap-filter-btn > span:first-child,
        .bicap-filter-btn > span:first-child {
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block !important;
            width: auto !important;
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            letter-spacing: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            text-align: left !important;
        }
        .bicap-filter-header:hover,
        .bicap-filter-btn:hover {
            background-color: #fafafa !important;
        }
        body.bicap-public-site .bicap-filter-icon,
        .bicap-filter-icon {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #000000 !important;
            line-height: 1 !important;
            margin-left: auto !important;
            padding: 0 !important;
            width: auto !important;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }

        body.bicap-public-site .bicap-filter-content,
        .bicap-filter-content {
            padding: 6px 0 14px 0 !important;
            box-sizing: border-box !important;
        }

        body.bicap-public-site .bicap-filter-label,
        .bicap-filter-item,
        .bicap-filter-label {
            font-family: 'Bebas Neue', serif !important;
            font-size: 15px !important;
            font-weight: 400 !important;
            letter-spacing: 0.5px !important;
            color: #333333 !important;
            padding: 3px 20px !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            margin: 0 !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            cursor: pointer !important;
            text-transform: uppercase !important;
            line-height: 20px !important;
            box-sizing: border-box !important;
            transition: color 0.15s ease !important;
        }
        .bicap-filter-item:hover,
        .bicap-filter-label:hover {
            color: #000000 !important;
        }
        body.bicap-public-site .bicap-filter-label input[type="checkbox"],
        .bicap-filter-label input[type="checkbox"] {
            margin: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 13px !important;
            height: 13px !important;
            min-width: 13px !important;
            min-height: 13px !important;
            border: 1px solid #777777 !important;
            border-radius: 2px !important;
            accent-color: #000000 !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        body.bicap-public-site .bicap-filter-label span,
        .bicap-filter-label span {
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block !important;
            line-height: 1 !important;
        }
        .bicap-product-title {
            font-family: 'Bebas Neue', sans-serif !important;
            font-size: 26px !important;
            font-weight: 700 !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            line-height: 1.15 !important;
            color: #000000 !important;
        }
        .bicap-product-subtitle {
            font-size: 13px !important;
            font-weight: 400 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            line-height: 1.4 !important;
            color: #555555 !important;
        }
        .bicap-product-img {
            max-height: 100% !important;
            max-width: 100% !important;
            object-fit: contain !important;
            transform: scale(1.2) !important;
            transform-origin: center center !important;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .group:hover .bicap-product-img {
            transform: scale(1.26) !important;
        }
        .bicap-main-photo {
            max-height: 480px !important;
            max-width: 100% !important;
            object-fit: contain !important;
            transform-origin: center center !important;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .bicap-main-photo.is-first-photo {
            transform: scale(1.38) !important;
        }
        .group:hover .bicap-main-photo.is-first-photo {
            transform: scale(1.42) !important;
        }
        .group:hover .bicap-main-photo:not(.is-first-photo) {
            transform: scale(1.04) !important;
        }
        /* Mobile offcanvas drawer (< 1024px) */
        @media (max-width: 1023px) {
            .bicap-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 260px !important;
                max-width: 85vw !important;
                background-color: #ffffff !important;
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.18) !important;
                z-index: 99999 !important;
                overflow-y: auto !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            }
            .bicap-sidebar.is-closed {
                transform: translateX(-105%) !important;
                visibility: hidden;
                pointer-events: none;
            }
            .bicap-sidebar.is-open {
                transform: translateX(0) !important;
                visibility: visible;
                pointer-events: auto;
            }
        }
        /* Desktop in-page left sidebar (>= 1024px) */
        @media (min-width: 1024px) {
            .bicap-sidebar {
                position: relative !important;
                width: 300px !important;
                min-width: 300px !important;
                flex-shrink: 0 !important;
                background-color: #ffffff !important;
                box-shadow: none !important;
                border-right: 1px solid #dedede !important;
                z-index: 10 !important;
            }
        }

        /* Top Yellow Announcement Bar - Identical to bicap.shop */
        .bicap-top-banner {
            background-color: #fedb00 !important;
            color: #000000 !important;
            width: 100% !important;
            text-align: center !important;
            padding: 7px 16px !important;
            font-family: 'Montserrat', sans-serif !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
            user-select: none !important;
            position: relative !important;
            z-index: 40 !important;
            box-sizing: border-box !important;
        }

        /* Sticky Black Header - Full Width with edge-to-edge alignment */
        .bicap-header-sticky {
            position: sticky !important;
            top: 0 !important;
            z-index: 9990 !important;
            background-color: #000000 !important;
            width: 100% !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5) !important;
            border-bottom: 1px solid #111111 !important;
        }

        .bicap-header-container {
            width: 100% !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            height: 80px !important;
            box-sizing: border-box !important;
        }
        @media (min-width: 768px) {
            .bicap-header-container {
                padding-left: 36px !important;
                padding-right: 36px !important;
            }
        }
        @media (min-width: 1280px) {
            .bicap-header-container {
                padding-left: 48px !important;
                padding-right: 48px !important;
            }
        }

        /* Yellow Icons & BICAP.IT accents */
        .bicap-yellow-text {
            color: #fedb00 !important;
        }
        .bicap-yellow-icon {
            color: #fedb00 !important;
            fill: #fedb00 !important;
            stroke: #fedb00 !important;
        }

        /* Bicap Footer Styles (1-to-1 bicap.shop) */
        .bicap-footer-section {
            background-color: #151515 !important;
            color: #ffffff !important;
            font-family: 'Montserrat', sans-serif !important;
            border-top: 1px solid #000000 !important;
            padding: 60px 0 40px 0 !important;
            font-size: 13px !important;
            line-height: 28px !important;
        }
        .bicap-footer-section h4 {
            color: #ffffff !important;
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            line-height: 1.2 !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            margin-bottom: 25px !important;
        }
        .bicap-footer-section a {
            color: #ffffff !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            text-decoration: none !important;
            transition: color 0.2s ease !important;
            display: inline-block !important;
            line-height: 24px !important;
        }
        .bicap-footer-section a:hover {
            color: #fedb00 !important;
        }
        .bicap-footer-icon-yellow {
            color: #fedb00 !important;
            fill: #fedb00 !important;
            display: inline-flex !important;
            align-items: center !important;
            margin-right: 8px !important;
            flex-shrink: 0 !important;
        }
        .bicap-footer-bottom {
            background-color: #0d0d0d !important;
            border-top: 1px solid #222222 !important;
            color: #888888 !important;
            font-size: 11px !important;
            padding: 20px 0 !important;
        }
        .bicap-scroll-top-btn {
            position: fixed !important;
            right: 20px !important;
            bottom: 20px !important;
            width: 44px !important;
            height: 44px !important;
            background-color: #fedb00 !important;
            color: #000000 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            z-index: 999 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
            transition: transform 0.2s, background-color 0.2s !important;
        }
        .bicap-scroll-top-btn:hover {
            transform: translateY(-3px) !important;
            background-color: #ffe020 !important;
        }
        .bicap-whatsapp-btn {
            position: fixed !important;
            right: 20px !important;
            bottom: 74px !important;
            width: 44px !important;
            height: 44px !important;
            background-color: #25d366 !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            z-index: 999 !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.35) !important;
            transition: transform 0.2s, background-color 0.2s !important;
        }
        /* Product Detail Page Specifics (Identical to bicap.shop) */
        .font-bebas {
            font-family: 'Bebas Neue', serif !important;
        }
        .btn-prodotti {
            background-color: #fedb00 !important;
            color: #000000 !important;
            font-family: 'Bebas Neue', serif !important;
            font-size: 16px !important;
            letter-spacing: 0.5px !important;
            padding: 5px 14px !important;
            display: inline-block !important;
            text-transform: uppercase !important;
            font-weight: 400 !important;
            line-height: 1.2 !important;
            transition: opacity 0.2s !important;
            text-decoration: none !important;
        }
        .btn-prodotti:hover {
            opacity: 0.85 !important;
            color: #000000 !important;
        }
        .btn-bicap-orange {
            background-color: #e35205 !important;
            color: #ffffff !important;
            font-family: 'Bebas Neue', serif !important;
            font-size: 24px !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            padding: 12px 24px !important;
            text-align: center !important;
            display: block !important;
            width: 100% !important;
            line-height: 1.2 !important;
            border: none !important;
            cursor: pointer !important;
            transition: background-color 0.2s !important;
            text-decoration: none !important;
        }
        .btn-bicap-orange:hover {
            background-color: #c94500 !important;
            color: #ffffff !important;
        }
        .btn-bicap-download {
            border: 1px solid #000000 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: 'Bebas Neue', serif !important;
            font-size: 16px !important;
            letter-spacing: 0.5px !important;
            padding: 8px 20px !important;
            text-transform: uppercase !important;
            display: inline-block !important;
            line-height: 1.2 !important;
            transition: all 0.2s !important;
            text-decoration: none !important;
        }
        .btn-bicap-download:hover {
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        .bicap-accordion-btn {
            font-family: 'Bebas Neue', serif !important;
            font-size: 18px !important;
            letter-spacing: 0.5px !important;
            color: #000000 !important;
            padding: 12px 0 !important;
            border-bottom: 1px solid #e0e0e0 !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
            background: none !important;
            cursor: pointer !important;
            text-transform: uppercase !important;
        }
        .bicap-accordion-btn:hover {
            color: #e35205 !important;
        }
        .bicap-accordion-content {
            padding: 14px 0 !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
            color: #333333 !important;
            border-bottom: 1px solid #e0e0e0 !important;
        }
        .bicap-accordion-content img {
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
            margin: 8px 0 !important;
        }
        .bicap-accordion-content p {
            margin-bottom: 0.6rem !important;
        }
        .bicap-accordion-content p:last-child {
            margin-bottom: 0 !important;
        }
        .bicap-accordion-content ul, .bicap-accordion-content ol {
            padding-left: 20px !important;
            margin-bottom: 0.6rem !important;
        }
        .bicap-specs-list {
            font-size: 13px !important;
            line-height: 22px !important;
            color: #222222 !important;
        }
        .bicap-specs-list strong {
            font-weight: 700 !important;
            color: #000000 !important;
            display: inline-block !important;
            min-width: 65px !important;
        }
        /* Bicap.shop .simboli section (1-to-1 fidelity) */
        .simboli-section {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 15px !important;
            padding-top: 10px !important;
            padding-bottom: 30px !important;
        }
        .simboli-hr {
            width: 100% !important;
            border: 0 !important;
            border-top: 1px solid #afafaf !important;
            margin: 12px 0 !important;
            display: block !important;
        }
        .simboli-row {
            display: flex !important;
            align-items: center !important;
            padding: 8px 0 !important;
            box-sizing: border-box !important;
        }
        .simboli-row-start {
            display: flex !important;
            align-items: flex-start !important;
            padding: 12px 0 !important;
            box-sizing: border-box !important;
        }
        .simboli-label {
            width: 200px !important;
            min-width: 180px !important;
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
        }
        .simboli-label strong {
            font-family: "Bebas Neue", serif !important;
            font-weight: 700 !important;
            font-size: 25px !important;
            color: #000000 !important;
            text-transform: uppercase !important;
            letter-spacing: normal !important;
            line-height: 1 !important;
        }
        .simboli-content {
            flex: 1 !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }
        .simboli-content img {
            max-height: 38px !important;
            width: auto !important;
            object-fit: contain !important;
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .simboli-content.simboli-content-componenti img {
            max-height: 48px !important;
        }
        .simboli-content.simboli-content-sicurezza img {
            max-height: 36px !important;
        }
        .simboli-settori-text {
            font-family: inherit !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            color: #000000 !important;
            line-height: 1.5 !important;
        }

        /* Descrizione clamp & toggle identical to bicap.shop */
        .simboli-descrizione {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            transition: all 0.3s ease !important;
            max-height: 3.2em !important;
            line-height: 1.6 !important;
            font-size: 14px !important;
            color: #222222 !important;
        }
        .simboli-descrizione.expanded {
            display: block !important;
            -webkit-line-clamp: unset !important;
            max-height: none !important;
        }
        .simboli-descrizione h3 {
            font-size: 17px !important;
            font-weight: bold !important;
            margin-top: 14px !important;
            margin-bottom: 6px !important;
            color: #000000 !important;
        }
        .simboli-descrizione h3:first-child {
            margin-top: 0 !important;
        }
        .simboli-descrizione p {
            margin-bottom: 8px !important;
            line-height: 1.6 !important;
        }
        .simboli-toggle-descrizione {
            float: right !important;
            font-weight: 700 !important;
            margin-top: 6px !important;
            background: none !important;
            border: none !important;
            cursor: pointer !important;
            font-size: 13px !important;
            color: #000000 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }
        .simboli-toggle-descrizione:hover {
            color: #e35205 !important;
        }

        /* Bicap.shop Approfondimento (Descrizione breve con sfondo grigio #f3f3f3) */
        .bicap-approfondimento,
        .approfondimento {
            background-color: #f3f3f3 !important;
            padding: 12px 14px !important;
            line-height: 22px !important;
            font-size: 14px !important;
            color: #000000 !important;
            margin: 14px 0 !important;
            border-radius: 0 !important;
            display: block !important;
        }
        .bicap-approfondimento p,
        .approfondimento p {
            margin: 0 !important;
            padding: 0 !important;
            line-height: inherit !important;
            color: #000000 !important;
        }

        /* Bicap.shop Correlati / Potrebbe Interessarti (Banner titolo grigio #f3f3f3) */
        .bicap-correlati-banner {
            background-color: #f3f3f3 !important;
            width: 100% !important;
            padding: 16px 0 14px 0 !important;
            margin-top: 32px !important;
            border-top: 1px solid #e0e0e0 !important;
            box-sizing: border-box !important;
        }
        .bicap-correlati-titolo {
            font-family: 'Bebas Neue', serif !important;
            font-size: 34px !important;
            font-weight: 700 !important;
            color: #000000 !important;
            text-transform: uppercase !important;
            line-height: 1 !important;
            margin: 0 !important;
            text-align: left !important;
        }
        .bicap-correlati-item {
            text-align: center !important;
            display: block !important;
            text-decoration: none !important;
        }
        .bicap-correlati-item h4 {
            font-family: 'Bebas Neue', serif !important;
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #000000 !important;
            text-transform: uppercase !important;
            line-height: 1.15 !important;
            margin: 6px 0 1px 0 !important;
            text-align: center !important;
            transition: color 0.15s ease !important;
        }
        .bicap-correlati-item:hover h4 {
            color: #e35205 !important;
        }
        .bicap-correlati-item .bicap-correlati-code {
            font-family: 'Bebas Neue', serif !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            color: #555555 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            text-align: center !important;
        }
    </style>

    <!-- Tracking Scripts (Head) -->
    @include('public.partials.tracking_scripts_head')

    <!-- Dati Strutturati Sitelink (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "WebSite",
      "name": "BICAP Official Shop",
      "url": "{{ url('/') }}"
    }
    </script>
</head>
<body class="main antialiased bg-white flex flex-col min-h-screen bicap-public-site selection:bg-[#FFED00] selection:text-black">
    <!-- Tracking Scripts (Body) -->
    @include('public.partials.tracking_scripts_body')

    @include('public.partials.header')

    <!-- Page Content -->
    <main class="flex-grow bg-white">
        @yield('content')
    </main>

    @include('public.partials.footer')

    @php
        $globalSettings = \App\Models\Setting::pluck('value', 'key')->all();
        $accessibilityEnabled = !isset($globalSettings['accessibility_panel_enabled']) || $globalSettings['accessibility_panel_enabled'] == '1';
    @endphp

    @if($accessibilityEnabled)
        <x-accessibility-widget />
    @endif

    @stack('scripts')

    <!-- Cookie Consent Banner (BOTTOM) -->
    @include('public.partials.cookie_consent')
</body>
</html>
