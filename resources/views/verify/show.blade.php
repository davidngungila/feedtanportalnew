<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($payout->kind ?? 'matured') === 'coupon' ? 'Uthibitisho wa Coupon' : 'Uthibitisho wa Malipo' }} — FeedTan CMG</title>
    <meta name="description" content="Thibitisha taarifa zako za malipo ya FeedTan Community Microfinance Group.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <style>
        :root{
            --sand-50:#FBF7EF; --sand-100:#F4ECDC; --sand-200:#E9DCC0;
            --coffee-900:#2A1B10; --coffee-800:#3B2718; --coffee-700:#4D3422; --coffee-500:#7A5C42; --coffee-300:#A98968;
            --terracotta-600:#C2592B; --terracotta-500:#D06B3A; --terracotta-100:#F6E1D3;
            --acacia-600:#5E6E3F; --acacia-500:#7A8450; --acacia-100:#E2E7D4;
            --gold-500:#D4A24C; --gold-100:#F7E9CB;
            --ink:#241408; --ink-soft:#6B5A48; --line:#E4D7C2; --white:#FFFFFF;
            --danger:#B33A3A; --danger-100:#F6DCDA;
            --r-sm:10px; --r-md:16px; --r-lg:24px;
            --shadow-sm:0 1px 2px rgba(42,27,16,.08); --shadow-md:0 10px 30px rgba(42,27,16,.12); --shadow-lg:0 24px 60px rgba(42,27,16,.22);
        }
        *{box-sizing:border-box}
        body{
            margin:0;font-family:'Raleway',sans-serif;color:var(--ink);
            background:
                radial-gradient(600px 300px at 85% -5%, rgba(212,162,76,.20), transparent 60%),
                radial-gradient(700px 340px at 5% 0%, rgba(194,89,43,.14), transparent 55%),
                radial-gradient(800px 500px at 50% 110%, rgba(94,110,63,.10), transparent 55%),
                var(--sand-50);
            -webkit-font-smoothing:antialiased;min-height:100vh;
        }
        .wrap{max-width:700px;margin:0 auto;padding:20px 16px 48px}
        /* ---------- header ---------- */
        .hero{
            background:linear-gradient(150deg,#1d1108 0%,var(--coffee-900) 45%,#5a2c14 100%);
            border-radius:var(--r-lg);color:#fff;padding:26px 24px 72px;position:relative;overflow:hidden;
            box-shadow:var(--shadow-lg);
        }
        .hero::before{content:"";position:absolute;width:280px;height:280px;border-radius:50%;right:-90px;top:-110px;background:radial-gradient(circle,rgba(212,162,76,.35),transparent 70%)}
        .hero::after{content:"";position:absolute;width:180px;height:180px;border-radius:50%;left:-60px;bottom:-90px;background:radial-gradient(circle,rgba(194,89,43,.30),transparent 70%)}
        .brand{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
        .mark{width:44px;height:44px;border-radius:13px;flex:none;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;box-shadow:0 6px 16px rgba(0,0,0,.3)}
        .brand strong{display:block;font-size:16px;letter-spacing:.01em}
        .brand span{display:block;font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;color:var(--gold-500);font-weight:700;margin-top:2px}
        .secure{margin-left:auto;display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.14);padding:7px 13px;border-radius:20px;font-size:11.5px;font-weight:700;color:#dff0d8;white-space:nowrap;position:relative;z-index:1}
        .secure .dot{width:7px;height:7px;border-radius:50%;background:#7bc47f;box-shadow:0 0 0 0 rgba(123,196,127,.6);animation:pulse 2s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(123,196,127,.55)}70%{box-shadow:0 0 0 7px rgba(123,196,127,0)}100%{box-shadow:0 0 0 0 rgba(123,196,127,0)}}
        .greet{display:flex;align-items:center;gap:15px;margin-top:22px;position:relative;z-index:1}
        .avatar{width:58px;height:58px;border-radius:50%;flex:none;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff;background:linear-gradient(155deg,var(--terracotta-500),var(--gold-500));border:2px solid rgba(255,255,255,.35)}
        .greet h1{margin:0;font-size:21px;line-height:1.25;color:#fff}
        .greet p{margin:5px 0 0;font-size:13px;color:rgba(255,255,255,.72)}
        .pills{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;position:relative;z-index:1}
        .pill{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:800;letter-spacing:.04em;padding:6px 13px;border-radius:20px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);color:#ffe9c4}
        .pill.status-pending{background:rgba(212,162,76,.22);border-color:rgba(212,162,76,.55);color:#ffe1a1}
        .pill.status-verified,.pill.status-paid{background:rgba(123,196,127,.18);border-color:rgba(123,196,127,.5);color:#c9eccb}
        /* ---------- amount hero ---------- */
        .amount-card{background:var(--white);border-radius:var(--r-lg);box-shadow:var(--shadow-md);border:1px solid var(--line);margin:-52px 12px 0;padding:24px 22px;text-align:center;position:relative;z-index:2}
        .amount-card .lbl{font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-soft)}
        .amount-card .val{font-size:30px;font-weight:800;color:var(--terracotta-600);margin:6px 0 2px;letter-spacing:-.02em;overflow-wrap:anywhere}
        .amount-card .val.gross{font-size:24px;color:var(--coffee-900)}
        .amount-card .deduct{font-size:20px;font-weight:800;color:var(--danger);margin:6px 0 2px;overflow-wrap:anywhere}
        .amount-row{display:flex;gap:14px;justify-content:space-between}
        .amount-col{flex:1;min-width:0}
        @media (max-width:520px){.amount-row{flex-direction:column;gap:10px}.amount-card .val{font-size:26px}.amount-card .val.gross{font-size:22px}}
        .amount-card .sub{font-size:12.5px;color:var(--ink-soft)}
        /* ---------- stepper ---------- */
        /* ---------- active stage only ---------- */
        .stage-now{display:flex;align-items:center;justify-content:center;gap:9px;margin:18px 4px 0;background:var(--white);border:1.5px solid var(--line);border-radius:20px;padding:10px 18px;font-size:13px;font-weight:800;color:var(--terracotta-600);box-shadow:var(--shadow-sm)}
        .stage-now.stage-verified,.stage-now.stage-paid{color:var(--acacia-600);border-color:var(--acacia-500)}
        .stage-now.stage-rejected{color:var(--danger);border-color:var(--danger)}
        /* ---------- cards ---------- */
        .card{background:var(--white);border:1px solid var(--line);border-radius:var(--r-md);box-shadow:var(--shadow-sm);margin-top:16px;overflow:hidden}
        .card-h{padding:16px 20px 13px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px}
        .card-h .ico{width:34px;height:34px;border-radius:10px;flex:none;display:flex;align-items:center;justify-content:center;background:var(--terracotta-100);color:var(--terracotta-600);font-size:15px}
        .card-h h3{margin:0;font-size:15.5px}
        .card-h p{margin:2px 0 0;font-size:12px;color:var(--ink-soft)}
        .card-b{padding:6px 20px 18px}
        .kv{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px dashed var(--line);font-size:14px}
        .kv:last-child{border-bottom:none}
        .kv .k-ico{width:32px;height:32px;border-radius:9px;flex:none;display:flex;align-items:center;justify-content:center;background:var(--sand-100);color:var(--coffee-500);font-size:13px}
        .kv .k{color:var(--ink-soft);flex:1}
        .kv .v{font-weight:700;color:var(--coffee-900);text-align:right;overflow-wrap:anywhere}
        .kv .v.neg{color:var(--danger)}
        .kv.total{background:var(--sand-100);margin:8px -20px -18px;padding:13px 20px;border-bottom:none}
        .kv.total .v{color:var(--terracotta-600);font-size:17px}
        /* ---------- form ---------- */
        .alloc-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px}
        .field label{display:block;font-size:11px;font-weight:800;color:var(--coffee-700);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em}
        .in-wrap{position:relative}
        .in-wrap > i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--coffee-300);font-size:13px;pointer-events:none}
        .field input,.field select,.field textarea{width:100%;padding:12px 13px 12px 36px;border:1.5px solid var(--line);border-radius:var(--r-sm);background:var(--white);font-size:14.5px;font-weight:600;color:var(--ink);font-family:inherit;transition:border-color .15s,box-shadow .15s}
        .field textarea{padding-left:13px;resize:vertical;min-height:74px;font-weight:500}
        .field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--terracotta-500);box-shadow:0 0 0 3px var(--terracotta-100)}
        .field.full{grid-column:1/-1}
        .chip-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
        .chip{border:1.5px solid var(--line);background:var(--sand-100);color:var(--coffee-700);font-weight:700;font-size:12.5px;padding:8px 14px;border-radius:20px;font-family:inherit}
        .chip:active{transform:scale(.97)}
        .remain{margin-top:14px;border-radius:var(--r-sm);padding:13px 16px;display:flex;align-items:center;gap:12px;background:var(--sand-100);border:1.5px dashed var(--coffee-300)}
        .remain.ok{background:var(--acacia-100);border-color:var(--acacia-500)}
        .remain.bad{background:var(--danger-100);border-color:var(--danger)}
        .remain .r-ico{width:36px;height:36px;border-radius:10px;flex:none;display:flex;align-items:center;justify-content:center;background:var(--white);color:var(--coffee-700)}
        .remain small{display:block;font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--ink-soft)}
        .remain b{font-size:18px}
        .remain.ok b{color:var(--acacia-600)} .remain.bad b{color:var(--danger)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;width:100%;padding:15px;border-radius:12px;border:none;font-weight:800;font-size:15.5px;font-family:inherit;background:linear-gradient(155deg,var(--terracotta-500),var(--terracotta-600));color:#fff;box-shadow:0 8px 20px rgba(194,89,43,.35);margin-top:14px}
        .btn:active{transform:translateY(1px)}
        .btn:disabled{opacity:.6;cursor:not-allowed;box-shadow:none}
        .btn-back{background:var(--white);color:var(--coffee-700);border:1.5px solid var(--line);box-shadow:none;margin-top:10px}
        .hidden-step{display:none}
        .mini-err{display:none;background:var(--danger-100);border:1.5px solid var(--danger);color:var(--danger);border-radius:var(--r-sm);padding:11px 14px;font-size:13px;font-weight:700;margin-top:12px}
        .wiz-back{background:none;border:none;color:var(--ink-soft);font-weight:700;font-size:13px;margin-top:12px;font-family:inherit}
        /* ---------- states ---------- */
        .ok-banner{background:var(--acacia-100);border:1.5px solid var(--acacia-500);color:var(--acacia-600);border-radius:var(--r-sm);padding:12px 15px;font-size:13.5px;font-weight:700;margin:14px 20px 0;text-align:center}
        .err-banner{background:var(--danger-100);border:1.5px solid var(--danger);color:var(--danger);border-radius:var(--r-sm);padding:12px 15px;font-size:13px;font-weight:700;margin:14px 20px 0;text-align:center}
        .done-hero{text-align:center;padding:26px 20px 8px}
        .done-hero .big-ico{width:66px;height:66px;border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:26px;background:var(--acacia-100);color:var(--acacia-600);border:2px solid var(--acacia-500)}
        .done-hero h3{margin:0;font-size:19px}
        .done-hero p{font-size:13px;color:var(--ink-soft);margin:6px 0 0}
        .note{display:flex;gap:10px;margin-top:16px;background:var(--white);border:1px solid var(--line);border-radius:var(--r-md);padding:14px 16px;font-size:12.5px;line-height:1.6;color:var(--ink-soft)}
        .note i{color:var(--gold-500);margin-top:2px}
        .foot{text-align:center;font-size:11px;color:var(--ink-soft);margin-top:20px;display:flex;align-items:center;justify-content:center;gap:6px}
        .foot i{color:var(--acacia-600)}
        /* ---------- success popup modal ---------- */
        .modal-bg{position:fixed;inset:0;z-index:500;background:rgba(36,20,8,.55);backdrop-filter:blur(3px);display:none;align-items:center;justify-content:center;padding:20px}
        .modal-bg.show{display:flex}
        .modal-box{background:var(--sand-50);border-radius:var(--r-lg);box-shadow:var(--shadow-lg);width:100%;max-width:400px;padding:30px 26px 24px;text-align:center;animation:popIn .3s cubic-bezier(.2,.8,.2,1)}
        @keyframes popIn{from{opacity:0;transform:scale(.94) translateY(12px)}to{opacity:1;transform:scale(1) translateY(0)}}
        .modal-box .big-ico{width:70px;height:70px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:30px;background:var(--acacia-100);color:var(--acacia-600);border:2px solid var(--acacia-500)}
        .modal-box h3{margin:0;font-size:19px}
        .modal-box p{font-size:13.5px;color:var(--coffee-700);line-height:1.6;margin:8px 0 0}
        .modal-box.err .big-ico{background:var(--danger-100);color:var(--danger);border-color:var(--danger)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .anim{animation:fadeUp .4s ease}
        @media (max-width:520px){
            .wrap{padding:12px 10px 40px}
            .hero{padding:20px 18px 66px}
            .greet h1{font-size:18px}
            .amount-card .val{font-size:31px}
            .alloc-grid{grid-template-columns:1fr}
            .step{font-size:10px}
        }
    </style>
</head>
<body>
<div class="wrap">

    <header class="hero">
        <div class="brand">
            <div class="mark"><i class="fa-solid fa-leaf"></i></div>
            <div><strong>FeedTan CMG</strong><span>Let's Grow Together</span></div>
            <div class="secure"><span class="dot"></span>Salama</div>
        </div>
        <div class="greet">
            <div class="avatar">{{ strtoupper(substr($payout->member->name ?? 'F', 0, 1)) }}</div>
            <div>
                <h1>Habari, {{ explode(' ', $payout->member->name ?? 'mwanachama')[0] }}</h1>
                <p>{{ ($payout->kind ?? 'matured') === 'coupon' ? 'Malipo ya Gawio' : 'Uthibitisho wa malipo ya uwekezaji' }} · Ref {{ $payout->verify_code }}</p>
            </div>
        </div>
        <div class="pills">
            <span class="pill status-{{ $payout->status }}"><i class="fa-solid fa-circle" style="font-size:7px"></i>{{ ucfirst($payout->status) }}</span>
        </div>
    </header>

    <div class="amount-card anim">
        @php $deductTotal = $payout->loan_installment + $payout->swf_deduction + $payout->fines_deduction + $payout->tshirt_deduction + $payout->capital_cmg; @endphp
        <div class="amount-row">
            <div class="amount-col"><div class="lbl">Gawio la jumla</div><div class="val gross">TZS {{ number_format($payout->amount, 0) }}</div></div>
            <div class="amount-col"><div class="lbl">Makato</div><div class="deduct">− TZS {{ number_format($deductTotal, 0) }}</div></div>
            <div class="amount-col"><div class="lbl">Kiasi halisi (baada ya makato)</div><div class="val">TZS {{ number_format($payout->net_cash, 0) }}</div></div>
        </div>
        <div class="sub">{{ $payout->member->name ?? '—' }}</div>
    </div>

    @php
        $stage = match($payout->status) {
            'paid' => ['Imekamilika — umeshalipwa', 'fa-circle-check', 'paid'],
            'verified' => ['Imethibitishwa — subiri malipo', 'fa-hourglass-half', 'verified'],
            'rejected' => ['Imekataliwa — tumepokea sababu yako', 'fa-circle-xmark', 'rejected'],
            default => ['Hatua ya sasa: Kuthibitisha', 'fa-pen-to-square', 'pending'],
        };
    @endphp
    <div class="stage-now stage-{{ $stage[2] }}"><i class="fa-solid {{ $stage[1] }}"></i><span>{{ $stage[0] }}</span></div>

    @if(session('status'))
    <div class="modal-bg show" id="successModal">
        <div class="modal-box">
            <div class="big-ico"><i class="fa-solid fa-check"></i></div>
            <h3>Hongera!</h3>
            <p>{{ session('status') }}</p>
            <button type="button" class="btn" id="successOkBtn" style="margin-top:18px"><span>Sawa</span></button>
        </div>
    </div>
    @endif
    @if($errors->any())<div class="err-banner anim">{{ $errors->first() }}</div>@endif

    @if($payout->status === 'pending')<div id="detailsStep">@endif
    <section class="card anim">
        <div class="card-h">
            <div class="ico"><i class="fa-solid fa-receipt"></i></div>
            <div><h3>Muhtasari wa malipo</h3><p>Jumla, makato na salio lako</p></div>
        </div>
        <div class="card-b">
            <div class="kv"><div class="k-ico"><i class="fa-solid fa-user"></i></div><div class="k">Jina</div><div class="v">{{ $payout->member->name ?? '—' }}</div></div>
            <div class="kv"><div class="k-ico"><i class="fa-solid fa-phone"></i></div><div class="k">Simu</div><div class="v">{{ $payout->phone ?: '—' }}</div></div>
            <div class="kv"><div class="k-ico"><i class="fa-solid fa-sack-dollar"></i></div><div class="k">Kiasi kilichokomaa</div><div class="v">@money($payout->amount)</div></div>
            @if($payout->loan_installment > 0)<div class="kv"><div class="k-ico"><i class="fa-solid fa-hand-holding-dollar"></i></div><div class="k">Mkopo (kato)</div><div class="v neg">− @money($payout->loan_installment)</div></div>@endif
            @if($payout->swf_deduction > 0)<div class="kv"><div class="k-ico"><i class="fa-solid fa-shield-heart"></i></div><div class="k">SWF (kato)</div><div class="v neg">− @money($payout->swf_deduction)</div></div>@endif
            @if($payout->fines_deduction > 0)<div class="kv"><div class="k-ico"><i class="fa-solid fa-gavel"></i></div><div class="k">Faini (kato)</div><div class="v neg">− @money($payout->fines_deduction)</div></div>@endif
            @if($payout->tshirt_deduction > 0)<div class="kv"><div class="k-ico"><i class="fa-solid fa-shirt"></i></div><div class="k">T-shirt (kato)</div><div class="v neg">− @money($payout->tshirt_deduction)</div></div>@endif
            @if($payout->capital_cmg > 0)<div class="kv"><div class="k-ico"><i class="fa-solid fa-building-columns"></i></div><div class="k">Mtaji FeedTan CMG (kato)</div><div class="v neg">− @money($payout->capital_cmg)</div></div>@endif
            <div class="kv total"><div class="k-ico"><i class="fa-solid fa-wallet"></i></div><div class="k"><b>Salio lako</b></div><div class="v">@money($payout->net_cash)</div></div>
        </div>
    </section>
    @if($payout->status === 'pending')
    <button type="button" class="btn" id="toBreakdownBtn" style="margin-top:16px"><span>Endelea na mchanganuo wa malipo</span><i class="fa-solid fa-arrow-right"></i></button>
    </div>
    @endif

    @if($payout->status === 'paid')
        <section class="card anim">
            <div class="done-hero">
                <div class="big-ico"><i class="fa-solid fa-check"></i></div>
                <h3>Tayari imelipwa</h3>
                <p>Imelipwa {{ $payout->paid_at?->format('d M Y') }}. Wasiliana na ofisi kwa maswali yoyote.</p>
            </div>
            <div class="card-b">
                @if(count($payout->allocationRows()))
                    @foreach($payout->allocationRows() as [$label, $amt])<div class="kv"><div class="k-ico"><i class="fa-solid fa-circle-check"></i></div><div class="k">{{ $label }}</div><div class="v">@money($amt)</div></div>@endforeach
                @endif
            </div>
        </section>
    @elseif($payout->status === 'verified')
        <section class="card anim">
            <div class="done-hero">
                <div class="big-ico"><i class="fa-solid fa-check"></i></div>
                <h3>Umeshathibitisha</h3>
                <p>Ulithibitisha {{ $payout->verified_at?->format('d M Y') }}. Tutashughulikia malipo yako hivi karibuni.</p>
            </div>
            <div class="card-b">
                @if(count($payout->allocationRows()))
                    @foreach($payout->allocationRows() as [$label, $amt])<div class="kv"><div class="k-ico"><i class="fa-solid fa-circle-check"></i></div><div class="k">{{ $label }}</div><div class="v">@money($amt)</div></div>@endforeach
                    <div class="kv total"><div class="k-ico"><i class="fa-solid fa-scale-balanced"></i></div><div class="k"><b>Jumla</b></div><div class="v">@money(collect($payout->allocationRows())->sum(1))</div></div>
                @else
                    <div class="kv"><div class="k-ico"><i class="fa-solid fa-circle-info"></i></div><div class="k">Mgawanyo</div><div class="v">Hakuna salio la kugawa</div></div>
                @endif
                @if($payout->decision_notes)<div class="kv"><div class="k-ico"><i class="fa-solid fa-note-sticky"></i></div><div class="k">Maelezo yako</div><div class="v">{{ $payout->decision_notes }}</div></div>@endif
            </div>
        </section>
    @elseif($payout->status === 'rejected')
        <section class="card anim">
            <div class="done-hero">
                <div class="big-ico" style="background:var(--danger-100);color:var(--danger);border-color:var(--danger)"><i class="fa-solid fa-xmark"></i></div>
                <h3>Ulikataa taarifa hizi</h3>
                <p>Asante kwa kutujulisha. Ofisi itazipitia na kukujulisha hatua inayofuata.</p>
            </div>
            <div class="card-b">
                @if($payout->decision_notes)<div class="kv"><div class="k-ico"><i class="fa-solid fa-note-sticky"></i></div><div class="k">Sababu yako</div><div class="v">{{ $payout->decision_notes }}</div></div>@endif
            </div>
        </section>
    @else
        <section class="card anim hidden-step" id="allocCard">
            <div class="card-b" style="padding-top:6px">
                <button type="button" class="btn btn-back" id="backToDetailsBtn" style="margin-top:0;margin-bottom:6px"><i class="fa-solid fa-arrow-left"></i><span>Rudi kwenye taarifa</span></button>
                @if($payout->net_cash > 0)
                <form method="POST" action="{{ route('verify.confirm', $payout) }}" id="verifyForm" data-net="{{ $payout->net_cash }}" novalidate>
                    @csrf
                    <div id="allocStep">
                        <div class="chip-row">
                            <button type="button" class="chip" id="fillCash"><i class="fa-solid fa-money-bill-wave"></i> Gawa salio lote</button>
                            <button type="button" class="chip" id="clearAll"><i class="fa-solid fa-eraser"></i> Futa</button>
                        </div>
                        <div class="alloc-grid">
                            <div class="field"><label>Pesa taslimu</label><div class="in-wrap"><i class="fa-solid fa-money-bill-wave"></i><input type="number" name="alloc_cash" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Lipa SWF</label><div class="in-wrap"><i class="fa-solid fa-shield-heart"></i><input type="number" name="alloc_swf" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Rejesho (mkopo)</label><div class="in-wrap"><i class="fa-solid fa-hand-holding-dollar"></i><input type="number" name="alloc_loan" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Hisa za duka</label><div class="in-wrap"><i class="fa-solid fa-store"></i><input type="number" name="alloc_shares" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Wekeza tena</label><div class="in-wrap"><i class="fa-solid fa-arrow-trend-up"></i><input type="number" name="alloc_reinvest" id="alloc_reinvest" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Muda wa kuwekeza</label><div class="in-wrap"><i class="fa-solid fa-calendar-days"></i><select name="reinvest_term" id="reinvest_term"><option value="">— Chagua —</option><option value="2">Miaka 2</option><option value="4">Miaka 4</option><option value="6">Miaka 6</option></select></div></div>
                            <div class="field"><label>Akiba</label><div class="in-wrap"><i class="fa-solid fa-piggy-bank"></i><input type="number" name="alloc_savings" id="alloc_savings" class="alloc" min="0" step="100" value="0"></div></div>
                            <div class="field"><label>Aina ya akiba</label><div class="in-wrap"><i class="fa-solid fa-layer-group"></i><select name="savings_type" id="savings_type"><option value="">— Chagua —</option><option value="rda">RDA (&gt; 100,000)</option><option value="flex">Flex</option><option value="emergence">Emergence</option></select></div></div>
                            <div class="field full"><label>Maelezo (hiari)</label><textarea name="decision_notes" id="decision_notes" rows="2" placeholder="Eleza marekebisho au maelekezo…"></textarea></div>
                        </div>
                        <div class="remain" id="remainBox">
                            <div class="r-ico"><i class="fa-solid fa-scale-balanced"></i></div>
                            <div><small>Imebaki kugawa</small><b id="allocLeft">TZS {{ number_format($payout->net_cash, 0) }}</b></div>
                        </div>
                        <button type="button" class="btn" id="nextBtn"><span>Endelea — Hakiki</span><i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                    <div id="previewStep" class="hidden-step">
                        <div style="font-size:11px;font-weight:800;color:var(--coffee-700);text-transform:uppercase;letter-spacing:.05em;margin:14px 0 4px;">Hakiki mgawanyo wako</div>
                        <div id="previewRows"></div>
                        <div class="kv total"><div class="k-ico"><i class="fa-solid fa-scale-balanced"></i></div><div class="k"><b>Jumla</b></div><div class="v" id="previewTotal"></div></div>
                        <div class="kv" id="previewNotesRow" style="display:none"><div class="k-ico"><i class="fa-solid fa-note-sticky"></i></div><div class="k">Maelezo</div><div class="v" id="previewNotes"></div></div>
                        <button type="submit" class="btn" id="verifyBtn"><i class="fa-solid fa-lock"></i><span>Thibitisha sasa</span></button>
                        <button type="button" class="btn btn-back" id="backBtn"><i class="fa-solid fa-arrow-left"></i><span>Rudi kurekebisha</span></button>
                    </div>
                </form>
                @else
                <form method="POST" action="{{ route('verify.confirm', $payout) }}" id="verifyForm" data-net="0" style="padding-top:14px">
                    @csrf
                    <div class="kv"><div class="k-ico"><i class="fa-solid fa-circle-info"></i></div><div class="k">Salio</div><div class="v">Hakuna salio la kugawa — thibitisha kupokea taarifa</div></div>
                    <div class="field" style="margin-top:12px"><label>Maelezo (hiari)</label><textarea name="decision_notes" id="decision_notes" rows="2" placeholder="Maelezo yoyote…"></textarea></div>
                    <button type="submit" class="btn" id="verifyBtn"><i class="fa-solid fa-lock"></i><span>Thibitisha</span></button>
                </form>
                @endif
            </div>
        </section>
    @endif

    <div class="modal-bg" id="errModal">
        <div class="modal-box err">
            <div class="big-ico"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3>Pole!</h3>
            <p id="errModalMsg"></p>
            <button type="button" class="btn" id="errOkBtn" style="margin-top:18px"><span>Sawa, nirekebishe</span></button>
        </div>
    </div>
    <div class="foot"><i class="fa-solid fa-lock"></i>FeedTan CMG · Let's Grow Together</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sm = document.getElementById('successModal');
    if (sm) {
        const ok = document.getElementById('successOkBtn');
        const close = () => sm.classList.remove('show');
        if (ok) ok.addEventListener('click', close);
        sm.addEventListener('click', e => { if (e.target === sm) close(); });
    }
    const em = document.getElementById('errModal');
    if (em) {
        const ok = document.getElementById('errOkBtn');
        const close = () => em.classList.remove('show');
        if (ok) ok.addEventListener('click', close);
        em.addEventListener('click', e => { if (e.target === em) close(); });
    }
    const f = document.getElementById('verifyForm');
    if (!f) return;
    const net = parseFloat(f.dataset.net) || 0;
    const leftEl = document.getElementById('allocLeft');
    const box = document.getElementById('remainBox');
    const btn = document.getElementById('verifyBtn');
    const allocInputs = f.querySelectorAll('.alloc');
    const fillBtn = document.getElementById('fillCash');
    const clearBtn = document.getElementById('clearAll');
    const nextBtn = document.getElementById('nextBtn');
    const backBtn = document.getElementById('backBtn');
    const fmt = n => 'TZS ' + Math.round(n).toLocaleString('en-US');
    const val = name => parseFloat((f.querySelector('[name="' + name + '"]') || {}).value) || 0;
    function sum(){ let s = 0; allocInputs.forEach(i => { s += parseFloat(i.value) || 0; }); return s; }
    function showErr(msg){
        if (errModalMsg) errModalMsg.textContent = msg;
        if (miniErr) miniErr.classList.add('show');
    }
    function hideErr(){ if (miniErr) miniErr.classList.remove('show'); }
    function recalc(){
        if (!leftEl) return;
        const left = Math.round((net - sum()) * 100) / 100;
        leftEl.textContent = fmt(left);
        const done = Math.abs(left) < 0.5;
        if (box) { box.classList.toggle('ok', done); box.classList.toggle('bad', !done); }
    }
    const allocStep = document.getElementById('allocStep');
    const previewStep = document.getElementById('previewStep');
    const previewRows = document.getElementById('previewRows');
    const previewTotal = document.getElementById('previewTotal');
    const previewNotesRow = document.getElementById('previewNotesRow');
    const previewNotes = document.getElementById('previewNotes');
    const miniErr = document.getElementById('errModal');
    const errModalMsg = document.getElementById('errModalMsg');
    function checkRules(){
        let neg = false;
        allocInputs.forEach(i => { if (parseFloat(i.value) < 0) neg = true; });
        if (neg) return 'Kiasi hakiwezi kuwa namba hasi (chini ya 0).';
        const left = Math.round((net - sum()) * 100) / 100;
        if (Math.abs(left) >= 0.5) return 'Mgao lazima ujumlishe ' + fmt(net) + '. Imebaki: ' + fmt(left) + '.';
        if (val('alloc_reinvest') > 0 && !['2','4','6'].includes(f.querySelector('#reinvest_term').value)) return 'Chagua miaka 2, 4 au 6 kwa kuwekeza tena.';
        if (val('alloc_savings') > 0 && !['rda','flex','emergence'].includes(f.querySelector('#savings_type').value)) return 'Chagua RDA, Flex au Emergence kwa akiba.';
        if (val('alloc_savings') > 0 && f.querySelector('#savings_type').value === 'rda' && val('alloc_savings') <= 100000) return 'RDA inahitaji zaidi ya TZS 100,000. Chagua Flex au Emergence.';
        return null;
    }
    const LABELS = [
        ['alloc_cash', 'Pesa taslimu', 'fa-money-bill-wave'],
        ['alloc_swf', 'Lipa SWF', 'fa-shield-heart'],
        ['alloc_loan', 'Rejesho (mkopo)', 'fa-hand-holding-dollar'],
        ['alloc_shares', 'Hisa za duka', 'fa-store'],
        ['alloc_reinvest', 'Wekeza tena', 'fa-arrow-trend-up'],
        ['alloc_savings', 'Akiba', 'fa-piggy-bank'],
    ];
    function goPreview(){
        hideErr();
        const problem = checkRules();
        if (problem) { showErr(problem); return; }
        let html = '';
        LABELS.forEach(([name, label, icon]) => {
            const amt = val(name);
            if (amt <= 0) return;
            let extra = '';
            if (name === 'alloc_reinvest') extra = ' (miaka ' + f.querySelector('#reinvest_term').value + ')';
            if (name === 'alloc_savings') extra = ' (' + f.querySelector('#savings_type').value.toUpperCase() + ')';
            html += '<div class="kv"><div class="k-ico"><i class="fa-solid ' + icon + '"></i></div><div class="k">' + label + extra + '</div><div class="v">' + fmt(amt) + '</div></div>';
        });
        previewRows.innerHTML = html;
        previewTotal.textContent = fmt(sum());
        const notes = (document.getElementById('decision_notes') || {}).value || '';
        if (notes.trim()) { previewNotes.textContent = notes; previewNotesRow.style.display = 'flex'; }
        else { previewNotesRow.style.display = 'none'; }
        allocStep.classList.add('hidden-step');
        previewStep.classList.remove('hidden-step');
        previewStep.scrollIntoView({behavior:'smooth', block:'start'});
    }
    function goBack(){
        previewStep.classList.add('hidden-step');
        allocStep.classList.remove('hidden-step');
        hideErr();
        recalc();
        allocStep.scrollIntoView({behavior:'smooth', block:'start'});
    }
    if (fillBtn) fillBtn.addEventListener('click', function(){
        const cash = f.querySelector('[name="alloc_cash"]');
        const left = Math.round((net - sum()) * 100) / 100;
        if (cash) cash.value = Math.max(0, Math.round(((parseFloat(cash.value) || 0) + left) * 100) / 100);
        hideErr();
        recalc();
    });
    if (clearBtn) clearBtn.addEventListener('click', function(){
        allocInputs.forEach(i => { i.value = 0; });
        hideErr();
        recalc();
    });
    allocInputs.forEach(i => i.addEventListener('input', () => { hideErr(); recalc(); }));
    if (nextBtn) nextBtn.addEventListener('click', goPreview);
    if (backBtn) backBtn.addEventListener('click', goBack);
    const detailsStep = document.getElementById('detailsStep');
    const allocCard = document.getElementById('allocCard');
    const toBreakdownBtn = document.getElementById('toBreakdownBtn');
    const backToDetailsBtn = document.getElementById('backToDetailsBtn');
    if (toBreakdownBtn) toBreakdownBtn.addEventListener('click', () => {
        if (detailsStep) detailsStep.classList.add('hidden-step');
        toBreakdownBtn.classList.add('hidden-step');
        if (allocCard) allocCard.classList.remove('hidden-step');
        hideErr();
        recalc();
        if (allocCard) allocCard.scrollIntoView({behavior:'smooth', block:'start'});
    });
    if (backToDetailsBtn) backToDetailsBtn.addEventListener('click', () => {
        if (allocCard) allocCard.classList.add('hidden-step');
        if (detailsStep) detailsStep.classList.remove('hidden-step');
        if (toBreakdownBtn) toBreakdownBtn.classList.remove('hidden-step');
        if (detailsStep) detailsStep.scrollIntoView({behavior:'smooth', block:'start'});
    });
    recalc();
    f.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Inatuma…</span>';
    });
});
</script>
</body>
</html>
