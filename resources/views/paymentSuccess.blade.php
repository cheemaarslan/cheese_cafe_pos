<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company['company_name'] }} — Payment Successful</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --red: #B4161D;
            --red-dark: #7A0A0F;
            --red-glow: rgba(26, 183, 89, 0.18);
            --red-ultra-light: rgba(26, 183, 89, 0.06);
            --gold: #F59E0B;
            --gold-light: #FEF3C7;
            --gold-dark: #78350F;
            --surface: #ffffff;
            --surface-2: #F8F9FA;
            --surface-3: #F1F3F5;
            --border: rgba(0, 0, 0, 0.07);
            --border-strong: rgba(0, 0, 0, 0.12);
            --text-primary: #0D0D0D;
            --text-secondary: #6B7280;
            --text-tertiary: #9CA3AF;
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04);
            --shadow-red: 0 8px 32px rgba(26, 183, 89, 0.22);
        }

        html,
        body {
            min-height: 100%;
            /* background: var(--surface-2);  */
            font-family: 'Sora', sans-serif;
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* ─── PAGE LAYOUT ─── */
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.25rem;
            position: relative;
            overflow: hidden;
        }

        /* subtle background grid
        .page::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        } */

        /* red radial at top */
        .page::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse at center, rgba(26, 183, 89, 0.10) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ─── LOGO ─── */
        .logo-link {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2.5rem;
            text-decoration: none;
        }

        .logo-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface);
            border: 1px solid var(--border-strong);
            border-radius: 100px;
            padding: 8px 18px 8px 10px;
            box-shadow: var(--shadow-sm);
        }

        .logo-img-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            overflow: hidden;
            background: var(--surface-3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-brand {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        /* ─── CHECK ANIMATION ─── */
        .check-wrap {
            position: relative;
            width: 96px;
            height: 96px;
            margin-bottom: 2rem;
        }

        .ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--red);
            opacity: 0;
            animation: ringPulse 2.4s ease-out infinite;
        }

        .ring:nth-child(2) {
            animation-delay: 0.8s;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(0.85);
                opacity: 0.5;
            }

            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }

        .check-circle {
            position: absolute;
            inset: 8px;
            border-radius: 50%;
            background: var(--red);
            box-shadow: var(--shadow-red);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: popIn 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .check-circle svg {
            width: 38px;
            height: 38px;
        }

        .check-path {
            stroke: white;
            stroke-width: 2.8;
            fill: none;
            stroke-dasharray: 55;
            stroke-dashoffset: 55;
            stroke-linecap: round;
            stroke-linejoin: round;
            animation: drawCheck 0.45s ease 0.45s forwards;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        /* ─── HEADLINE ─── */
        .headline-block {
            text-align: center;
            margin-bottom: 2rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 10px;
        }

        .eyebrow-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--red);
            animation: blink 2s ease infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .headline-block h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.8px;
            color: var(--text-primary);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .headline-block p {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        /* ─── MAIN CARD ─── */
        .main-card {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 1rem;
            animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.2s both;
        }

        @keyframes slideUp {
            0% {
                transform: translateY(16px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* TXN section */
        .txn-section {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .field-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-tertiary);
            margin-bottom: 6px;
        }

        .txn-id {
            font-family: 'DM Mono', monospace;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-primary);
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .copy-btn {
            cursor: pointer;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-secondary);
            font-family: 'Sora', sans-serif;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .copy-btn:hover {
            background: var(--surface-2);
            color: var(--text-primary);
        }

        /* Summary grid */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .summary-cell {
            padding: 16px 24px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .summary-cell:nth-child(2n) {
            border-right: none;
        }

        .summary-cell:nth-child(3),
        .summary-cell:nth-child(4) {
            border-bottom: none;
        }

        .cell-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .cell-value.status-ok {
            color: var(--red);
        }

        /* ─── LOYALTY CARD ─── */
        .loyalty-card {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: var(--shadow-md);
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.35s both;
        }

        .loyalty-card::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.10) 0%, transparent 70%);
            pointer-events: none;
        }

        .loyalty-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .coin-wrap {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gold-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .loyalty-text .label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #92400E;
            margin-bottom: 3px;
        }

        .loyalty-text .name {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .points-right {
            text-align: right;
            flex-shrink: 0;
        }

        .points-num {
            font-size: 42px;
            font-weight: 700;
            color: var(--red);
            letter-spacing: -2px;
            line-height: 1;
            animation: countUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.7s both;
        }

        @keyframes countUp {
            0% {
                opacity: 0;
                transform: translateY(8px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .points-suffix {
            font-size: 11px;
            color: var(--text-tertiary);
            font-weight: 400;
            margin-top: 2px;
        }

        /* ─── BUTTONS ─── */
        .btn-primary {
            width: 100%;
            padding: 16px;
            border-radius: 100px;
            background: var(--red);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -0.2px;
            text-align: center;
            text-decoration: none;
            display: block;
            border: none;
            cursor: pointer;
            font-family: 'Sora', sans-serif;
            box-shadow: 0 4px 16px rgba(26, 183, 89, 0.28);
            transition: opacity 0.15s, transform 0.1s, box-shadow 0.15s;
            margin-bottom: 10px;
            animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.5s both;
        }

        .btn-primary:hover {
            opacity: 0.92;
            box-shadow: 0 6px 24px rgba(26, 183, 89, 0.35);
        }

        .btn-primary:active {
            transform: scale(0.99);
        }

        .btn-ghost {
            width: 100%;
            padding: 15px;
            border-radius: 100px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            display: block;
            text-decoration: none;
            border: 1px solid var(--border-strong);
            cursor: pointer;
            font-family: 'Sora', sans-serif;
            transition: all 0.15s;
            animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.6s both;
        }

        .btn-ghost:hover {
            background: var(--surface-3);
            color: var(--text-primary);
        }

        /* ─── FOOTER ─── */
        .footer-note {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-tertiary);
            animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.7s both;
        }

        .lock-icon {
            width: 12px;
            height: 12px;
            opacity: 0.5;
        }

        /* ─── COPY TOAST ─── */
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(60px);
            background: var(--text-primary);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 100px;
            font-family: 'Sora', sans-serif;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
            opacity: 0;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 440px) {
            .headline-block h1 {
                font-size: 24px;
            }

            .points-num {
                font-size: 36px;
            }

            .txn-id {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="content">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="logo-link">
                <div class="logo-pill">
                    <div class="logo-img-wrap">
                        <img src="{{ $logo->logo }}" alt="{{ $company['company_name'] }}">
                    </div>
                    <span class="logo-brand">{{ $company['company_name'] }}</span>
                </div>
            </a>

            {{-- CHECK ANIMATION --}}
            <div class="check-wrap" id="check-wrap">
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="check-circle">
                    <span
                        style="
                        color:#F8F9FA; font-weight: 700; font-size: 24px;
                        ">&#10003;</span>
                </div>
            </div>

            {{-- HEADLINE --}}
            <div class="headline-block">
                <div class="eyebrow">
                    <div class="eyebrow-dot"></div>
                    {{ __('all.label.congratulations') }}
                </div>
                <h1>{{ __('all.message.payment_successful') }}</h1>
                <p>Your order has been confirmed and is being processed.</p>
            </div>

            {{-- LOYALTY POINTS --}}
            @if ($order?->loyaltyPoint && $order->loyaltyPoint->points > 0)
                @php $totalPoints = $order->loyaltyPoint->points; @endphp
                <div class="loyalty-card">
                    <div class="loyalty-left">
                        <div class="coin-wrap">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
                                <circle cx="13" cy="13" r="11" fill="#F59E0B" />
                                <circle cx="13" cy="13" r="8.5" fill="#FBBF24" />
                                <path
                                    d="M13 8v10M10 10.5c0-1.38 1.34-2.5 3-2.5s3 1.12 3 2.5c0 1.38-1.34 2.5-3 2.5s-3 1.12-3 2.5 1.34 2.5 3 2.5 3-1.12 3-2.5"
                                    stroke="#78350F" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="loyalty-text">
                            <div class="label">Rewards earned</div>
                            <div class="name">Loyalty Points</div>
                        </div>
                    </div>
                    <div class="points-right">
                        <div class="points-num">+{{ $totalPoints }}</div>
                        <div class="points-suffix">points</div>
                    </div>
                </div>
            @endif

            {{-- MAIN CARD --}}
            <div class="main-card">

                {{-- TXN ID --}}
                <div class="txn-section">
                    <div class="field-label">{{ __('all.label.transaction_id') }}</div>
                    <div class="txn-id">
                        <span id="txn-text">{{ $order?->transaction?->transaction_no ?? '—' }}</span>
                        @if ($order?->transaction?->transaction_no)
                            <button class="copy-btn" onclick="copyTxn()">Copy</button>
                        @endif
                    </div>
                </div>

                {{-- SUMMARY GRID --}}
                <div class="summary-grid">
                    <div class="summary-cell">
                        <div class="field-label">Date</div>
                        <div class="cell-value">{{ $order?->created_at?->format('M j, Y') ?? now()->format('M j, Y') }}
                        </div>
                    </div>
                    <div class="summary-cell">
                        <div class="field-label">Status</div>
                        <div class="cell-value status-ok">Confirmed</div>
                    </div>
                    <div class="summary-cell">
                        <div class="field-label">Method</div>
                        <div class="cell-value">{{ ucfirst($order?->payment_type ?? 'Online') }}</div>
                    </div>
                    <div class="summary-cell">
                        <div class="field-label">Total</div>
                        <div class="cell-value">${{ number_format($order?->total ?? 0, 2) }}</div>
                    </div>
                </div>

            </div>

            {{-- BUTTONS --}}
            <a id="home-route" href="{{ route('home') }}" class="btn-primary">
                {{ __('all.label.back_to_home') }}
            </a>

            <a href="#" id="print-btn" class="btn-ghost">
                Download receipt
            </a>

            {{-- SECURE BADGE --}}
            <div class="footer-note">
                <svg class="lock-icon" viewBox="0 0 12 12" fill="none">
                    <rect x="2" y="5" width="8" height="6" rx="1.5" stroke="currentColor"
                        stroke-width="1" />
                    <path d="M4 5V3.5a2 2 0 114 0V5" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                </svg>
                Secured by 256-bit SSL encryption
            </div>

        </div>
    </div>

    {{-- COPY TOAST --}}
    <div class="toast" id="toast">Copied to clipboard</div>

    <script>
        // Table order redirect (preserve original logic)
        (function() {
            var data = JSON.parse(localStorage.getItem('vuex') || 'null');
            var url = '<?= URL::to('/') . '/table-order/' ?>';
            var order_id = '<?= $order?->id ?>';
            if (data && data.tableCart && data.tableCart.paymentMethod) {
                document.getElementById('home-route').setAttribute(
                    'href',
                    url + data.tableCart.table.slug + '/' + order_id
                );
            }
        })();

        // Copy TXN ID
        function copyTxn() {
            var text = document.getElementById('txn-text').textContent.trim();
            if (!text || text === '—') return;
            navigator.clipboard.writeText(text).then(showToast).catch(function() {
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToast();
            });
        }

        function showToast() {
            var t = document.getElementById('toast');
            t.classList.add('show');
            setTimeout(function() {
                t.classList.remove('show');
            }, 2200);
        }

        //Print receipt
        document.getElementById('print-btn').addEventListener('click', function(e) {
            e.preventDefault();
            //hide back home and print btn while printing
            document.getElementById('home-route').style.display = 'none';
            this.style.display = 'none';
            
            window.print();

            //show buttons again after print
            document.getElementById('home-route').style.display = 'block';
            this.style.display = 'block';
            
            return false;
        });
    </script>

</body>

</html>
