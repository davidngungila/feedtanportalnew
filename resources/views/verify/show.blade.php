<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uthibitisho wa Malipo — FeedTan CMG</title>
    <meta name="description" content="Thibitisha taarifa zako za malipo ya FeedTan Community Microfinance Group na uchague hatua inayofuata kwa salio lako.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{
            --sand-50:#FBF7EF; --sand-100:#F4ECDC; --sand-200:#E9DCC0;
            --coffee-900:#2A1B10; --coffee-800:#3B2718; --coffee-700:#4D3422; --coffee-500:#7A5C42; --coffee-300:#A98968;
            --terracotta-600:#C2592B; --terracotta-500:#D06B3A; --terracotta-100:#F6E1D3;
            --acacia-600:#5E6E3F; --acacia-500:#7A8450; --acacia-100:#E2E7D4;
            --gold-500:#D4A24C; --gold-100:#F7E9CB;
            --ink:#241408; --ink-soft:#6B5A48; --line:#E4D7C2; --white:#FFFFFF; --danger:#B33A3A; --danger-100:#F6DCDA;
            --radius-sm:8px; --radius-md:14px; --radius-lg:20px;
            --shadow-sm:0 1px 2px rgba(42,27,16,.08); --shadow-md:0 8px 24px rgba(42,27,16,.10); --shadow-lg:0 20px 48px rgba(42,27,16,.18);
        }
        *{box-sizing:border-box}
        html,body{min-height:100%}
        body{margin:0;font-family:'Raleway',sans-serif;background:var(--sand-50);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;overflow-x:clip}
        img,svg{max-width:100%}
        .view-wrap,.public-grid,.settings-panel,.topbar{min-width:0}
        ::selection{background:var(--terracotta-100);color:var(--coffee-900)}
        h1,h2,h3{font-family:'Raleway',sans-serif;color:var(--coffee-900);letter-spacing:-.01em}
        a{color:inherit;text-decoration:none}
        button{font-family:inherit;cursor:pointer}
        input,select,textarea{font-family:inherit}
        .mesh-bg{background-color:var(--sand-50);background-image:radial-gradient(at 0% 0%, rgba(194,89,43,.08) 0, transparent 50%),radial-gradient(at 100% 0%, rgba(212,162,76,.10) 0, transparent 45%),radial-gradient(at 50% 100%, rgba(94,110,63,.06) 0, transparent 50%)}
        .topbar{position:sticky;top:0;z-index:100;height:72px;background:rgba(251,247,239,.86);backdrop-filter:blur(10px);border-bottom:1px solid var(--line);display:flex;align-items:center;gap:16px;padding:0 28px}
        .sb-mark{width:38px;height:38px;border-radius:10px;flex:none;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-sm);color:#fff}
        .sb-mark i{font-size:15px}
        .tb-live{display:flex;align-items:center;gap:7px;background:var(--acacia-100);color:var(--acacia-600);padding:7px 13px;border-radius:20px;font-size:12.5px;font-weight:700}
        .tb-live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--acacia-600);box-shadow:0 0 0 0 rgba(94,110,63,.5);animation:pulse 2s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(94,110,63,.45)}70%{box-shadow:0 0 0 7px rgba(94,110,63,0)}100%{box-shadow:0 0 0 0 rgba(94,110,63,0)}}
        .view-wrap{padding:28px;flex:1}
        .view-head{margin-bottom:18px;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap}
        .view-head h2{font-size:24px;margin:0}
        .view-head .sub{font-size:13.5px;color:var(--ink-soft);margin-top:4px}
        .view-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
        .public-grid{display:grid;grid-template-columns:1.55fr 1fr;gap:18px;align-items:start}
        .hero-info{background:var(--coffee-900);background-image:radial-gradient(circle at 0% 0%, rgba(212,162,76,.10), transparent 55%);color:#fff;border:1px solid rgba(255,255,255,.06);border-radius:var(--radius-md);padding:26px;position:relative;overflow:hidden;box-shadow:var(--shadow-sm)}
        .hero-info::after{content:"";position:absolute;right:-20px;top:-20px;width:90px;height:90px;border-radius:50%;background:rgba(212,162,76,.12)}
        .hero-kicker{color:var(--gold-500);font-size:10.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;gap:8px}
        .hero-kicker::before{content:"";width:22px;height:3px;border-radius:2px;background:var(--gold-500);flex:none}
        .settings-panel{background:var(--white);border:1px solid var(--line);border-radius:var(--radius-md);box-shadow:var(--shadow-sm);padding:26px;overflow:hidden}
        .field{margin-bottom:16px;min-width:0}
        .pair-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;min-width:0}
        .pair-row .field{margin-bottom:0;min-width:0}
        .field label{display:block;font-size:12.5px;font-weight:600;color:var(--coffee-700);margin-bottom:7px;text-transform:uppercase;letter-spacing:.04em}
        .field input,.field select,.field textarea{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:var(--radius-sm);background:var(--white);font-size:14.5px;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        .field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--terracotta-500);box-shadow:0 0 0 3px var(--terracotta-100)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:var(--radius-sm);border:none;font-weight:600;font-size:14.5px;transition:transform .12s,box-shadow .12s,background .15s;text-decoration:none}
        .btn:active{transform:translateY(1px)}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32)}
        .btn-primary:hover{background:var(--terracotta-500)}
        .btn:disabled{opacity:.7;cursor:not-allowed}
        .amount-card{background:var(--white);border:1px solid var(--line);border-radius:var(--radius-md);padding:16px 18px;box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
        .amount-card .ac-label{font-size:11.5px;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-soft);font-weight:700}
        .amount-card .ac-value{font-size:22px;font-weight:700;color:var(--coffee-900);margin-top:2px;overflow-wrap:anywhere}
        .rows{background:var(--white);border:1px solid var(--line);border-radius:var(--radius-sm);overflow:hidden}
        .row{display:flex;justify-content:space-between;gap:12px;padding:10px 14px;border-bottom:1px dashed var(--line);font-size:14px}
        .row:last-child{border-bottom:none}
        .row span{color:var(--ink-soft);flex:none;max-width:45%}
        .row b{color:var(--coffee-900);text-align:right;min-width:0;overflow-wrap:anywhere}
        .row.total{background:var(--sand-100)}
        .row.total b{font-size:17px;color:var(--terracotta-600)}
        .ok{background:var(--acacia-100);color:var(--acacia-600);border:1px solid var(--acacia-600);border-radius:10px;padding:12px 14px;font-size:13.5px;font-weight:600;margin-bottom:16px;text-align:center}
        .error{background:var(--danger-100);color:var(--danger);border:1px solid var(--danger);border-radius:10px;padding:12px 14px;font-size:13px;font-weight:600;margin-bottom:16px}
        .done{text-align:center;padding:6px 0}
        .done .big{font-size:20px;font-weight:700;color:var(--acacia-600);margin-bottom:6px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-up{animation:fadeUp .35s ease}
        @media (max-width:1180px){.public-grid{grid-template-columns:1fr}}
        @media (max-width:900px){.topbar{padding:0 14px}}
        /* Compact mobile — hero hidden, amounts + form stay visible and scroll normally */
        @media (max-width:640px){
            html,body{height:auto;min-height:100%;overflow-x:hidden;overflow-x:clip;overflow-y:auto}
            .mesh-bg{min-height:100dvh;height:auto;overflow:visible}
            .min-h-screen{min-height:100dvh;height:auto;overflow:visible}
            .view-wrap{padding:10px 12px 16px;overflow-x:clip}
            .view-wrap > div{max-width:100% !important;min-width:0}
            .topbar{height:auto;min-height:50px;padding:6px 12px;gap:8px;flex-wrap:wrap;row-gap:6px}
            .topbar > div:nth-child(2){min-width:0;flex:1}
            .topbar strong{font-size:13px !important;overflow-wrap:anywhere}
            .topbar span[style*="font-size:11px"]{font-size:9px !important}
            .sb-mark{width:32px;height:32px;border-radius:8px}
            .sb-mark i{font-size:13px}
            .topbar strong{font-size:13px !important}
            .tb-live{padding:4px 8px;font-size:10px;gap:4px}
            .view-head{margin-bottom:8px !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;text-align:center !important}
            .view-head h2{font-size:16px !important}
            .view-head .sub{font-size:10.5px !important}
            .view-head .view-actions{justify-content:center !important;width:100%}
            .panel-heading{text-align:center !important}
            .panel-heading h3{font-size:14px !important}
            .public-grid{display:flex;flex-direction:column;gap:8px}
            .hero-info,.hide-mobile{display:none !important}
            .settings-panel{padding:10px 12px !important;border-radius:10px}
            .field{margin-bottom:8px !important;min-width:0}
            .pair-row{grid-template-columns:1fr 1fr;gap:8px}
            @media (max-width:480px){.pair-row{grid-template-columns:1fr}}
            .field label{font-size:10px !important;margin-bottom:3px !important}
            .field input,.field select,.field textarea{padding:8px 10px !important;font-size:12.5px !important;border-radius:6px}
            .amount-card{padding:8px 10px !important;border-radius:8px;gap:8px !important}
            .amount-card .ac-label{font-size:9px !important}
            .amount-card .ac-value{font-size:15px !important}
            .row{padding:7px 10px !important;font-size:12px !important}
            .row.total b{font-size:14px !important}
            .btn{padding:9px 12px !important;font-size:12.5px !important;border-radius:6px}
            .ok,.error{font-size:12px !important;padding:10px 12px !important}
        }
    </style>
</head>
<body class="mesh-bg min-h-screen">
    <div class="min-h-screen flex flex-col">
        <header class="topbar">
            <div class="sb-mark"><i class="fa-solid fa-leaf"></i></div>
            <div style="line-height:1.2">
                <strong style="display:block;color:var(--coffee-900);font-size:15.5px">FeedTan CMG</strong>
                <span style="display:block;color:var(--ink-soft);font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:600">Uthibitisho wa Malipo · Mtandaoni</span>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:10px">
                <span class="tb-live"><span>Salama · Encrypted</span></span>
            </div>
        </header>

        <div class="view-wrap">
            <div style="max-width:1080px;margin:0 auto">
                <div class="view-head">
                    <div>
                        <h2>Uthibitisho wa Malipo</h2>
                        <p class="sub">Habari {{ $payout->member->name ?? 'mwanachama' }} — thibitisha taarifa zako (ref {{ $payout->verify_code }}).</p>
                    </div>
                </div>

                <div class="public-grid">
                    <!-- Info - desktop like hero -->
                    <div style="display:flex;flex-direction:column;gap:18px">
                        <div class="hero-info">
                            <div class="hero-kicker">FeedTan CMG · Uthibitisho</div>
                            <h1 style="font-size:26px;line-height:1.2;color:#fff;margin-bottom:10px">Thibitisha malipo yako<br><span style="color:var(--gold-500)">kwa hatua chache</span></h1>
                            <p style="font-size:13.5px;line-height:1.6;color:rgba(255,255,255,.78);margin-bottom:16px">Angalia taarifa zako, thibitisha kuwa ziko sahihi, kisha chagua tufanye nini na salio lako. Tutashughulikia malipo mara tu uthibitisho utakapopokelewa.</p>
                            <div style="display:flex;flex-direction:column;gap:10px;font-size:13.5px">
                                <div style="display:flex;gap:10px;align-items:center"><span style="width:32px;height:32px;border-radius:9px;background:rgba(255,255,255,.10);display:flex;align-items:center;justify-content:center;flex:none"><i class="fa-solid fa-eye" style="font-size:13px;color:var(--gold-500)"></i></span><span style="color:rgba(255,255,255,.9)">1. Angalia jina, simu na makato yako</span></div>
                                <div style="display:flex;gap:10px;align-items:center"><span style="width:32px;height:32px;border-radius:9px;background:rgba(255,255,255,.10);display:flex;align-items:center;justify-content:center;flex:none"><i class="fa-solid fa-circle-check" style="font-size:13px;color:var(--gold-500)"></i></span><span style="color:rgba(255,255,255,.9)">2. Thibitisha na uchague hatua inayofuata</span></div>
                                <div style="display:flex;gap:10px;align-items:center"><span style="width:32px;height:32px;border-radius:9px;background:rgba(255,255,255,.10);display:flex;align-items:center;justify-content:center;flex:none"><i class="fa-solid fa-wallet" style="font-size:13px;color:var(--gold-500)"></i></span><span style="color:rgba(255,255,255,.9)">3. Tunashughulikia malipo yako</span></div>
                            </div>
                        </div>
                        <div class="settings-panel hide-mobile" style="padding:16px 18px">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:var(--ink-soft);margin-bottom:10px">Kumbuka</div>
                            <div style="font-size:12.5px;color:var(--ink-soft);line-height:1.6">Kiungo hiki ni cha kwako peke yako — usishiriki na mtu mwingine. Ukiona kosa lolote, chagua "zinahitaji kusahihishwa" na ueleze.</div>
                            <div style="font-size:11px;color:var(--ink-soft);margin-top:10px;text-align:center">Let's Grow Together</div>
                        </div>
                    </div>

                    <!-- Amounts + form -->
                    <div class="settings-panel animate-fade-up panel-heading-box" style="padding:0;overflow:hidden">
                        <div class="panel-heading" style="padding:18px 22px;border-bottom:1px solid var(--line)">
                            <h3 style="font-size:16px;margin:0">Taarifa zako za malipo</h3>
                            <div style="font-size:12.5px;color:var(--ink-soft);margin-top:3px">Ref {{ $payout->verify_code }} · <span style="color:var(--terracotta-600);font-weight:700">{{ ucfirst($payout->status) }}</span></div>
                        </div>
                        <div style="padding:18px 22px;display:flex;flex-direction:column;gap:14px">
                            @if(session('status'))<div class="ok">{{ session('status') }}</div>@endif
                            @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif

                            <div class="amount-card">
                                <div>
                                    <div class="ac-label">Kiasi Halisi Kwako</div>
                                    <div class="ac-value">TZS {{ number_format($payout->net_cash, 0) }}</div>
                                </div>
                                <div style="width:38px;height:38px;border-radius:10px;background:var(--terracotta-100);color:var(--terracotta-600);display:flex;align-items:center;justify-content:center;flex:none">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                            </div>

                            <div class="rows">
                                <div class="row"><span>Jina</span><b>{{ $payout->member->name ?? '—' }}</b></div>
                                <div class="row"><span>Simu</span><b>{{ $payout->phone ?: '—' }}</b></div>
                                <div class="row"><span>Kiasi kilichokomaa</span><b>@money($payout->amount)</b></div>
                                @if($payout->loan_installment > 0)<div class="row"><span>Mkopo (kato)</span><b>− @money($payout->loan_installment)</b></div>@endif
                                @if($payout->swf_deduction > 0)<div class="row"><span>SWF (kato)</span><b>− @money($payout->swf_deduction)</b></div>@endif
                                @if($payout->fines_deduction > 0)<div class="row"><span>Faini (kato)</span><b>− @money($payout->fines_deduction)</b></div>@endif
                                @if($payout->tshirt_deduction > 0)<div class="row"><span>T-shirt (kato)</span><b>− @money($payout->tshirt_deduction)</b></div>@endif
                                @if($payout->capital_cmg > 0)<div class="row"><span>Mtaji FeedTan CMG (kato)</span><b>− @money($payout->capital_cmg)</b></div>@endif
                            </div>

                            @if($payout->status === 'paid')
                                <div class="done"><div class="big">Tayari Imelipwa ✓</div><p class="sub">Imelipwa {{ $payout->paid_at?->format('d M Y') }}. Wasiliana na ofisi kwa maswali.</p></div>
                                @if(count($payout->allocationRows()))
                                <div class="rows" style="margin-top:12px;">
                                    @foreach($payout->allocationRows() as [$label, $amt])<div class="row"><span>{{ $label }}</span><b>@money($amt)</b></div>@endforeach
                                </div>
                                @endif
                            @elseif($payout->status === 'verified')
                                <div class="done"><div class="big">Tayari Umethibitisha ✓</div><p class="sub">Uli thibitisha {{ $payout->verified_at?->format('d M Y') }}. Tutashughulikia malipo yako.</p></div>
                                @if(count($payout->allocationRows()))
                                <div class="rows" style="margin-top:12px;">
                                    @foreach($payout->allocationRows() as [$label, $amt])<div class="row"><span>{{ $label }}</span><b>@money($amt)</b></div>@endforeach
                                    <div class="row total"><span>Jumla</span><b>@money(collect($payout->allocationRows())->sum(1))</b></div>
                                </div>
                                @else
                                <div class="rows" style="margin-top:12px;"><div class="row"><span>Mgawanyo</span><b>Hakuna salio la kugawa</b></div></div>
                                @endif
                                @if($payout->decision_notes)<div class="rows" style="margin-top:12px;"><div class="row"><span>Maelezo yako</span><b>{{ $payout->decision_notes }}</b></div></div>@endif
                            @else
                            @if($payout->net_cash > 0)
                            <form method="POST" action="{{ route('verify.confirm', $payout) }}" id="verifyForm" style="display:flex;flex-direction:column;gap:14px" data-net="{{ $payout->net_cash }}">
                                @csrf
                                <div style="font-size:12.5px;font-weight:700;color:var(--coffee-700);text-transform:uppercase;letter-spacing:.04em;">Mgawanyo wa salio la @money($payout->net_cash)</div>
                                <div class="field" style="margin-bottom:0"><label for="alloc_cash">Pesa taslimu (kamili au sehemu)</label>
                                    <input type="number" name="alloc_cash" class="alloc" min="0" step="100" value="0">
                                </div>
                                <div class="field" style="margin-bottom:0"><label for="alloc_swf">Lipa SWF</label>
                                    <input type="number" name="alloc_swf" class="alloc" min="0" step="100" value="0">
                                </div>
                                <div class="field" style="margin-bottom:0"><label for="alloc_loan">Rejesho (lipa mkopo)</label>
                                    <input type="number" name="alloc_loan" class="alloc" min="0" step="100" value="0">
                                </div>
                                <div class="field" style="margin-bottom:0"><label for="alloc_shares">Hisa za duka</label>
                                    <input type="number" name="alloc_shares" class="alloc" min="0" step="100" value="0">
                                </div>
                                <div class="pair-row">
                                    <div class="field"><label for="alloc_reinvest">Wekeza tena</label>
                                        <input type="number" name="alloc_reinvest" id="alloc_reinvest" class="alloc" min="0" step="100" value="0">
                                    </div>
                                    <div class="field"><label for="reinvest_term">Muda wa kuwekeza tena</label>
                                        <select name="reinvest_term" id="reinvest_term"><option value="">—</option><option value="2">Miaka 2</option><option value="4">Miaka 4</option><option value="6">Miaka 6</option></select>
                                    </div>
                                </div>
                                <div class="pair-row">
                                    <div class="field"><label for="alloc_savings">Akiba</label>
                                        <input type="number" name="alloc_savings" id="alloc_savings" class="alloc" min="0" step="100" value="0">
                                    </div>
                                    <div class="field"><label for="savings_type">Aina ya akiba (RDA &gt; 100,000)</label>
                                        <select name="savings_type" id="savings_type"><option value="">—</option><option value="rda">RDA</option><option value="flex">Flex</option><option value="emergence">Emergence</option></select>
                                    </div>
                                </div>
                                <div class="amount-card" style="border-color:var(--terracotta-600);">
                                    <div>
                                        <div class="ac-label">Imebaki kugawa</div>
                                        <div class="ac-value" id="allocLeft">TZS {{ number_format($payout->net_cash, 0) }}</div>
                                    </div>
                                    <div style="width:38px;height:38px;border-radius:10px;background:var(--gold-100);color:#8a6418;display:flex;align-items:center;justify-content:center;flex:none">
                                        <i class="fa-solid fa-scale-balanced"></i>
                                    </div>
                                </div>
                                <div class="field" style="margin-bottom:0"><label for="decision_notes">Maelezo (hiari)</label>
                                    <textarea name="decision_notes" id="decision_notes" rows="3" placeholder="Eleza marekebisho au maelekezo"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="verifyBtn" style="width:100%;padding:14px;font-size:15px"><i class="fa-solid fa-lock" style="font-size:12px"></i><span>Thibitisha</span></button>
                            </form>
                            @else
                            <div class="rows"><div class="row"><span>Salio</span><b>Hakuna salio la kugawa — thibitisha kupokea taarifa</b></div></div>
                            <form method="POST" action="{{ route('verify.confirm', $payout) }}" id="verifyForm" style="display:flex;flex-direction:column;gap:14px" data-net="0">
                                @csrf
                                <div class="field" style="margin-bottom:0"><label for="decision_notes">Maelezo (hiari)</label>
                                    <textarea name="decision_notes" id="decision_notes" rows="3" placeholder="Maelezo yoyote"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="verifyBtn" style="width:100%;padding:14px;font-size:15px"><i class="fa-solid fa-lock" style="font-size:12px"></i><span>Thibitisha</span></button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div style="text-align:center;font-size:11px;color:var(--ink-soft);margin-top:12px;display:flex;align-items:center;justify-content:center;gap:6px"><i class="fa-solid fa-lock" style="color:var(--acacia-600)"></i> FeedTan CMG · Let's Grow Together</div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const f = document.getElementById('verifyForm');
        if (f) {
            const net = parseFloat(f.dataset.net) || 0;
            const leftEl = document.getElementById('allocLeft');
            const btn = document.getElementById('verifyBtn');
            const allocInputs = f.querySelectorAll('.alloc');
            if (!leftEl || allocInputs.length === 0) {
                btn.disabled = false;
            } else {
                const fmt = n => 'TZS ' + Math.round(n).toLocaleString('en-US');
                function recalc(){
                    let sum = 0;
                    allocInputs.forEach(i => { sum += parseFloat(i.value) || 0; });
                    const left = Math.round((net - sum) * 100) / 100;
                    leftEl.textContent = fmt(left);
                    leftEl.style.color = Math.abs(left) < 0.5 ? 'var(--acacia-600)' : 'var(--danger)';
                    btn.disabled = Math.abs(left) >= 0.5;
                }
                allocInputs.forEach(i => i.addEventListener('input', recalc));
                recalc();
            }
            f.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Inatuma…</span>';
            });
        }
    });
    </script>
</body>
</html>
