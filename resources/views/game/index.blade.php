<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Grid Dominion — Energy Command</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/scss/game.scss')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7"></script>
</head>
<body>
<div id="game" data-game-id="{{ $game->id }}">
    <header class="topbar">
        <div class="brand"><div class="brand-bolt">ϟ</div><div><b>GRID</b><span>DOMINION</span><small>ENERGY COMMAND</small></div></div>
        <div class="resources">
            <div class="resource money"><i>€</i><span><small>LIQUID CAPITAL</small><strong id="cash">€10,000,000</strong></span></div>
            <div class="resource"><i>⌁</i><span><small>DEMAND</small><strong id="demandTop">18 MW</strong></span></div>
            <div class="resource"><i>ϟ</i><span><small>PRODUCTION</small><strong id="productionTop">20 MW</strong></span></div>
            <div class="resource reserve"><i>△</i><span><small>RESERVE</small><strong id="reserveTop">+2 MW</strong></span></div>
            <div class="resource"><i>▣</i><span><small>BATTERY</small><strong id="batteryTop">6 / 10 MWh</strong></span></div>
            <div class="resource"><i>♟</i><span><small>POPULATION</small><strong id="population">25,000</strong></span></div>
            <div class="resource"><i>€</i><span><small>ENERGY PRICE</small><strong id="price">€91 / MWh</strong></span></div>
        </div>
        <div class="clock"><div><small id="gameDate">MAY 2028</small><strong id="gameDateFull">MON, 01 MAY · 14:00</strong></div><div class="speed" aria-label="Game speed"><button data-speed="0">Ⅱ</button><button data-speed="1" class="active">1×</button><button data-speed="2">2×</button><button data-speed="4">4×</button></div></div>
    </header>

    <main class="command-layout">
        <aside class="left-rail">
            <section class="panel grid-panel"><div class="panel-title"><span>CITY GRID</span><small>LIVE LOAD BALANCE</small></div>
                <div class="grid-status stable" id="gridStatus"><span class="status-dot"></span><b>GRID STABLE</b><small>ALL SYSTEMS NOMINAL</small></div>
                <div class="meter-row"><span>DEMAND <b id="gridDemand">18.0 MW</b></span><div class="meter"><i id="demandBar"></i></div></div>
                <div class="meter-row production"><span>PRODUCTION <b id="gridProduction">20.0 MW</b></span><div class="meter"><i id="productionBar"></i></div></div>
                <div class="reserve-readout"><span>AVAILABLE RESERVE</span><strong id="gridReserve">+2.0 MW</strong></div>
                <div class="trade-line"><span>↘ IMPORT <b id="imports">0 MW</b></span><span>↗ EXPORT <b id="exports">2 MW</b></span></div>
            </section>
            <section class="panel weather-panel"><div class="panel-title"><span>WEATHER SYSTEM</span><small>LIVE FORECAST</small></div><div class="weather-main"><div class="weather-icon" id="weatherIcon">☀</div><div><strong id="condition">SUNNY</strong><b id="temperature">27°C</b></div></div><div class="weather-stats"><span>☀ SOLAR <b id="solarEfficiency">92%</b></span><span>〰 WIND <b id="windSpeed">18 km/h</b></span></div></section>
            <section class="panel finance-panel"><div class="panel-title"><span>TODAY'S FINANCIALS</span><small>OPERATIONS LEDGER</small></div><div class="finance-row"><span>REVENUE</span><b id="revenue">€0</b></div><div class="finance-row"><span>EXPENSES</span><b id="expenses">€0</b></div><div class="finance-row profit"><span>NET PROFIT</span><b id="profit">+€0</b></div></section>
        </aside>

        <section class="map-shell">
            <div class="map-toolbar"><span><b>AURORA VALLEY</b> · TRANSMISSION REGION 01</span><div><button title="Grid overlay" class="active">ϟ GRID</button><button title="Terrain view">◇ TERRAIN</button><button id="openAnalytics">⌁ ANALYTICS</button></div></div>
            <div class="map" id="cityMap">
                <svg class="terrain" viewBox="0 0 1200 760" preserveAspectRatio="none" aria-label="Aurora Valley energy map">
                    <defs>
                        <linearGradient id="land" x2="0" y2="1"><stop stop-color="#64806b"/><stop offset="1" stop-color="#263e36"/></linearGradient>
                        <linearGradient id="river" x2="1" y2=".5"><stop stop-color="#3a7488"/><stop offset=".5" stop-color="#5c9cad"/><stop offset="1" stop-color="#315f76"/></linearGradient>
                        <filter id="shadow"><feDropShadow dx="0" dy="7" stdDeviation="5" flood-opacity=".35"/></filter>
                        <pattern id="fields" width="38" height="18" patternUnits="userSpaceOnUse" patternTransform="rotate(-18)"><rect width="38" height="18" fill="#6e7c53"/><path d="M0 2h38M0 9h38M0 16h38" stroke="#82905e" stroke-width="2" opacity=".45"/></pattern>
                    </defs>
                    <rect width="1200" height="760" fill="url(#land)"/>
                    <path d="M0 80 Q120 20 250 96T510 55T770 120T1040 50L1200 80V0H0Z" fill="#1d3530" opacity=".95"/>
                    <path d="M0 145 Q130 75 270 142T550 96T820 164T1200 110V0H0Z" fill="#39564a" opacity=".8"/>
                    <path d="M380 -20 C335 120 470 190 425 308 S315 475 415 560 S620 655 560 790" fill="none" stroke="#233f4c" stroke-width="86"/>
                    <path d="M380 -20 C335 120 470 190 425 308 S315 475 415 560 S620 655 560 790" fill="none" stroke="url(#river)" stroke-width="69"/>
                    <path d="M382 0 C340 120 472 190 428 308 S320 475 417 560 S620 655 563 760" fill="none" stroke="#b4d6d8" stroke-width="3" opacity=".35" stroke-dasharray="19 31" class="river-flow"/>
                    <path d="M720 80L1200 15V300L670 340Z" fill="url(#fields)" opacity=".82"/>
                    <path d="M0 350L310 270L370 580L0 650Z" fill="#365841"/>
                    <g class="trees" fill="#193f32"><path d="M80 390l18-38 18 38h-10l15 30H75l15-30z"/><path d="M160 340l18-38 18 38h-10l15 30h-46l15-30z"/><path d="M240 410l18-38 18 38h-10l15 30h-46l15-30z"/><path d="M105 520l18-38 18 38h-10l15 30h-46l15-30z"/><path d="M285 500l18-38 18 38h-10l15 30h-46l15-30z"/></g>
                    <g class="roads" fill="none" stroke="#8d8b77" stroke-width="12" opacity=".8"><path d="M20 690Q360 580 680 610T1190 470"/><path d="M660 610Q775 430 1110 315"/><path d="M680 610Q870 730 1160 690"/></g>
                    <g class="roads" fill="none" stroke="#c4baa0" stroke-width="2" stroke-dasharray="12 14"><path d="M20 690Q360 580 680 610T1190 470"/><path d="M660 610Q775 430 1110 315"/><path d="M680 610Q870 730 1160 690"/></g>
                    <g class="city" filter="url(#shadow)">
                        <g fill="#223541" stroke="#9a8356" stroke-width="2"><path d="M690 450h54v95h-54z"/><path d="M751 420h50v125h-50z"/><path d="M810 465h67v80h-67z"/><path d="M885 400h48v145h-48z"/><path d="M941 444h72v101h-72z"/><path d="M1023 468h48v77h-48z"/></g>
                        <g fill="#ddad55" class="windows"><path d="M705 470h8v11h-8zm17 0h8v11h-8zm0 26h8v11h-8zm45-54h8v12h-8zm18 0h8v12h-8zm0 28h8v12h-8zm42 16h9v12h-9zm22 0h9v12h-9zm50-62h8v12h-8zm18 0h8v12h-8zm40 43h10v12h-10zm24 0h10v12h-10z"/></g>
                        <text x="850" y="580" text-anchor="middle" fill="#d8c89e" font-size="21" font-weight="800" letter-spacing="5">AURORA CITY</text>
                    </g>
                    <g class="grid-lines" fill="none" stroke="#73d5ef" stroke-width="3"><path d="M220 140L665 440L850 485"/><path d="M905 235L665 440"/><path d="M965 610L850 485"/><path d="M590 310L665 440"/></g>
                    <g class="pylons" stroke="#bec8bd" fill="none" stroke-width="3"><path d="M665 420l-18 50h36zM655 442h20M650 455h30"/><path d="M850 465l-16 42h32zM842 485h16"/></g>
                </svg>
                <div id="mapObjects"></div>
                <div class="map-vignette"></div><div class="map-legend"><span><i class="legend-build"></i> AVAILABLE SITE</span><span><i class="legend-plant"></i> ACTIVE FACILITY</span><span><i class="legend-grid"></i> ENERGIZED LINE</span></div>
            </div>
        </section>

        <aside class="right-rail">
            <section class="panel mix-panel"><div class="panel-title"><span>ENERGY MIX</span><small>GENERATION PORTFOLIO</small></div><div id="mixRows"></div><div class="mix-summary"><div><small>RENEWABLE</small><b id="renewable">0%</b></div><div><small>CO₂ INTENSITY</small><b id="co2">0 <em>g/kWh</em></b></div></div></section>
            <section class="panel objectives"><div class="panel-title"><span>ENERGY TRANSITION 2035</span><small>CAMPAIGN OBJECTIVE</small></div><div class="objective" data-key="population"><span>POPULATION <b>50,000+</b></span><div><i></i></div><small id="objPopulation">25,000 / 50,000</small></div><div class="objective" data-key="reliability"><span>GRID RELIABILITY <b>98%+</b></span><div><i></i></div><small id="objReliability">99.7%</small></div><div class="objective" data-key="renewable"><span>RENEWABLE ENERGY <b>50%+</b></span><div><i></i></div><small id="objRenewable">0%</small></div><div class="objective" data-key="cash"><span>LIQUID CAPITAL <b>€20M+</b></span><div><i></i></div><small id="objCash">€10M / €20M</small></div></section>
            <section class="panel news"><div class="panel-title"><span>CITY WIRE</span><small>INTELLIGENCE & ALERTS</small></div><div id="events"></div></section>
        </aside>
    </main>

    <footer class="status-footer"><div><span class="online-dot"></span> SIMULATION ONLINE</div><div>BLACKOUT HOURS <b id="blackouts">0</b></div><div>RELIABILITY <b id="footerReliability">99.7%</b></div><div>CITIZEN SATISFACTION <b id="satisfaction">86%</b></div><div>INVESTOR REPUTATION <b id="reputation">74%</b></div><button id="audioToggle" title="Audio system reserved for future effects">♫ AUDIO READY</button></footer>

    <div class="drawer" id="buildDrawer"><button class="drawer-close" aria-label="Close">×</button><div class="drawer-head"><small>DEVELOPMENT PERMIT</small><h2 id="siteName">BUILD ENERGY FACILITY</h2><p id="siteZone">SELECT A TECHNOLOGY FOR THIS SITE</p></div><div id="buildOptions"></div></div>
    <div class="modal-backdrop" id="analyticsModal"><section class="analytics"><button class="drawer-close">×</button><div class="panel-title"><span>GRID INTELLIGENCE</span><small>HISTORICAL OPERATIONS</small></div><nav><button class="active" data-chart="energy">LAST 24 HOURS</button><button data-chart="finance">FINANCIALS</button></nav><div class="chart-wrap"><canvas id="analyticsChart"></canvas></div></section></div>
    <div class="toast" id="toast"></div>
</div>
<script>window.GRID_INITIAL_STATE=@json($state);</script><script src="{{ asset('js/game.js') }}"></script>
</body></html>
