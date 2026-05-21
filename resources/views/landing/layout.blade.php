<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Beranda')</title>
    <meta name="description" content="Perpustakaan digital modern dengan nuansa komik interaktif."/>
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>"/>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;600;700;900&family=Fredoka+One&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        :root {
            --comic-cream: #FFF8F0;
            --comic-orange: #FF6B35;
            --comic-dark: #1A1A2E;
            --comic-blue: #4ECDC4;
            --comic-yellow: #FFE66D;
            --comic-red: #FF3366;
            --comic-shadow: #000;
            --shadows: 4px 4px 0px var(--comic-shadow);
            --shadows-lg: 6px 6px 0px var(--comic-shadow);
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--comic-cream);
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
            color: var(--comic-dark);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--comic-cream); }
        ::-webkit-scrollbar-thumb { background: var(--comic-orange); border: 2px solid var(--comic-dark); border-radius: 0; }

        /* ── Comic Navbar ── */
        .comic-navbar {
            background: rgba(26,26,46,0.9) !important;
            backdrop-filter: blur(8px);
            border-bottom: 4px solid var(--comic-orange);
        }
        .comic-brand .brand-icon { font-size: 1.8rem; }
        .comic-brand .brand-text {
            font-family: 'Bangers', cursive;
            font-size: 1.5rem;
            letter-spacing: 2px;
            color: var(--comic-orange);
        }
        .nav-link.active-link {
            background-color: var(--comic-orange) !important;
            color: #fff !important;
            border-radius: 0 !important;
            border: 2px solid var(--comic-dark) !important;
        }

        /* ── Hero ── */
        .comic-hero {
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
            min-height: 100vh;
            color: #fff;
        }
        .hero-bg-pattern {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255,107,53,0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(78,205,196,0.3) 0%, transparent 50%),
                repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(255,255,255,0.02) 40px, rgba(255,255,255,0.02) 41px),
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(255,255,255,0.02) 40px, rgba(255,255,255,0.02) 41px);
            pointer-events: none;
        }

        /* ── Hero Slider Section ── */
        .hero-slider-section {
            background: var(--comic-dark);
            position: relative;
            overflow: hidden;
        }
        /* 16:9 Aspect Ratio Hero — keeps slider at 16:9 on all screens */
        .comic-carousel {
            aspect-ratio: 16 / 9;
            max-height: 56.25vw; /* force 16:9 using viewport width */
            min-height: 320px;
            overflow: hidden;
        }
        .comic-carousel .carousel-inner,
        .comic-carousel .carousel-item {
            aspect-ratio: 16 / 9;
            min-height: unset;
            height: 100%;
        }
        .comic-carousel .carousel-item .container > .row {
            min-height: unset;
            height: 100%;
            align-items: center;
        }
        .comic-carousel .carousel-item {
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: transform 6s ease;
            height: 100%;
        }
        .comic-carousel .carousel-item.active .slide-bg {
            transform: scale(1.05);
        }
        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(26,26,46,0.92) 0%, rgba(26,26,46,0.7) 50%, rgba(15,52,96,0.6) 100%);
        }
        .slide-content {
            position: relative;
            z-index: 2;
            padding: 40px 0;
        }
        .slide-content .row {
            min-height: 0;
            align-items: center;
        }
        .slide-illustration { max-width: 100%; }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .carousel-item.active .slide-content {
            animation: slideInUp 0.8s 0.2s ease-out both;
        }
        .slide-tag {
            display: inline-block;
            background: var(--comic-orange);
            border: 3px solid var(--comic-dark);
            box-shadow: 3px 3px 0 var(--comic-dark);
            font-family: 'Fredoka One', cursive;
            font-size: 0.85rem;
            letter-spacing: 2px;
            padding: 5px 16px;
            margin-bottom: 16px;
            color: #fff;
        }
        .slide-title {
            font-family: 'Bangers', cursive;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            color: #fff;
            line-height: 1.1;
            letter-spacing: 3px;
            text-shadow: 4px 4px 0 var(--comic-orange), 6px 6px 0 rgba(0,0,0,0.4);
            margin-bottom: 16px;
        }
        .slide-subtitle {
            font-family: 'Nunito', sans-serif;
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: rgba(255,255,255,0.85);
            font-weight: 700;
            max-width: 500px;
            margin-bottom: 24px;
        }
        .slide-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-orange-slide {
            background: var(--comic-orange);
            color: #fff;
            border: 3px solid var(--comic-dark);
            border-radius: 0;
            font-family: 'Fredoka One', cursive;
            letter-spacing: 1px;
            box-shadow: 4px 4px 0 var(--comic-dark);
            transition: all 0.2s ease;
        }
        .btn-orange-slide:hover {
            background: var(--comic-yellow);
            color: var(--comic-dark);
            transform: translateY(-3px);
            box-shadow: 6px 8px 0 var(--comic-dark);
        }
        .btn-outline-light-slide {
            background: transparent;
            color: #fff;
            border: 3px solid rgba(255,255,255,0.8);
            border-radius: 0;
            font-family: 'Fredoka One', cursive;
            letter-spacing: 1px;
            box-shadow: 4px 4px 0 rgba(255,255,255,0.2);
            transition: all 0.2s ease;
        }
        .btn-outline-light-slide:hover {
            background: rgba(255,255,255,0.15);
            border-color: #fff;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 6px 8px 0 rgba(255,255,255,0.2);
        }

        /* Carousel Controls */
        .comic-carousel .carousel-control-prev,
        .comic-carousel .carousel-control-next {
            width: 56px;
            height: 56px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            transition: all 0.3s ease;
            z-index: 10;
        }
        .carousel-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: rgba(255,248,240,0.95);
            border: 3px solid var(--comic-dark);
            box-shadow: 4px 4px 0 var(--comic-dark);
            color: var(--comic-dark);
            transition: all 0.25s ease;
        }
        .comic-carousel .carousel-control-prev:hover .carousel-arrow,
        .comic-carousel .carousel-control-next:hover .carousel-arrow {
            background: var(--comic-orange);
            color: #fff;
            transform: scale(1.1);
            box-shadow: 6px 6px 0 var(--comic-dark);
        }
        .comic-carousel .carousel-control-prev { left: 24px; }
        .comic-carousel .carousel-control-next { right: 24px; }

        /* Carousel Indicators */
        .comic-carousel .carousel-indicators {
            bottom: 30px;
            gap: 8px;
            margin: 0;
        }
        .comic-carousel .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 0;
            border: 2px solid #fff;
            background: transparent;
            opacity: 0.6;
            transition: all 0.3s ease;
            margin: 0;
        }
        .comic-carousel .carousel-indicators button.active {
            background: var(--comic-orange);
            border-color: var(--comic-orange);
            opacity: 1;
            transform: scale(1.3);
            box-shadow: 2px 2px 0 var(--comic-dark);
        }

        /* Floating Books Illustration */
        .slide-illustration { position: relative; }
        .floating-books {
            position: relative;
            width: 300px;
            height: 300px;
        }
        .float-book {
            position: absolute;
            font-size: 4rem;
            filter: drop-shadow(3px 3px 0 rgba(0,0,0,0.3));
            animation: floatBook 3s ease-in-out infinite;
        }
        .float-book.fb-1 { top: 10%; left: 5%; animation-delay: 0s; }
        .float-book.fb-2 { top: 5%; left: 35%; animation-delay: 0.5s; }
        .float-book.fb-3 { top: 20%; left: 60%; animation-delay: 1s; }
        .float-book.fb-4 { top: 40%; left: 75%; animation-delay: 1.5s; }
        @keyframes floatBook {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }

        /* Illustration Image */
        .slide-illustration-image {
            position: relative;
            width: 320px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ill-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(4px 4px 0 var(--comic-dark));
            animation: illFloat 4s ease-in-out infinite;
        }
        .ill-glow {
            position: absolute;
            inset: -20px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,107,53,0.2) 0%, transparent 70%);
            animation: illPulse 3s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes illFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-12px) scale(1.02); }
        }
        @keyframes illPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }

        /* Min vh utility */
        .min-vh-600 { min-height: 600px; }

        /* Stats Banner */
        .stats-banner {
            background: var(--comic-dark);
            border-bottom: 4px solid var(--comic-orange);
        }
        .stat-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .stat-number {
            font-family: 'Bangers', cursive;
            font-size: 2rem;
            color: var(--comic-orange);
            line-height: 1;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 rgba(0,0,0,0.3);
        }
        .stat-text {
            font-size: 0.7rem;
            font-weight: 900;
            color: rgba(255,255,255,0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ── Search CTA Section ── */
        .search-cta-section {
            background: var(--comic-cream);
            position: relative;
            overflow: hidden;
        }
        .search-cta-card {
            position: relative;
            background: var(--comic-cream);
            border: 4px solid var(--comic-dark);
            box-shadow: 8px 8px 0 var(--comic-orange);
            padding: 50px 40px;
            text-align: center;
            overflow: hidden;
        }
        .search-cta-card::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            background: var(--comic-orange);
            border: 4px solid var(--comic-dark);
            transform: rotate(15deg);
            opacity: 0.3;
        }
        .scta-deco {
            position: absolute;
            font-size: 5rem;
            opacity: 0.15;
            top: 10px;
        }
        .scta-deco.scta-left { left: 20px; animation: sctaFloatL 4s ease-in-out infinite; }
        .scta-deco.scta-right { right: 20px; animation: sctaFloatR 4s ease-in-out infinite; }
        @keyframes sctaFloatL {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-10px) rotate(0deg); }
        }
        @keyframes sctaFloatR {
            0%, 100% { transform: translateY(0) rotate(5deg); }
            50% { transform: translateY(-10px) rotate(0deg); }
        }
        .scta-label {
            font-family: 'Fredoka One', cursive;
            font-size: 0.8rem;
            letter-spacing: 4px;
            color: var(--comic-orange);
            margin-bottom: 8px;
        }
        .scta-title {
            font-family: 'Bangers', cursive;
            font-size: 2.5rem;
            color: var(--comic-dark);
            letter-spacing: 3px;
            line-height: 1.1;
            margin-bottom: 12px;
        }
        .scta-desc {
            color: #666;
            font-weight: 700;
            font-size: 1rem;
            max-width: 500px;
            margin: 0 auto 28px;
        }
        .scta-search-form {
            max-width: 680px;
            margin: 0 auto;
        }
        .scta-inputs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .scta-input-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .scta-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
        }
        .scta-input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 3px solid var(--comic-dark);
            background: #fff;
            font-weight: 800;
            font-size: 1rem;
            border-radius: 0;
            box-shadow: 4px 4px 0 var(--comic-dark);
            outline: none;
        }
        .scta-input:focus {
            border-color: var(--comic-orange);
            box-shadow: 5px 5px 0 var(--comic-orange);
        }
        .scta-select {
            padding: 12px 16px;
            border: 3px solid var(--comic-dark);
            background: #fff;
            font-weight: 800;
            border-radius: 0;
            box-shadow: 4px 4px 0 var(--comic-dark);
            min-width: 160px;
        }
        .scta-select:focus {
            border-color: var(--comic-orange);
            box-shadow: 5px 5px 0 var(--comic-orange);
            outline: none;
        }
        .scta-btn {
            padding: 14px 28px;
            font-family: 'Bangers', cursive;
            font-size: 1.1rem;
            letter-spacing: 2px;
            border: 3px solid var(--comic-dark);
            box-shadow: 4px 4px 0 var(--comic-dark);
            border-radius: 0;
            white-space: nowrap;
        }
        .scta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 6px 6px 0 var(--comic-dark);
        }
        .scta-hint {
            margin-top: 16px;
            font-size: 0.8rem;
            color: #888;
            font-weight: 700;
        }

        /* ── Scroll Animations ── */
        @keyframes comicBounceIn {
            0% { opacity: 0; transform: scale(0.3) translateY(40px); }
            50% { transform: scale(1.05) translateY(-5px); }
            70% { transform: scale(0.95) translateY(3px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0) rotate(-10deg); }
            70% { transform: scale(1.2) rotate(5deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        .anim-bounce { animation: comicBounceIn 0.7s cubic-bezier(0.36,0.07,0.19,0.97) both; }
        .anim-slide-up { animation: slideUpFade 0.6s ease-out both; }
        .anim-pop { animation: popIn 0.5s cubic-bezier(0.36,0.07,0.19,0.97) both; }

        /* ── Hero fallback (no slides) ── */
        .hero-fallback {
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
            min-height: 100vh;
        }
        .min-h-500 { min-height: 500px; }

        /* Slider Navbar */
        .comic-navbar-slider {
            background: var(--comic-dark) !important;
            position: sticky;
            top: 0;
            z-index: 1020;
            border-bottom: 4px solid var(--comic-orange) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .comic-navbar-slider.scrolled {
            background: rgba(26,26,46,0.98) !important;
        }
        .comic-navbar-slider .nav-link.btn-outline-light {
            border-color: rgba(255,255,255,0.5);
            color: #fff;
            border-radius: 0;
        }
        .comic-navbar-slider .nav-link.btn-outline-light:hover {
            background: var(--comic-orange);
            border-color: var(--comic-orange);
            color: #fff;
        }
        .comic-title {
            font-family: 'Bangers', cursive;
            font-size: clamp(3rem, 8vw, 5.5rem);
            line-height: 1;
            letter-spacing: 3px;
            text-shadow: 4px 4px 0px var(--comic-orange), 6px 6px 0px var(--comic-shadow);
        }
        .text-orange { color: var(--comic-orange) !important; }
        .bg-orange { background-color: var(--comic-orange) !important; }
        .btn-orange {
            background-color: var(--comic-orange);
            color: #fff;
            border: 3px solid var(--comic-dark);
            border-radius: 0;
            font-family: 'Fredoka One', cursive;
            letter-spacing: 1px;
        }
        .btn-orange:hover {
            background-color: var(--comic-yellow);
            color: var(--comic-dark);
            transform: translateY(-3px);
            box-shadow: 6px 6px 0 var(--comic-dark);
        }
        .btn-dark { border-radius: 0; border: 3px solid var(--comic-dark); }
        .shadow-comic { box-shadow: 4px 4px 0 var(--comic-dark) !important; }
        .btn-lg { padding: 0.75rem 1.5rem; font-size: 1rem; }

        /* ── Speech Bubble ── */
        .speech-bubble {
            position: relative;
            background: rgba(255,248,240,0.95);
            padding: 30px 35px;
            border: 4px solid var(--comic-dark);
            box-shadow: 6px 6px 0 var(--comic-dark);
        }
        .speech-bubble::before {
            content: '';
            position: absolute;
            bottom: -25px;
            left: 40px;
            border: 12px solid transparent;
            border-top: 15px solid var(--comic-dark);
        }
        .speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -16px;
            left: 43px;
            border: 9px solid transparent;
            border-top: 11px solid rgba(255,248,240,0.95);
        }

        /* ── Stats ── */
        .stat-badges { gap: 1.5rem; }
        .stat-item { text-align: center; }
        .stat-num {
            display: block;
            font-family: 'Bangers', cursive;
            font-size: 2.2rem;
            color: var(--comic-orange);
            line-height: 1;
            text-shadow: 2px 2px 0 var(--comic-shadow);
        }
        .stat-label { font-size: 0.75rem; color: #aaa; letter-spacing: 2px; text-transform: uppercase; }
        .stat-divider { font-size: 2rem; color: rgba(255,255,255,0.2); align-self: center; }

        /* ── Hero Book Stack ── */
        .hero-comic-panel {
            position: relative;
        }
        .panel-inner { position: relative; width: 300px; height: 300px; margin: 0 auto; }
        .book-stack {
            position: relative;
            width: 220px;
            height: 260px;
            margin: 0 auto;
        }
        .stacked-book {
            position: absolute;
            width: 120px;
            height: 160px;
            border: 3px solid var(--comic-dark);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            box-shadow: 3px 3px 0 var(--comic-dark);
            transition: transform 0.3s;
        }
        .stacked-book:nth-child(1) { background: #e74c3c; top: 0; left: 0; transform: rotate(-6deg); z-index: 1; }
        .stacked-book:nth-child(2) { background: #3498db; top: 5px; left: 20px; transform: rotate(-2deg); z-index: 2; }
        .stacked-book:nth-child(3) { background: #2ecc71; top: 10px; left: 40px; transform: rotate(2deg); z-index: 3; }
        .stacked-book:nth-child(4) { background: #f39c12; top: 15px; left: 60px; transform: rotate(5deg); z-index: 4; }
        .stacked-book:nth-child(5) { background: #9b59b6; top: 20px; left: 80px; transform: rotate(8deg); z-index: 5; }
        .book-stack:hover .stacked-book:nth-child(1) { transform: rotate(-12deg) translateY(-10px); }
        .book-stack:hover .stacked-book:nth-child(2) { transform: rotate(-5deg) translateY(-8px); }
        .book-stack:hover .stacked-book:nth-child(3) { transform: rotate(3deg) translateY(-6px); }
        .book-stack:hover .stacked-book:nth-child(4) { transform: rotate(10deg) translateY(-8px); }
        .book-stack:hover .stacked-book:nth-child(5) { transform: rotate(15deg) translateY(-10px); }

        .pop-effect {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            font-family: 'Bangers', cursive;
            font-size: 2.5rem;
            color: var(--comic-yellow);
            text-shadow: 3px 3px 0 var(--comic-dark);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
        }

        /* ── Section Labels ── */
        .section-label {
            font-family: 'Bangers', cursive;
            font-size: 1rem;
            letter-spacing: 4px;
            color: var(--comic-orange);
            margin-bottom: 4px;
        }
        .comic-section-title {
            font-family: 'Bangers', cursive;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            letter-spacing: 2px;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.15);
            color: var(--comic-dark);
        }

        /* ── Category Bubbles ── */
        .category-section { background: var(--comic-cream); }
        .category-bubble {
            background: #fff;
            border: 3px solid var(--comic-dark);
            box-shadow: 4px 4px 0 var(--comic-dark);
            padding: 25px 15px;
            border-radius: 20px;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        .category-bubble:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 6px 10px 0 var(--comic-dark);
            background: var(--comic-orange);
        }
        .category-bubble:hover .bubble-name,
        .category-bubble:hover .bubble-count { color: #fff; }
        .category-bubble:hover .bubble-icon { transform: scale(1.3) rotate(-10deg); }
        .bubble-icon { font-size: 2.5rem; transition: transform 0.3s; margin-bottom: 8px; }
        .bubble-name { font-family: 'Fredoka One', cursive; font-size: 0.95rem; color: var(--comic-dark); transition: color 0.3s; }
        .bubble-count { font-size: 0.75rem; color: #888; transition: color 0.3s; }

        /* ── Popular Section ── */
        .popular-section { background: var(--comic-dark); padding-top: 80px; padding-bottom: 80px; }
        .popular-section .comic-section-title { color: #fff; }
        .popular-section .section-label { color: var(--comic-yellow); }
        #prevPopular, #nextNext { border: 2px solid var(--comic-dark); }

        /* ── Carousel ── */
        .comic-carousel { overflow: hidden; }
        .carousel-track {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding: 10px 5px 20px;
        }
        .carousel-track::-webkit-scrollbar { display: none; }
        .comic-card-wrapper { flex: 0 0 auto; }

        /* ── Comic Card ── */
        .comic-card {
            width: 200px;
            background: #fff;
            border: 3px solid var(--comic-dark);
            box-shadow: 4px 4px 0 var(--comic-dark);
            border-radius: 0;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }
        .book-card { width: 100%; }
        .comic-card:hover {
            transform: translateY(-10px) rotate(-1deg);
            box-shadow: 8px 12px 0 var(--comic-dark);
        }
        .comic-card:hover .pop-overlay {
            opacity: 1;
            transform: scale(1) rotate(-5deg);
        }
        .card-comic-border {
            position: relative;
            background: #eee;
            overflow: hidden;
        }
        .comic-book-cover {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }
        .comic-no-cover {
            height: 240px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Bangers', cursive;
            font-size: 4rem;
            color: #fff;
            background: var(--comic-orange);
        }
        .pop-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background: var(--comic-yellow);
            border: 3px solid var(--comic-dark);
            font-family: 'Bangers', cursive;
            font-size: 1.5rem;
            padding: 8px 16px;
            box-shadow: 3px 3px 0 var(--comic-dark);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: var(--comic-dark);
            white-space: nowrap;
        }
        .stock-warning-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--comic-yellow);
            border: 2px solid var(--comic-dark);
            color: var(--comic-dark);
            font-family: 'Fredoka One', cursive;
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        .stock-empty-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--comic-red);
            border: 2px solid var(--comic-dark);
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        .card-body { padding: 12px; }
        .card-tag {
            display: inline-block;
            background: var(--comic-cream);
            border: 1px solid var(--comic-dark);
            font-size: 0.65rem;
            font-weight: 900;
            padding: 2px 8px;
            margin-bottom: 6px;
            color: var(--comic-dark);
        }
        .card-title {
            font-family: 'Fredoka One', cursive;
            font-size: 0.9rem;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .card-author {
            font-size: 0.75rem;
            color: #777;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .stock-badge {
            font-size: 0.7rem;
            font-weight: 900;
            color: var(--comic-dark);
        }
        .stock-badge.stock-empty { color: var(--comic-red); }
        .rack-location { font-size: 0.7rem; color: #aaa; font-weight: 700; }

        /* ── Books Grid ── */
        .books-grid-section .bg-cream { background: var(--comic-cream); }
        .comic-search-bar {
            background: var(--comic-dark);
            border: 3px solid var(--comic-dark);
            box-shadow: 6px 6px 0 var(--comic-orange);
            padding: 25px;
        }
        .comic-search-bar .form-control,
        .comic-search-bar .form-select {
            border: 2px solid var(--comic-dark);
            border-radius: 0;
            font-weight: 700;
        }
        .comic-search-bar .form-control:focus,
        .comic-search-bar .form-select:focus {
            border-color: var(--comic-orange);
            box-shadow: 4px 4px 0 var(--comic-orange);
            outline: none;
        }

        /* ── Empty State ── */
        .empty-comic-box {
            background: #fff;
            border: 4px dashed var(--comic-dark);
            padding: 50px;
            max-width: 500px;
            margin: 0 auto;
        }
        .empty-icon { font-size: 4rem; margin-bottom: 15px; }

        /* ── Modal ── */
        .comic-modal {
            border: 4px solid var(--comic-dark);
            box-shadow: 8px 8px 0 var(--comic-dark);
            border-radius: 0;
            background: var(--comic-cream);
        }
        .comic-modal .modal-header {
            background: var(--comic-dark);
            color: #fff;
        }
        .comic-modal .modal-title {
            font-family: 'Bangers', cursive;
            font-size: 1.8rem;
            letter-spacing: 2px;
        }
        .comic-modal .btn-close {
            filter: invert(1);
            border: 2px solid #fff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .info-item {
            background: #fff;
            border: 2px solid var(--comic-dark);
            padding: 10px;
            text-align: center;
        }
        .info-label { display: block; font-size: 0.75rem; font-weight: 900; color: #888; }
        .info-value { display: block; font-size: 0.95rem; font-weight: 900; color: var(--comic-dark); }
        .synopsis-box {
            background: #fff;
            border: 2px solid var(--comic-dark);
            padding: 15px;
        }

        /* ── Footer ── */
        .comic-footer {
            background: var(--comic-dark);
            border-top: 5px solid var(--comic-orange);
        }
        .comic-footer .text-light { color: rgba(255,255,255,0.7) !important; }
        .border-dark { border-color: var(--comic-dark) !important; }

        /* ── Pagination ── */
        .pagination { gap: 4px; }
        .pagination .page-link {
            border-radius: 0;
            border: 2px solid var(--comic-dark);
            font-weight: 900;
            color: var(--comic-dark);
        }
        .pagination .page-item.active .page-link {
            background: var(--comic-orange);
            border-color: var(--comic-dark);
            color: #fff;
        }
        .pagination .page-link:hover {
            background: var(--comic-yellow);
            border-color: var(--comic-dark);
        }

        /* ── Books Page ── */
        .page-hero {
            background: var(--comic-dark);
            padding: 60px 0;
            border-bottom: 5px solid var(--comic-orange);
            position: relative;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                45deg, transparent, transparent 30px,
                rgba(255,107,53,0.05) 30px, rgba(255,107,53,0.05) 31px
            );
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .comic-title { font-size: 3rem; }
            .panel-inner { width: 200px; height: 200px; }
            .book-stack { width: 150px; height: 200px; }
            .stacked-book { width: 80px; height: 110px; font-size: 2rem; }
            .info-grid { grid-template-columns: 1fr; }
            .comic-card { width: 100%; }
            .book-card { width: 100%; }
        }

        /* ── Boom text animation ── */
        .boom-text {
            display: inline-block;
            animation: boom 1s ease-in-out infinite;
        }
        @keyframes boom {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.3) rotate(-10deg); }
        }

        /* ── Categories page grid ── */
        .category-page-card {
            background: #fff;
            border: 3px solid var(--comic-dark);
            box-shadow: 4px 4px 0 var(--comic-dark);
            padding: 30px;
            text-align: center;
            transition: all 0.25s;
            cursor: pointer;
        }
        .category-page-card:hover {
            transform: translateY(-8px);
            box-shadow: 6px 8px 0 var(--comic-dark);
            background: var(--comic-orange);
        }
        .category-page-card:hover .cat-name { color: #fff; }
        .category-page-card:hover .cat-count { color: rgba(255,255,255,0.8); }
        .cat-icon { font-size: 3rem; margin-bottom: 12px; display: block; }
        .cat-name { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: var(--comic-dark); transition: color 0.3s; }
        .cat-count { font-size: 0.8rem; color: #888; font-weight: 700; transition: color 0.3s; }

        /* ── Book detail page ── */
        .detail-hero {
            background: linear-gradient(135deg, var(--comic-dark) 0%, #0F3460 100%);
            padding: 60px 0;
            border-bottom: 5px solid var(--comic-orange);
        }
        .book-detail-card {
            background: #fff;
            border: 4px solid var(--comic-dark);
            box-shadow: 6px 6px 0 var(--comic-dark);
            padding: 30px;
        }
        .detail-cover {
            width: 100%;
            max-width: 280px;
            border: 4px solid var(--comic-dark);
            box-shadow: 6px 6px 0 var(--comic-dark);
        }

        /* ── Comic Pagination ── */
        .comic-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 40px;
        }
        .comic-pagination .pagination {
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            min-width: 48px;
            height: 48px;
            padding: 8px 14px;
            background: #fff;
            border: 3px solid var(--comic-dark);
            box-shadow: 3px 3px 0 var(--comic-dark);
            font-family: 'Fredoka One', cursive;
            font-size: 0.85rem;
            color: var(--comic-dark);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            white-space: nowrap;
            border-radius: 0 !important;
        }
        .page-btn:hover:not(.page-btn-disabled):not(.page-btn-active) {
            background: var(--comic-yellow);
            color: var(--comic-dark);
            transform: translateY(-2px);
            box-shadow: 4px 5px 0 var(--comic-dark);
        }
        .page-btn-active {
            background: var(--comic-orange) !important;
            color: #fff !important;
            border-color: var(--comic-dark) !important;
            box-shadow: 4px 4px 0 var(--comic-dark) !important;
            transform: translateY(-2px);
            cursor: default;
        }
        .page-btn-disabled {
            background: #eee !important;
            color: #aaa !important;
            border-color: #ccc !important;
            box-shadow: 2px 2px 0 #ccc !important;
            cursor: not-allowed;
        }
        .page-btn-dots {
            background: #fff !important;
            color: #aaa !important;
            border-color: transparent !important;
            box-shadow: none !important;
            cursor: default;
            pointer-events: none;
        }
        .page-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Fredoka One', cursive;
            font-size: 0.75rem;
            color: #aaa;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
    @stack('custom-css')
</head>
<body>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('vendor-js')
    @stack('custom-js')
</body>
</html>
