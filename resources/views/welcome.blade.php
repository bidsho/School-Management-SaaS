
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SchoolFlow') }} | Smart School Management</title>

    <meta name="description" content="A smarter way to manage your school. Manage students, teachers, attendance, fees, examinations and communication from one powerful platform.">

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #3157d5;
            --primary-dark: #2344b7;
            --secondary: #10234e;
            --dark: #111c35;
            --muted: #718096;
            --light: #f5f7fc;
            --border: #e9edf5;
            --green: #13a879;
            --orange: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--dark);
            background: #fff;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.8px;
        }

        a {
            text-decoration: none;
        }

        .container {
            max-width: 1190px;
        }

        /* NAVIGATION */

        .main-navbar {
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(230,235,245,.8);
            padding: 17px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 11px;
            color: var(--secondary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 21px;
            letter-spacing: -1px;
        }

        .brand-symbol {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #4268eb, #2346bb);
            color: white;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            box-shadow: 0 7px 16px rgba(49,87,213,.23);
        }

        .navbar-nav .nav-link {
            color: #53617a;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 15px !important;
            transition: .25s;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary);
        }

        .btn-primary-custom {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: white;
            border-radius: 9px;
            padding: 12px 23px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: .25s;
            box-shadow: 0 7px 17px rgba(49,87,213,.17);
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(49,87,213,.24);
        }

        .btn-outline-custom {
            border: 1px solid #dce3f0;
            background: white;
            color: var(--secondary);
            border-radius: 9px;
            padding: 12px 23px;
            font-size: 13px;
            font-weight: 700;
            transition: .25s;
        }

        .btn-outline-custom:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        /* HERO */

        .hero-section {
            position: relative;
            padding: 100px 0 105px;
            background:
                radial-gradient(circle at 82% 18%, rgba(89,126,255,.12), transparent 28%),
                radial-gradient(circle at 5% 75%, rgba(49,87,213,.06), transparent 30%),
                #fbfcff;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            width: 450px;
            height: 450px;
            border: 1px solid rgba(49,87,213,.07);
            border-radius: 50%;
            top: -220px;
            right: -100px;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border: 1px solid rgba(49,87,213,.07);
            border-radius: 50%;
            bottom: -190px;
            left: 35%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #edf2ff;
            color: var(--primary);
            border: 1px solid #dce6ff;
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .4px;
            margin-bottom: 25px;
        }

        .hero-badge span {
            width: 7px;
            height: 7px;
            background: #18b886;
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(24,184,134,.12);
        }

        .hero-title {
            font-size: clamp(38px, 5.1vw, 65px);
            line-height: 1.13;
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: -3px;
            margin-bottom: 24px;
        }

        .hero-title .highlight {
            color: var(--primary);
            position: relative;
        }

        .hero-description {
            font-size: 16px;
            line-height: 1.9;
            color: #6b7890;
            max-width: 510px;
            margin-bottom: 31px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 35px;
        }

        .hero-trust {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #77849a;
            font-size: 12px;
            font-weight: 600;
        }

        .hero-trust i {
            color: var(--green);
            font-size: 17px;
        }

        /* DASHBOARD PREVIEW */

        .dashboard-scene {
            position: relative;
            z-index: 2;
            padding: 18px;
        }

        .dashboard-window {
            background: #fff;
            border-radius: 17px;
            border: 1px solid #e9edf6;
            overflow: hidden;
            box-shadow: 0 30px 75px rgba(31,52,100,.14);
            transform: perspective(1200px) rotateY(-4deg) rotateX(2deg);
            transition: .5s;
        }

        .dashboard-window:hover {
            transform: perspective(1200px) rotateY(0) rotateX(0) translateY(-5px);
        }

        .window-top {
            height: 49px;
            background: #fff;
            border-bottom: 1px solid #edf0f6;
            display: flex;
            align-items: center;
            padding: 0 17px;
            gap: 6px;
        }

        .window-dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            background: #dce2ec;
        }

        .window-title {
            font-size: 10px;
            font-weight: 800;
            color: #68758d;
            margin-left: 12px;
        }

        .dashboard-body {
            display: flex;
            min-height: 350px;
            background: #f7f9fd;
        }

        .mock-sidebar {
            width: 125px;
            background: #fff;
            border-right: 1px solid #edf0f6;
            padding: 17px 10px;
            flex-shrink: 0;
        }

        .mock-brand {
            font-size: 10px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 23px;
            padding-left: 5px;
        }

        .mock-nav {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 8px;
            border-radius: 6px;
            font-size: 8px;
            color: #8490a5;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .mock-nav.active {
            background: #edf2ff;
            color: var(--primary);
        }

        .mock-content {
            flex: 1;
            padding: 18px;
            min-width: 0;
        }

        .mock-heading {
            font-size: 14px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 3px;
        }

        .mock-subtitle {
            color: #99a4b5;
            font-size: 8px;
        }

        .mock-stat {
            background: #fff;
            padding: 12px 10px;
            border-radius: 8px;
            border: 1px solid #edf0f6;
            height: 100%;
        }

        .mock-stat-label {
            font-size: 8px;
            color: #8792a7;
            font-weight: 700;
        }

        .mock-stat-value {
            font-size: 19px;
            font-weight: 800;
            color: var(--secondary);
            margin: 5px 0 2px;
        }

        .mock-stat-change {
            font-size: 7px;
            color: #16a879;
            font-weight: 700;
        }

        .mock-chart {
            background: white;
            border: 1px solid #edf0f6;
            padding: 13px;
            border-radius: 8px;
            margin-top: 12px;
        }

        .mock-chart-title {
            font-size: 9px;
            font-weight: 800;
            color: var(--secondary);
        }

        .chart-bars {
            height: 85px;
            display: flex;
            align-items: end;
            justify-content: space-around;
            gap: 7px;
            padding-top: 10px;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, #6d8cff, #3157d5);
            border-radius: 4px 4px 0 0;
            opacity: .85;
        }

        .mock-activity {
            background: white;
            border: 1px solid #edf0f6;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
        }

        .mock-activity-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid #f0f2f7;
            font-size: 8px;
            color: #738097;
        }

        .mock-activity-row:last-child {
            border: 0;
        }

        .mock-avatar {
            width: 19px;
            height: 19px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eaf0ff;
            color: var(--primary);
            margin-right: 5px;
            font-size: 8px;
        }

        .floating-card {
            position: absolute;
            background: white;
            border: 1px solid #edf0f6;
            border-radius: 12px;
            padding: 13px 16px;
            box-shadow: 0 12px 35px rgba(31,52,100,.12);
            z-index: 4;
        }

        .floating-card.one {
            top: 3px;
            right: -10px;
        }

        .floating-card.two {
            bottom: 0;
            left: -12px;
        }

        .floating-card-icon {
            width: 31px;
            height: 31px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            margin-right: 8px;
        }

        .floating-card strong {
            display: block;
            font-size: 12px;
            color: var(--secondary);
        }

        .floating-card small {
            font-size: 9px;
            color: #8994a8;
        }

        /* TRUST STRIP */

        .trust-strip {
            padding: 28px 0;
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .trust-label {
            font-size: 11px;
            color: #99a4b6;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 20px;
        }

        .trust-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            color: #65728a;
            font-size: 12px;
            font-weight: 800;
        }

        .trust-item i {
            color: var(--primary);
            font-size: 20px;
        }

        /* GENERAL SECTION */

        .section-padding {
            padding: 100px 0;
        }

        .section-label {
            display: inline-block;
            color: var(--primary);
            background: #edf2ff;
            border-radius: 30px;
            padding: 7px 13px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 17px;
        }

        .section-title {
            color: var(--secondary);
            font-size: clamp(29px, 3.4vw, 43px);
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -1.8px;
            margin-bottom: 15px;
        }

        .section-description {
            max-width: 610px;
            margin: 0 auto;
            color: #7a879c;
            font-size: 14px;
            line-height: 1.9;
        }

        /* FEATURES */

        .features-section {
            background: #fff;
        }

        .feature-card {
            height: 100%;
            padding: 28px 25px;
            border: 1px solid #edf0f6;
            border-radius: 13px;
            background: white;
            transition: .3s ease;
        }

        .feature-card:hover {
            border-color: #d4defc;
            transform: translateY(-7px);
            box-shadow: 0 17px 35px rgba(34,58,110,.07);
        }

        .feature-icon {
            height: 49px;
            width: 49px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .feature-card h5 {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 11px;
            color: var(--secondary);
        }

        .feature-card p {
            color: #8290a4;
            font-size: 12px;
            line-height: 1.9;
            margin: 0;
        }

        .blue-bg { background: #edf2ff; color: #3157d5; }
        .green-bg { background: #e9faf3; color: #13a879; }
        .orange-bg { background: #fff4e5; color: #e99a22; }
        .purple-bg { background: #f2edff; color: #8055d8; }
        .red-bg { background: #fff0f0; color: #e45d6b; }
        .cyan-bg { background: #e9f8fc; color: #2196b4; }

        /* MODULES */

        .modules-section {
            background: var(--light);
        }

        .module-card {
            background: #fff;
            border: 1px solid #e9edf5;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            height: 100%;
            transition: .25s;
        }

        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(31,52,100,.07);
            border-color: #d5dfff;
        }

        .module-icon {
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            font-size: 19px;
            background: #edf2ff;
            color: var(--primary);
        }

        .module-card h6 {
            font-size: 12px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 4px;
        }

        .module-card p {
            font-size: 10px;
            color: #96a0b1;
            margin: 0;
        }

        /* BENEFITS */

        .benefits-section {
            background: white;
        }

        .benefits-visual {
            min-height: 390px;
            background: linear-gradient(145deg, #1c367d, #3157d5);
            border-radius: 19px;
            position: relative;
            padding: 38px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .benefits-visual::before,
        .benefits-visual::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .benefits-visual::before {
            width: 360px;
            height: 360px;
        }

        .benefits-visual::after {
            width: 480px;
            height: 480px;
        }

        .benefits-panel {
            background: white;
            border-radius: 13px;
            padding: 23px;
            width: 100%;
            max-width: 360px;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 50px rgba(0,0,0,.16);
        }

        .benefits-panel h6 {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .benefit-progress {
            margin-bottom: 15px;
        }

        .benefit-progress label {
            display: flex;
            justify-content: space-between;
            color: #758198;
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .benefit-progress .progress {
            height: 7px;
            border-radius: 20px;
            background: #edf0f6;
        }

        .benefit-progress .progress-bar {
            border-radius: 20px;
            background: var(--primary);
        }

        .benefit-list {
            margin-top: 30px;
        }

        .benefit-item {
            display: flex;
            gap: 15px;
            margin-bottom: 24px;
        }

        .benefit-check {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e9faf3;
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .benefit-item h6 {
            color: var(--secondary);
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .benefit-item p {
            font-size: 12px;
            color: #8995a8;
            line-height: 1.8;
            margin: 0;
        }

        /* HOW IT WORKS */

        .steps-section {
            background: var(--light);
        }

        .step-card {
            background: white;
            border: 1px solid #edf0f6;
            border-radius: 13px;
            padding: 27px;
            text-align: center;
            height: 100%;
            position: relative;
        }

        .step-number {
            width: 45px;
            height: 45px;
            margin: 0 auto 18px;
            background: #edf2ff;
            color: var(--primary);
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 800;
        }

        .step-card h5 {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .step-card p {
            color: #8591a5;
            font-size: 12px;
            line-height: 1.8;
            margin: 0;
        }

        /* CTA */

        .cta-section {
            padding: 85px 0;
            background: #10234e;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: "";
            position: absolute;
            width: 450px;
            height: 450px;
            right: -120px;
            top: -260px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .cta-section::after {
            content: "";
            position: absolute;
            width: 350px;
            height: 350px;
            left: -160px;
            bottom: -250px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            color: white;
            font-size: clamp(28px, 4vw, 43px);
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-bottom: 15px;
        }

        .cta-section p {
            color: #b7c4e1;
            font-size: 14px;
            line-height: 1.9;
            max-width: 600px;
            margin: 0 auto 27px;
        }

        .btn-white {
            background: white;
            color: var(--secondary);
            padding: 13px 25px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 800;
            transition: .25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-white:hover {
            background: #edf2ff;
            color: var(--primary);
            transform: translateY(-3px);
        }

        /* FOOTER */

        .main-footer {
            background: #0b1732;
            color: #fff;
            padding: 65px 0 25px;
        }

        .footer-description {
            color: #94a2bd;
            font-size: 12px;
            line-height: 1.9;
            max-width: 310px;
            margin-top: 17px;
        }

        .footer-heading {
            color: white;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links a {
            color: #94a2bd;
            font-size: 11px;
            transition: .2s;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 4px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.09);
            margin-top: 48px;
            padding-top: 22px;
            color: #7f8ca6;
            font-size: 11px;
        }

        .social-link {
            width: 33px;
            height: 33px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,.08);
            color: white;
            margin-right: 5px;
            transition: .2s;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
        }

        /* ANIMATIONS */

        .reveal {
            animation: revealUp .8s ease both;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            .hero-section {
                padding: 75px 0 85px;
            }

            .hero-title {
                font-size: 48px;
            }

            .dashboard-scene {
                margin-top: 45px;
                max-width: 650px;
                margin-left: auto;
                margin-right: auto;
            }

            .floating-card.one {
                right: 0;
            }

            .floating-card.two {
                left: 0;
            }

            .section-padding {
                padding: 75px 0;
            }
        }

        @media (max-width: 767px) {
            .hero-section {
                padding: 60px 0 70px;
            }

            .hero-title {
                font-size: 40px;
                letter-spacing: -2px;
            }

            .hero-description {
                font-size: 14px;
            }

            .dashboard-scene {
                padding: 10px;
            }

            .dashboard-window {
                transform: none;
            }

            .floating-card {
                padding: 9px 11px;
            }

            .floating-card.one {
                right: -2px;
                top: -12px;
            }

            .floating-card.two {
                left: -2px;
                bottom: -13px;
            }

            .mock-sidebar {
                width: 85px;
            }

            .mock-content {
                padding: 10px;
            }

            .mock-stat-value {
                font-size: 15px;
            }

            .section-padding {
                padding: 65px 0;
            }

            .benefits-visual {
                min-height: 330px;
                padding: 20px;
            }

            .trust-item {
                margin: 10px 0;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 35px;
            }

            .hero-actions a {
                width: 100%;
            }

            .mock-sidebar {
                width: 65px;
                padding: 12px 5px;
            }

            .mock-nav {
                font-size: 7px;
                padding: 8px 5px;
                gap: 4px;
            }

            .mock-brand {
                font-size: 8px;
                padding-left: 2px;
            }

            .mock-stat {
                padding: 9px 6px;
            }

            .mock-stat-label {
                font-size: 7px;
            }

            .mock-stat-value {
                font-size: 13px;
            }

            .mock-chart {
                padding: 8px;
            }

            .floating-card strong {
                font-size: 10px;
            }

            .floating-card small {
                font-size: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body>

{{-- NAVIGATION --}}
<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">

        <a class="brand-logo" href="{{ url('/') }}">
            <span class="brand-symbol">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
            <span>SchoolFlow</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <i class="bi bi-list fs-2 text-primary"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">

            <ul class="navbar-nav mx-auto my-3 my-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#modules">Modules</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#how-it-works">How It Works</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-wrap">

                @if (Route::has('login'))
                    @auth
                        <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}"
                           class="btn-outline-custom">
                            <i class="bi bi-grid me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-outline-custom">
                            Log In
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary-custom">
                                Get Started <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    @endauth
                @endif

            </div>

        </div>
    </div>
</nav>


{{-- HERO SECTION --}}
<section class="hero-section" id="home">

    <div class="container">
        <div class="row align-items-center g-4">

            <div class="col-lg-6 hero-content reveal">

                <div class="hero-badge">
                    <span></span>
                    THE SMARTER WAY TO RUN YOUR SCHOOL
                </div>

                <h1 class="hero-title">
                    Manage Your School.
                    <span class="highlight">Simplify</span>
                    Your Future.
                </h1>

                <p class="hero-description">
                    Everything your school needs, all in one place.
                    Manage students, teachers, attendance, examinations,
                    fees and communication with one powerful school
                    management platform.
                </p>

                <div class="hero-actions">

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary-custom">
                            Get Started Today
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    @elseif (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn-primary-custom">
                            Access Your School
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    @endif

                    <a href="#features" class="btn-outline-custom">
                        Explore Features
                        <i class="bi bi-arrow-down"></i>
                    </a>

                </div>

                <div class="hero-trust">
                    <i class="bi bi-shield-check"></i>
                    Secure school management
                    <span class="text-muted">•</span>
                    Easy to use
                    <span class="text-muted">•</span>
                    Cloud based
                </div>

            </div>


            {{-- Dashboard Preview --}}
            <div class="col-lg-6">

                <div class="dashboard-scene">

                    <div class="floating-card one">
                        <div class="d-flex align-items-center">
                            <span class="floating-card-icon green-bg">
                                <i class="bi bi-check-circle-fill"></i>
                            </span>
                            <div>
                                <strong>Attendance Updated</strong>
                                <small>School records are current</small>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-window">

                        <div class="window-top">
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                            <span class="window-title">School Management Dashboard</span>
                        </div>

                        <div class="dashboard-body">

                            <div class="mock-sidebar">

                                <div class="mock-brand">
                                    <i class="bi bi-mortarboard-fill text-primary me-1"></i>
                                    SCHOOL
                                </div>

                                <div class="mock-nav active">
                                    <i class="bi bi-grid"></i> Overview
                                </div>

                                <div class="mock-nav">
                                    <i class="bi bi-people"></i> Students
                                </div>

                                <div class="mock-nav">
                                    <i class="bi bi-person-badge"></i> Teachers
                                </div>

                                <div class="mock-nav">
                                    <i class="bi bi-calendar-check"></i> Attendance
                                </div>

                                <div class="mock-nav">
                                    <i class="bi bi-cash-stack"></i> Fees
                                </div>

                                <div class="mock-nav">
                                    <i class="bi bi-journal-text"></i> Exams
                                </div>

                            </div>

                            <div class="mock-content">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="mock-heading">Dashboard Overview</div>
                                        <div class="mock-subtitle">Your school at a glance</div>
                                    </div>

                                    <span class="badge text-bg-primary rounded-pill" style="font-size:7px">
                                        Live Preview
                                    </span>
                                </div>

                                <div class="row g-2">

                                    <div class="col-6 col-md-3">
                                        <div class="mock-stat">
                                            <div class="mock-stat-label">Students</div>
                                            <div class="mock-stat-value">1,248</div>
                                            <div class="mock-stat-change">
                                                <i class="bi bi-arrow-up"></i> Enrolled
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <div class="mock-stat">
                                            <div class="mock-stat-label">Teachers</div>
                                            <div class="mock-stat-value">86</div>
                                            <div class="mock-stat-change">Staff records</div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <div class="mock-stat">
                                            <div class="mock-stat-label">Attendance</div>
                                            <div class="mock-stat-value">94%</div>
                                            <div class="mock-stat-change">Daily overview</div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <div class="mock-stat">
                                            <div class="mock-stat-label">Classes</div>
                                            <div class="mock-stat-value">32</div>
                                            <div class="mock-stat-change">Active classes</div>
                                        </div>
                                    </div>

                                </div>

                                <div class="mock-chart">

                                    <div class="d-flex justify-content-between">
                                        <span class="mock-chart-title">School Performance Overview</span>
                                        <span class="text-primary" style="font-size:8px">This year</span>
                                    </div>

                                    <div class="chart-bars">
                                        <div class="chart-bar" style="height:42%"></div>
                                        <div class="chart-bar" style="height:62%"></div>
                                        <div class="chart-bar" style="height:50%"></div>
                                        <div class="chart-bar" style="height:78%"></div>
                                        <div class="chart-bar" style="height:65%"></div>
                                        <div class="chart-bar" style="height:90%"></div>
                                        <div class="chart-bar" style="height:75%"></div>
                                        <div class="chart-bar" style="height:100%"></div>
                                    </div>

                                </div>

                                <div class="mock-activity">

                                    <div class="mock-chart-title mb-2">
                                        Recent School Activities
                                    </div>

                                    <div class="mock-activity-row">
                                        <span>
                                            <span class="mock-avatar">S</span>
                                            Student enrollment
                                        </span>
                                        <span class="text-success">Completed</span>
                                    </div>

                                    <div class="mock-activity-row">
                                        <span>
                                            <span class="mock-avatar">T</span>
                                            Teacher records updated
                                        </span>
                                        <span class="text-success">Completed</span>
                                    </div>

                                    <div class="mock-activity-row">
                                        <span>
                                            <span class="mock-avatar">F</span>
                                            Fee payment recorded
                                        </span>
                                        <span class="text-primary">Recorded</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="floating-card two">
                        <div class="d-flex align-items-center">
                            <span class="floating-card-icon blue-bg">
                                <i class="bi bi-mortarboard-fill"></i>
                            </span>
                            <div>
                                <strong>One Connected Platform</strong>
                                <small>School operations simplified</small>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</section>


{{-- TRUST STRIP --}}
<section class="trust-strip">

    <div class="container">

        <div class="trust-label">
            EVERYTHING YOUR SCHOOL NEEDS IN ONE PLATFORM
        </div>

        <div class="row g-3">

            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-mortarboard"></i>
                    Student Management
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-person-video3"></i>
                    Staff Management
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-cash-coin"></i>
                    School Finance
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <i class="bi bi-bar-chart"></i>
                    School Reports
                </div>
            </div>

        </div>

    </div>

</section>


{{-- FEATURES --}}
<section class="section-padding features-section" id="features">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-label">Powerful Features</span>

            <h2 class="section-title">
                Everything You Need to <br>
                Run Your School Efficiently
            </h2>

            <p class="section-description">
                Bring your school's daily operations together with
                intelligent tools designed to save time, improve
                organisation and make better decisions.
            </p>

        </div>


        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon blue-bg">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h5>Student Management</h5>
                    <p>
                        Manage admissions, student profiles, guardians,
                        class placement and academic records from one
                        central location.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon green-bg">
                        <i class="bi bi-person-video3"></i>
                    </div>
                    <h5>Staff Management</h5>
                    <p>
                        Organise teachers and school employees, manage
                        assignments, staff records and responsibilities.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon orange-bg">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <h5>Attendance Tracking</h5>
                    <p>
                        Record and monitor daily student attendance.
                        Keep track of absences and attendance history.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon purple-bg">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <h5>Examinations & Results</h5>
                    <p>
                        Organise examinations, record scores, calculate
                        grades and generate academic performance reports.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon red-bg">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h5>Fees & Payments</h5>
                    <p>
                        Manage fee structures, invoices, payments,
                        outstanding balances and receipts.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon cyan-bg">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5>School Communication</h5>
                    <p>
                        Keep parents, teachers and students informed
                        through school announcements and updates.
                    </p>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- MODULES --}}
<section class="section-padding modules-section" id="modules">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-label">Complete School Solution</span>

            <h2 class="section-title">
                One Platform. Multiple School Operations.
            </h2>

            <p class="section-description">
                Manage every important department through a connected
                school management system.
            </p>

        </div>


        <div class="row g-3">

            @php
                $modules = [
                    ['bi-people-fill', 'Student Records', 'Student profiles and admissions'],
                    ['bi-person-badge-fill', 'Teacher Management', 'Teacher profiles and assignments'],
                    ['bi-building', 'Classes & Sections', 'Organise classes and sections'],
                    ['bi-calendar-week', 'Timetable', 'Manage academic schedules'],
                    ['bi-calendar-check', 'Attendance', 'Daily attendance records'],
                    ['bi-journal-bookmark-fill', 'Subjects', 'Academic subject management'],
                    ['bi-file-earmark-text', 'Assignments', 'Manage student assignments'],
                    ['bi-pencil-square', 'Examinations', 'Exam planning and score records'],
                    ['bi-award-fill', 'Grading System', 'Academic grades and results'],
                    ['bi-wallet-fill', 'Fee Management', 'Fees, invoices and payments'],
                    ['bi-receipt', 'Receipts', 'Payment receipts and records'],
                    ['bi-person-hearts', 'Parent Portal', 'Parent and guardian access'],
                    ['bi-megaphone-fill', 'Announcements', 'School-wide communication'],
                    ['bi-person-vcard', 'Reception Desk', 'Front office and enquiries'],
                    ['bi-graph-up-arrow', 'Reports', 'School performance reports'],
                    ['bi-gear-fill', 'School Settings', 'Configure your school'],
                ];
            @endphp

            @foreach ($modules as $module)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="bi {{ $module[0] }}"></i>
                        </div>

                        <div>
                            <h6>{{ $module[1] }}</h6>
                            <p>{{ $module[2] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>

</section>


{{-- BENEFITS --}}
<section class="section-padding benefits-section">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <div class="benefits-visual">

                    <div class="benefits-panel">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">School Activity Summary</h6>
                                <small class="text-muted" style="font-size:10px">
                                    Illustrative dashboard preview
                                </small>
                            </div>

                            <i class="bi bi-three-dots fs-5 text-muted"></i>
                        </div>

                        <div class="benefit-progress">
                            <label>
                                <span>Student Records</span>
                                <span>Organised</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar" style="width:88%"></div>
                            </div>
                        </div>

                        <div class="benefit-progress">
                            <label>
                                <span>Staff Records</span>
                                <span>Organised</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width:75%"></div>
                            </div>
                        </div>

                        <div class="benefit-progress">
                            <label>
                                <span>Academic Activities</span>
                                <span>Organised</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width:92%"></div>
                            </div>
                        </div>

                        <div class="benefit-progress mb-0">
                            <label>
                                <span>Financial Records</span>
                                <span>Organised</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width:80%"></div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-lg-6">

                <span class="section-label">Why Choose SchoolFlow?</span>

                <h2 class="section-title">
                    Spend Less Time on Administration.
                    More Time on Education.
                </h2>

                <p class="section-description text-start mx-0">
                    School administration should not be complicated.
                    Give your team the tools to manage school activities
                    with clarity, accuracy and confidence.
                </p>


                <div class="benefit-list">

                    <div class="benefit-item">
                        <div class="benefit-check">
                            <i class="bi bi-check-lg"></i>
                        </div>

                        <div>
                            <h6>Centralised School Information</h6>
                            <p>
                                Keep student, staff, academic and financial
                                records organised in one place.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-check">
                            <i class="bi bi-check-lg"></i>
                        </div>

                        <div>
                            <h6>Role-Based Access</h6>
                            <p>
                                Give administrators, teachers, parents and
                                students access to the tools relevant to them.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-check">
                            <i class="bi bi-check-lg"></i>
                        </div>

                        <div>
                            <h6>Better School Organisation</h6>
                            <p>
                                Simplify daily processes and keep school
                                activities easier to monitor.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-check">
                            <i class="bi bi-check-lg"></i>
                        </div>

                        <div>
                            <h6>Access From Anywhere</h6>
                            <p>
                                Use the platform from your computer, tablet
                                or mobile device.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- HOW IT WORKS --}}
<section class="section-padding steps-section" id="how-it-works">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-label">Getting Started</span>

            <h2 class="section-title">
                Start Managing Your School in 3 Steps
            </h2>

            <p class="section-description">
                A straightforward onboarding process designed to help
                your school get organised.
            </p>

        </div>


        <div class="row g-4">

            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">01</div>

                    <h5>Register Your School</h5>

                    <p>
                        Create your school account and provide the
                        basic information needed to set up your portal.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">02</div>

                    <h5>Set Up Your School</h5>

                    <p>
                        Configure academic sessions, classes, staff,
                        subjects and other school information.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">03</div>

                    <h5>Manage Your Activities</h5>

                    <p>
                        Start managing admissions, attendance, results,
                        payments and communication.
                    </p>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- CALL TO ACTION --}}
<section class="cta-section" id="contact">

    <div class="container text-center">

        <div class="cta-content">

            <span class="badge rounded-pill px-3 py-2 mb-3"
                  style="background:rgba(255,255,255,.12);color:#dbe5ff;font-size:10px">
                YOUR SCHOOL. YOUR SYSTEM. YOUR CONTROL.
            </span>

            <h2>
                Ready to Transform the Way<br>
                You Manage Your School?
            </h2>

            <p>
                Bring your school operations together with a platform
                designed to make administration easier and more organised.
            </p>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-white">
                    Create Your School Account
                    <i class="bi bi-arrow-right"></i>
                </a>
            @elseif (Route::has('login'))
                <a href="{{ route('login') }}" class="btn-white">
                    Access Your Account
                    <i class="bi bi-arrow-right"></i>
                </a>
            @endif

        </div>

    </div>

</section>


{{-- FOOTER --}}
<footer class="main-footer">

    <div class="container">

        <div class="row g-5">

            <div class="col-lg-4">

                <a class="brand-logo text-white" href="{{ url('/') }}">
                    <span class="brand-symbol">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span>SchoolFlow</span>
                </a>

                <p class="footer-description">
                    A modern school management platform built to
                    simplify administration, organise academic
                    activities and connect school communities.
                </p>

                <div class="mt-4">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>

            </div>


            <div class="col-6 col-lg-2">

                <h6 class="footer-heading">Platform</h6>

                <div class="footer-links">
                    <a href="#features">Features</a>
                    <a href="#modules">Modules</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#contact">Get Started</a>
                </div>

            </div>


            <div class="col-6 col-lg-2">

                <h6 class="footer-heading">School Portal</h6>

                <div class="footer-links">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}">Login</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register School</a>
                    @endif

                    <a href="#features">School Features</a>
                    <a href="#modules">Management Modules</a>
                </div>

            </div>


            <div class="col-lg-4">

                <h6 class="footer-heading">Built for Better School Management</h6>

                <p class="footer-description mt-0">
                    Bring administrators, teachers, parents and
                    students into one organised school environment.
                </p>

                <a href="#home" class="btn-primary-custom mt-3">
                    Back to Top <i class="bi bi-arrow-up"></i>
                </a>

            </div>

        </div>


        <div class="footer-bottom">

            <div class="row align-items-center">

                <div class="col-md-6 text-center text-md-start">
                    © {{ date('Y') }} {{ config('app.name', 'SchoolFlow') }}.
                    All rights reserved.
                </div>

                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    Designed for modern school management.
                </div>

            </div>

        </div>

    </div>

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
