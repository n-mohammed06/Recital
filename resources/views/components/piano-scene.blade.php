{{--
    Décor des pages de compte.

    :big="true"   en haut, une scène de théâtre prête pour un récital : rideaux de velours, lambrequin
                  à franges dorées, projecteur et piano à queue. Par-dessus, le grand titre « Récital »
                  et le slogan qui s'écrit puis s'efface (connexion, inscription).
                  sr-prefix : début du titre lu par les lecteurs d'écran (« Connexion à » Récital).
    sinon         petit logo puis le titre de la page (title).

    En bas de l'écran, un clavier aux couleurs de la charte.
    Les phrases du slogan se changent dans config/recital.php.
--}}
@props([
    'big' => false,
    'title' => null,
    'srPrefix' => null,
])

@php
    $slogans = config('recital.slogans', []);
@endphp

<div @class(['dg-scene', 'dg-scene--simple' => ! $big])>
    @if ($big)
        <div class="dg-scene__theatre">
            <svg class="dg-scene__decor" viewBox="0 0 390 360" preserveAspectRatio="xMidYMax slice" aria-hidden="true" focusable="false">
            <defs>
              <linearGradient id="dg-th-mur" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#0B0705"/><stop offset="0.7" stop-color="#170E09"/><stop offset="1" stop-color="#1C110B"/>
              </linearGradient>
              <linearGradient id="dg-th-faisceau" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#FFE7B8" stop-opacity="0.30"/><stop offset="1" stop-color="#FFE7B8" stop-opacity="0.07"/>
              </linearGradient>
              <radialGradient id="dg-th-flaque" cx="0.5" cy="0.5" r="0.5">
                <stop offset="0" stop-color="#FFD9A0" stop-opacity="0.42"/><stop offset="1" stop-color="#FFD9A0" stop-opacity="0"/>
              </radialGradient>
              <linearGradient id="dg-th-plancher" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#24150D"/><stop offset="1" stop-color="#3E2617"/>
              </linearGradient>
              <linearGradient id="dg-th-velours" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0" stop-color="#1E0A07"/><stop offset="0.18" stop-color="#46190F"/><stop offset="0.3" stop-color="#230B07"/>
                <stop offset="0.48" stop-color="#4F1E12"/><stop offset="0.6" stop-color="#270C08"/><stop offset="0.78" stop-color="#48190F"/>
                <stop offset="0.9" stop-color="#210A07"/><stop offset="1" stop-color="#3A150D"/>
              </linearGradient>
              <linearGradient id="dg-th-lambrequin" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#1C0806"/><stop offset="1" stop-color="#3D160E"/>
              </linearGradient>
              <radialGradient id="dg-th-rampe" cx="0.5" cy="0.5" r="0.5">
                <stop offset="0" stop-color="#FFE2B0" stop-opacity="0.9"/><stop offset="1" stop-color="#FFE2B0" stop-opacity="0"/>
              </radialGradient>
              <filter id="dg-th-flou" x="-20%" y="-5%" width="140%" height="110%"><feGaussianBlur stdDeviation="5"/></filter>
            </defs>
            <rect width="390" height="360" fill="url(#dg-th-mur)"/>
            <g stroke="#21140D" stroke-width="1" opacity="0.6"><line x1="100" y1="40" x2="100" y2="172"/><line x1="125" y1="40" x2="125" y2="172"/><line x1="150" y1="40" x2="150" y2="172"/><line x1="175" y1="40" x2="175" y2="172"/><line x1="200" y1="40" x2="200" y2="172"/><line x1="225" y1="40" x2="225" y2="172"/><line x1="250" y1="40" x2="250" y2="172"/><line x1="275" y1="40" x2="275" y2="172"/></g>
            <polygon points="58,172 332,172 390,224 0,224" fill="url(#dg-th-plancher)"/>
            <g stroke="#160C07" stroke-width="0.9" opacity="0.7"><line x1="32.1" y1="172" x2="-60" y2="224"/><line x1="48.7" y1="172" x2="-34" y2="224"/><line x1="65.3" y1="172" x2="-8" y2="224"/><line x1="81.9" y1="172" x2="18" y2="224"/><line x1="98.5" y1="172" x2="44" y2="224"/><line x1="115.1" y1="172" x2="70" y2="224"/><line x1="131.8" y1="172" x2="96" y2="224"/><line x1="148.4" y1="172" x2="122" y2="224"/><line x1="165.0" y1="172" x2="148" y2="224"/><line x1="181.6" y1="172" x2="174" y2="224"/><line x1="198.2" y1="172" x2="200" y2="224"/><line x1="214.8" y1="172" x2="226" y2="224"/><line x1="231.4" y1="172" x2="252" y2="224"/><line x1="248.0" y1="172" x2="278" y2="224"/><line x1="264.6" y1="172" x2="304" y2="224"/><line x1="281.2" y1="172" x2="330" y2="224"/><line x1="297.9" y1="172" x2="356" y2="224"/><line x1="314.5" y1="172" x2="382" y2="224"/><line x1="331.1" y1="172" x2="408" y2="224"/><line x1="347.7" y1="172" x2="434" y2="224"/></g>
            <polygon points="176,0 214,0 300,186 90,186" fill="url(#dg-th-faisceau)" filter="url(#dg-th-flou)"/>
            <ellipse class="dg-theatre__flaque" cx="195" cy="192" rx="128" ry="20" fill="url(#dg-th-flaque)"/>
            <rect x="0" y="224" width="390" height="3" fill="#6B4A2E"/>
            <rect x="0" y="227" width="390" height="16" fill="#0E0805"/>
            <circle cx="35" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="35" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="75" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="75" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="115" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="115" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="155" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="155" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="195" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="195" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="235" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="235" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="275" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="275" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="315" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="315" cy="225" r="1.6" fill="#FFE9C2"/>
            <circle cx="355" cy="225" r="9" fill="url(#dg-th-rampe)" opacity="0.45"/><circle cx="355" cy="225" r="1.6" fill="#FFE9C2"/>
            <ellipse cx="192" cy="200" rx="86" ry="5" fill="#000" opacity="0.55"/>
            <g class="dg-theatre__piano" transform="translate(0 -44)">
              <path d="M122 198 L150 128 Q158 121 168 126 L266 194 L258 198 Z" fill="#0C0806"/>
              <path d="M150 128 Q158 121 168 126 L266 194" fill="none" stroke="#9A7650" stroke-width="1.2" opacity="0.6"/>
              <line x1="206" y1="199" x2="201" y2="153" stroke="#0C0806" stroke-width="2.2"/>
              <path d="M128 196 L250 196 C264 196 274 202 274 210 C274 217 267 221 256 221 L128 221 Z" fill="#0C0806"/>
              <path d="M128 196 L250 196 C264 196 274 202 274 210" fill="none" stroke="#9A7650" stroke-width="1" opacity="0.55"/>
              <rect x="110" y="199" width="20" height="9" rx="1" fill="#0C0806"/>
              <line x1="111" y1="200.2" x2="129" y2="200.2" stroke="#E9DFCB" stroke-width="0.8" opacity="0.55"/>
              <path d="M132 196 L136 182 L152 182 L150 196" fill="#0C0806"/>
              <path d="M137 221 L141 221 L140 244 L138 244 Z M249 221 L253 221 L252 244 L250 244 Z" fill="#0C0806"/>
              <path d="M164 221 L166 238 M172 221 L170 238" stroke="#0C0806" stroke-width="1.6"/>
              <rect x="160" y="238" width="16" height="3" rx="1" fill="#0C0806"/>
              <rect x="84" y="213" width="24" height="5" rx="1.5" fill="#0C0806"/>
              <path d="M87 218 L86 244 M105 218 L106 244" stroke="#0C0806" stroke-width="2"/>
            </g>
            <g><path d="M0 0 L96 0 C92 76 56 134 36 164 C46 222 58 300 62 360 L0 360 Z" fill="url(#dg-th-velours)"/><path d="M10 0 C10 74 11 132 5 164 C7 222 8 300 8 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M15 0 C14 74 14 134 7 165 C9 224 12 302 12 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M28 0 C28 74 18 132 12 164 C14 222 20 300 20 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M33 0 C32 74 21 134 14 165 C16 224 24 302 24 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M46 0 C46 74 25 132 19 164 C21 222 32 300 32 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M51 0 C50 74 28 134 21 165 C23 224 36 302 36 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M64 0 C64 74 32 132 26 164 C28 222 44 300 44 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M69 0 C68 74 35 134 28 165 C30 224 48 302 48 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M82 0 C82 74 38 132 32 164 C34 222 56 300 56 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M87 0 C86 74 41 134 34 165 C36 224 60 302 60 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M0 158 Q20 170 40 162" fill="none" stroke="#D4AF37" stroke-width="3" stroke-linecap="round"/><path d="M40 162 L43 172 Q43 184 38 188 Q33 184 33 172 Z" fill="#B8962E"/></g>
            <g><path d="M390 0 L294 0 C298 76 334 134 354 164 C344 222 332 300 328 360 L390 360 Z" fill="url(#dg-th-velours)"/><path d="M380 0 C380 74 379 132 385 164 C383 222 382 300 382 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M375 0 C376 74 376 134 383 165 C381 224 378 302 378 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M362 0 C362 74 372 132 378 164 C376 222 370 300 370 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M357 0 C358 74 369 134 376 165 C374 224 366 302 366 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M344 0 C344 74 365 132 371 164 C369 222 358 300 358 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M339 0 C340 74 362 134 369 165 C367 224 354 302 354 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M326 0 C326 74 358 132 364 164 C362 222 346 300 346 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M321 0 C322 74 355 134 362 165 C360 224 342 302 342 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M308 0 C308 74 352 132 358 164 C356 222 334 300 334 360" fill="none" stroke="#160605" stroke-width="3" opacity="0.45" stroke-linecap="round"/><path d="M303 0 C304 74 349 134 356 165 C354 224 330 302 330 360" fill="none" stroke="#6E2C1D" stroke-width="1.4" opacity="0.32" stroke-linecap="round"/><path d="M390 158 Q370 170 350 162" fill="none" stroke="#D4AF37" stroke-width="3" stroke-linecap="round"/><path d="M350 162 L347 172 Q347 184 352 188 Q357 184 357 172 Z" fill="#B8962E"/></g>
            <path d="M0 0 L390 0 L390 18 Q351.0 44 312.0 18 Q273.0 44 234.0 18 Q195.0 44 156.0 18 Q117.0 44 78.0 18 Q39.0 44 0.0 18 Z" fill="url(#dg-th-lambrequin)"/>
            <path d="M390 18 Q351.0 44 312.0 18 Q273.0 44 234.0 18 Q195.0 44 156.0 18 Q117.0 44 78.0 18 Q39.0 44 0.0 18" fill="none" stroke="#D4AF37" stroke-width="1.8" opacity="0.9"/>
            <g stroke="#D4AF37" stroke-width="1" opacity="0.7"><line x1="6.5" y1="22.0" x2="6.5" y2="27.0"/><line x1="13.0" y1="25.2" x2="13.0" y2="30.2"/><line x1="19.5" y1="27.8" x2="19.5" y2="32.8"/><line x1="26.0" y1="29.6" x2="26.0" y2="34.6"/><line x1="32.5" y1="30.6" x2="32.5" y2="35.6"/><line x1="39.0" y1="31.0" x2="39.0" y2="36.0"/><line x1="45.5" y1="30.6" x2="45.5" y2="35.6"/><line x1="52.0" y1="29.6" x2="52.0" y2="34.6"/><line x1="58.5" y1="27.8" x2="58.5" y2="32.8"/><line x1="65.0" y1="25.2" x2="65.0" y2="30.2"/><line x1="71.5" y1="22.0" x2="71.5" y2="27.0"/><line x1="84.5" y1="22.0" x2="84.5" y2="27.0"/><line x1="91.0" y1="25.2" x2="91.0" y2="30.2"/><line x1="97.5" y1="27.8" x2="97.5" y2="32.8"/><line x1="104.0" y1="29.6" x2="104.0" y2="34.6"/><line x1="110.5" y1="30.6" x2="110.5" y2="35.6"/><line x1="117.0" y1="31.0" x2="117.0" y2="36.0"/><line x1="123.5" y1="30.6" x2="123.5" y2="35.6"/><line x1="130.0" y1="29.6" x2="130.0" y2="34.6"/><line x1="136.5" y1="27.8" x2="136.5" y2="32.8"/><line x1="143.0" y1="25.2" x2="143.0" y2="30.2"/><line x1="149.5" y1="22.0" x2="149.5" y2="27.0"/><line x1="162.5" y1="22.0" x2="162.5" y2="27.0"/><line x1="169.0" y1="25.2" x2="169.0" y2="30.2"/><line x1="175.5" y1="27.8" x2="175.5" y2="32.8"/><line x1="182.0" y1="29.6" x2="182.0" y2="34.6"/><line x1="188.5" y1="30.6" x2="188.5" y2="35.6"/><line x1="195.0" y1="31.0" x2="195.0" y2="36.0"/><line x1="201.5" y1="30.6" x2="201.5" y2="35.6"/><line x1="208.0" y1="29.6" x2="208.0" y2="34.6"/><line x1="214.5" y1="27.8" x2="214.5" y2="32.8"/><line x1="221.0" y1="25.2" x2="221.0" y2="30.2"/><line x1="227.5" y1="22.0" x2="227.5" y2="27.0"/><line x1="240.5" y1="22.0" x2="240.5" y2="27.0"/><line x1="247.0" y1="25.2" x2="247.0" y2="30.2"/><line x1="253.5" y1="27.8" x2="253.5" y2="32.8"/><line x1="260.0" y1="29.6" x2="260.0" y2="34.6"/><line x1="266.5" y1="30.6" x2="266.5" y2="35.6"/><line x1="273.0" y1="31.0" x2="273.0" y2="36.0"/><line x1="279.5" y1="30.6" x2="279.5" y2="35.6"/><line x1="286.0" y1="29.6" x2="286.0" y2="34.6"/><line x1="292.5" y1="27.8" x2="292.5" y2="32.8"/><line x1="299.0" y1="25.2" x2="299.0" y2="30.2"/><line x1="305.5" y1="22.0" x2="305.5" y2="27.0"/><line x1="318.5" y1="22.0" x2="318.5" y2="27.0"/><line x1="325.0" y1="25.2" x2="325.0" y2="30.2"/><line x1="331.5" y1="27.8" x2="331.5" y2="32.8"/><line x1="338.0" y1="29.6" x2="338.0" y2="34.6"/><line x1="344.5" y1="30.6" x2="344.5" y2="35.6"/><line x1="351.0" y1="31.0" x2="351.0" y2="36.0"/><line x1="357.5" y1="30.6" x2="357.5" y2="35.6"/><line x1="364.0" y1="29.6" x2="364.0" y2="34.6"/><line x1="370.5" y1="27.8" x2="370.5" y2="32.8"/><line x1="377.0" y1="25.2" x2="377.0" y2="30.2"/><line x1="383.5" y1="22.0" x2="383.5" y2="27.0"/></g>
            <rect width="390" height="6" fill="#1E0907"/><rect y="6" width="390" height="1.5" fill="#D4AF37" opacity="0.8"/>
            </svg>
            <header class="dg-scene__entete">
                <h1 class="dg-scene__titre">@if ($srPrefix)<span class="dg-visuellement-cache">{{ $srPrefix }} </span>@endif Récital</h1>
                @if (count($slogans) > 0)
                    <p class="dg-slogan">
                        <span class="dg-visuellement-cache">{{ $slogans[0] }}</span>
                        <span class="dg-slogan__texte" aria-hidden="true" data-typewriter="{{ json_encode($slogans, JSON_UNESCAPED_UNICODE) }}">{{ $slogans[0] }}</span><span class="dg-slogan__curseur" aria-hidden="true"></span>
                    </p>
                @endif
            </header>
        </div>
    @endif

    <div class="dg-scene__contenu">
        @unless ($big)
            <header class="dg-scene__entete">
                <a class="dg-logo-lien" href="{{ route('home') }}" aria-label="Récital, accueil"><x-logo /></a>
                @if ($title)
                    <h1 class="dg-titre-1">{{ $title }}</h1>
                @endif
            </header>
        @endunless

        {{ $slot }}
    </div>

    <svg class="dg-scene__clavier" viewBox="0 0 390 112" preserveAspectRatio="xMidYMin slice" aria-hidden="true" focusable="false">
        <rect width="390" height="112" fill="#5A4A70"/>
        <g fill="#F2EFF6"><rect x="0.00" y="-8" width="37.20" height="120" rx="6"/><rect x="39.20" y="-8" width="37.20" height="120" rx="6"/><rect x="78.40" y="-8" width="37.20" height="120" rx="6"/><rect x="117.60" y="-8" width="37.20" height="120" rx="6"/><rect x="156.80" y="-8" width="37.20" height="120" rx="6"/><rect x="196.00" y="-8" width="37.20" height="120" rx="6"/><rect x="235.20" y="-8" width="37.20" height="120" rx="6"/><rect x="274.40" y="-8" width="37.20" height="120" rx="6"/><rect x="313.60" y="-8" width="37.20" height="120" rx="6"/><rect x="352.80" y="-8" width="37.20" height="120" rx="6"/></g>
        <g fill="#0E0618"><rect x="27.04" y="-8" width="22.32" height="75.6" rx="4"/><rect x="66.24" y="-8" width="22.32" height="75.6" rx="4"/><rect x="144.64" y="-8" width="22.32" height="75.6" rx="4"/><rect x="183.84" y="-8" width="22.32" height="75.6" rx="4"/><rect x="223.04" y="-8" width="22.32" height="75.6" rx="4"/><rect x="301.44" y="-8" width="22.32" height="75.6" rx="4"/><rect x="340.64" y="-8" width="22.32" height="75.6" rx="4"/></g>
        <rect width="390" height="3" fill="#D4AF37"/>
    </svg>
</div>
