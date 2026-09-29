<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>@yield('title', 'Error') · Feedtan Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root{
            --sand-50:#FBF7EF;
            --sand-100:#F4ECDC;
            --sand-200:#E9DCC0;
            --coffee-900:#2A1B10;
            --coffee-800:#3B2718;
            --coffee-700:#4D3422;
            --coffee-500:#7A5C42;
            --coffee-300:#A98968;
            --terracotta-600:#C2592B;
            --terracotta-500:#D06B3A;
            --terracotta-100:#F6E1D3;
            --acacia-600:#5E6E3F;
            --acacia-500:#7A8450;
            --acacia-100:#E2E7D4;
            --gold-500:#D4A24C;
            --gold-100:#F7E9CB;
            --ink:#241408;
            --ink-soft:#6B5A48;
            --line:#E4D7C2;
            --white:#FFFFFF;
            --danger:#B33A3A;
            --danger-100:#F6DCDA;
            --success:#3F6B3F;
            --radius-sm:8px;
            --radius-md:14px;
            --radius-lg:20px;
            --shadow-sm:0 1px 2px rgba(42,27,16,.08);
            --shadow-md:0 8px 24px rgba(42,27,16,.10);
            --shadow-lg:0 20px 48px rgba(42,27,16,.18);
            --sidebar-w:264px;
            --sidebar-w-collapsed:76px;
            --topbar-h:72px;
        }
        *{box-sizing:border-box;}
        html,body{height:100%;}
        body{
            margin:0;
            font-family:'Raleway',sans-serif;
            background:var(--sand-50);
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
            overflow-x:hidden;
        }
        h1,h2,h3,h4{font-family:'Raleway',sans-serif;margin:0;color:var(--coffee-900);letter-spacing:-0.01em;}
        p{margin:0;}
        a{color:inherit;text-decoration:none;}
        button{font-family:inherit;cursor:pointer;}
        ::-webkit-scrollbar{width:9px;height:9px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background:var(--coffee-300);border-radius:10px;}

        /* ---- System shell (mirrors layouts/app) ---- */
        .sidebar{
            position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:200;
            background:var(--coffee-900);
            background-image:radial-gradient(circle at 0% 0%, rgba(212,162,76,.10), transparent 55%);
            display:flex;flex-direction:column;
            transition:width .25s ease, transform .25s ease;
            border-right:1px solid rgba(255,255,255,.06);
        }
        .sidebar.collapsed{width:var(--sidebar-w-collapsed);}
        .sb-brand{
            display:flex;align-items:center;gap:12px;padding:22px 20px;
            border-bottom:1px solid rgba(255,255,255,.08);min-height:var(--topbar-h);
        }
        .sb-mark{
            width:38px;height:38px;border-radius:10px;flex:none;
            background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));
            display:flex;align-items:center;justify-content:center;color:#fff;
        }
        .sb-mark svg{width:21px;height:21px;}
        .sb-brand-text{overflow:hidden;white-space:nowrap;}
        .sb-brand-text strong{display:block;color:#fff;font-size:15.5px;line-height:1.2;}
        .sb-brand-text span{display:block;color:var(--gold-500);font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:600;}
        .sidebar.collapsed .sb-brand-text{display:none;}
        .sb-nav{flex:1;overflow-y:auto;padding:16px 12px;}
        .sb-section-label{
            color:rgba(255,255,255,.32);font-size:10.5px;font-weight:700;letter-spacing:.09em;
            text-transform:uppercase;padding:14px 12px 8px;
        }
        .sidebar.collapsed .sb-section-label{display:none;}
        .sb-item{
            display:flex;align-items:center;gap:13px;padding:11px 12px;border-radius:10px;
            color:rgba(255,255,255,.62);font-size:14px;font-weight:500;margin-bottom:2px;
            white-space:nowrap;transition:background .15s,color .15s;
        }
        .sb-item:hover{background:rgba(255,255,255,.06);color:#fff;}
        .sb-item.active{background:rgba(212,162,76,.16);color:var(--gold-500);}
        .sb-item svg{width:19px;height:19px;flex:none;}
        .sidebar.collapsed .sb-item span{display:none;}
        .sidebar.collapsed .sb-item{justify-content:center;}
        .sb-foot{padding:14px 16px;border-top:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.38);font-size:11px;line-height:1.6;}
        .sidebar.collapsed .sb-foot{display:none;}

        .main{margin-left:var(--sidebar-w);transition:margin-left .25s ease;min-height:100vh;display:flex;flex-direction:column;}
        .sidebar.collapsed ~ .main{margin-left:var(--sidebar-w-collapsed);}
        .topbar{
            position:sticky;top:0;z-index:100;height:var(--topbar-h);
            background:rgba(251,247,239,.86);backdrop-filter:blur(10px);
            border-bottom:1px solid var(--line);
            display:flex;align-items:center;gap:16px;padding:0 28px;
        }
        .tb-toggle{
            width:38px;height:38px;border-radius:10px;border:1.5px solid var(--line);background:var(--white);
            display:flex;align-items:center;justify-content:center;flex:none;color:var(--coffee-700);
        }
        .tb-toggle svg{width:18px;height:18px;}
        .tb-toggle:hover{background:var(--sand-100);}
        .tb-crumb{display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--ink-soft);font-weight:600;min-width:0;}
        .tb-crumb b{color:var(--coffee-900);}
        .tb-crumb .sep{opacity:.5;}
        .tb-right{margin-left:auto;display:flex;align-items:center;gap:10px;}
        .tb-live{
            display:flex;align-items:center;gap:7px;background:var(--acacia-100);color:var(--acacia-600);
            padding:7px 13px;border-radius:20px;font-size:12.5px;font-weight:700;
        }
        .tb-live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--acacia-600);}
        .tb-live.is-down{background:var(--danger-100);color:var(--danger);}
        .tb-live.is-down::before{background:var(--danger);}
        .tb-user{
            display:flex;align-items:center;gap:9px;
            background:var(--white);border:1.5px solid var(--line);border-radius:11px;padding:5px 12px 5px 5px;
        }
        .tb-user-avatar{
            width:30px;height:30px;border-radius:50%;flex:none;
            background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));
            display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;
        }
        .tb-user-text b{display:block;color:var(--coffee-900);font-size:12.5px;line-height:1.25;}
        .tb-user-text span{display:block;color:var(--ink-soft);font-size:11px;}

        .view-wrap{padding:28px;flex:1;display:flex;flex-direction:column;}
        .view{animation:fadeUp .35s ease;flex:1;display:flex;flex-direction:column;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}
        .view-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap;}
        .view-head h2{font-size:27px;}
        .view-head .sub{color:var(--ink-soft);font-size:14px;margin-top:5px;}

        /* ---- Error content ---- */
        .error-wrap{
            flex:1;display:flex;align-items:center;justify-content:center;
            padding:24px 0;
        }
        .error-content{
            width:100%;max-width:640px;margin:0 auto;padding:20px 12px;text-align:center;
        }
        .error-icon{
            width:84px;height:84px;border-radius:24px;margin:0 auto 20px;
            display:flex;align-items:center;justify-content:center;
            background:var(--err-tint,var(--terracotta-100));
            color:var(--err-fg,var(--terracotta-600));
            box-shadow:var(--shadow-sm);
        }
        .error-icon svg{width:40px;height:40px;}
        .error-code{
            font-size:64px;font-weight:800;letter-spacing:-0.03em;
            color:var(--coffee-900);line-height:1;
        }
        .error-code small{font-size:20px;font-weight:700;color:var(--coffee-300);vertical-align:super;margin-left:4px;}
        .error-tag{
            display:inline-flex;align-items:center;gap:6px;margin-bottom:14px;
            padding:5px 13px;border-radius:20px;font-size:11.5px;font-weight:800;
            letter-spacing:.07em;text-transform:uppercase;
            background:var(--err-tint,var(--terracotta-100));
            color:var(--err-fg,var(--terracotta-600));
        }
        .error-content h1{font-size:26px;margin:10px 0 10px;}
        .error-content .lede{color:var(--ink-soft);font-size:14.5px;line-height:1.65;max-width:460px;margin:0 auto;}
        .error-meta{
            margin:22px auto 0;max-width:480px;
            background:var(--sand-100);border:1px dashed var(--coffee-300);
            border-radius:var(--radius-sm);padding:13px 15px;
            font-size:12.5px;color:var(--coffee-800);text-align:left;line-height:1.6;
            word-break:break-word;
        }
        .error-meta code{
            background:var(--white);border:1px solid var(--line);border-radius:5px;
            padding:1px 6px;font-size:11.5px;color:var(--coffee-700);
        }
        .error-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:26px;}
        .btn{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            padding:12px 20px;border-radius:var(--radius-sm);border:none;
            font-weight:600;font-size:14.5px;text-decoration:none;transition:background .15s,transform .12s;
        }
        .btn:active{transform:translateY(1px);}
        .btn svg{width:16px;height:16px;}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32);}
        .btn-primary:hover{background:var(--terracotta-500);}
        .btn-ghost{background:transparent;color:var(--coffee-700);border:1.5px solid var(--line);}
        .btn-ghost:hover{background:var(--sand-100);}
        .btn-soft{background:var(--sand-100);color:var(--coffee-800);}
        .btn-soft:hover{background:var(--sand-200);}
        .error-links{margin-top:20px;font-size:12.5px;color:var(--ink-soft);}
        .error-links a{color:var(--terracotta-600);font-weight:700;}
        .error-links a:hover{text-decoration:underline;}
        .error-foot{margin-top:22px;padding-top:16px;border-top:1px dashed var(--line);font-size:11.5px;color:var(--coffee-300);}

        .mobile-overlay{position:fixed;inset:0;background:rgba(36,20,8,.45);z-index:190;display:none;}
        .mobile-overlay.show{display:block;}
        @media (max-width:900px){
            .sidebar{transform:translateX(-100%);width:var(--sidebar-w);}
            .sidebar.mobile-open{transform:translateX(0);}
            .main{margin-left:0 !important;}
            .tb-crumb{display:none;}
        }
        @media (max-width:640px){
            .view-wrap{padding:16px;}
            .topbar{padding:0 14px;gap:10px;}
            .error-content{padding:12px 4px;}
            .error-code{font-size:48px;}
            .tb-live span{display:none;}
            .tb-user-text{display:none;}
            .view-head h2{font-size:22px;}
        }
    </style>
    @yield('head')
</head>
<body>
    @php
        // Keep error layout crash-safe: never touch DB or assume auth/route state.
        $errorUser = null;
        try { $errorUser = auth()->user(); } catch (\Throwable $e) { $errorUser = null; }
        $errorInitials = 'FP';
        try {
            if ($errorUser && $errorUser->name) {
                $errorInitials = strtoupper(implode('', array_map(fn ($w) => $w[0] ?? '', preg_split('/\s+/', $errorUser->name))));
            }
        } catch (\Throwable $e) { $errorInitials = 'FP'; }
        $homeUrl = '/';
        try { $homeUrl = url('/dashboard'); } catch (\Throwable $e) { $homeUrl = url('/'); }
        $loginUrl = '/login';
        try { $loginUrl = url('/login'); } catch (\Throwable $e) { $loginUrl = url('/'); }
    @endphp
    <div id="app">
        <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sb-brand">
                <div class="sb-mark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                </div>
                <div class="sb-brand-text">
                    <strong>Feedtan&nbsp;Portal</strong>
                    <span>Let's Grow Together</span>
                </div>
            </div>
            <nav class="sb-nav">
                <div class="sb-section-label">Overview</div>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect></svg>
                    <span>Dashboard</span>
                </a>
                <div class="sb-section-label">Workspace</div>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Members</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <span>Loans</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    <span>Deposits</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    <span>Investments</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>SWF</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <span>Finance</span>
                </a>
                <a href="{{ $homeUrl }}" class="sb-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg>
                    <span>Reports</span>
                </a>
            </nav>
            <div class="sb-foot">Feedtan Portal · Error page<br>Your workspace shell stays visible while the content explains the problem.</div>
        </aside>

        <div class="main" id="mainArea">
            <header class="topbar">
                <button class="tb-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="7" x2="20" y2="7"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="17" x2="20" y2="17"></line></svg>
                </button>
                <div class="tb-crumb"><b>Feedtan Portal</b><span class="sep">/</span><span>Error @yield('code', '')</span></div>
                <div class="tb-right">
                    <span class="tb-live {{ in_array($__env->yieldContent('code'), ['500','502','503','504']) ? 'is-down' : '' }}"><span>System @yield('code', 'Error')</span></span>
                    @if($errorUser)
                        <span class="tb-user">
                            <span class="tb-user-avatar">{{ $errorInitials }}</span>
                            <span class="tb-user-text"><b>{{ $errorUser->name }}</b><span>Signed in</span></span>
                        </span>
                    @else
                        <a class="btn btn-ghost" style="padding:9px 16px;font-size:13px;" href="{{ $loginUrl }}">Sign in</a>
                    @endif
                </div>
            </header>

            <div class="view-wrap">
                <div class="view">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar(){
            if(window.innerWidth <= 900){
                document.getElementById('sidebar').classList.toggle('mobile-open');
                document.getElementById('mobileOverlay').classList.toggle('show');
            } else {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }
        }
        function closeMobileSidebar(){
            document.getElementById('sidebar').classList.remove('mobile-open');
            document.getElementById('mobileOverlay').classList.remove('show');
        }
        function goBack(){
            if (window.history.length > 1) { window.history.back(); }
            else { window.location.href = '/'; }
        }
    </script>
    @yield('scripts')
</body>
</html>
