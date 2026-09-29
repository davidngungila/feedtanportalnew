<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Create account · Feedtan Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root{
            --sand-50:#FBF7EF;--sand-100:#F4ECDC;--sand-200:#E9DCC0;
            --coffee-900:#2A1B10;--coffee-800:#3B2718;--coffee-700:#4D3422;--coffee-500:#7A5C42;--coffee-300:#A98968;
            --terracotta-600:#C2592B;--terracotta-500:#D06B3A;--terracotta-100:#F6E1D3;
            --acacia-600:#5E6E3F;--acacia-100:#E2E7D4;
            --gold-500:#D4A24C;--gold-100:#F7E9CB;
            --ink:#241408;--ink-soft:#6B5A48;--line:#E4D7C2;--white:#FFF;
            --danger:#B33A3A;--danger-100:#F6DCDA;
            --radius-sm:8px;--radius-md:14px;--radius-lg:20px;
            --shadow-lg:0 20px 48px rgba(42,27,16,.18);
        }
        *{box-sizing:border-box;}
        body{
            margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
            font-family:'Raleway',sans-serif;background:var(--coffee-900);
            background-image:radial-gradient(circle at 85% 15%, rgba(212,162,76,.16), transparent 45%), radial-gradient(circle at 10% 90%, rgba(194,89,43,.18), transparent 45%);
            padding:24px;color:var(--ink);
        }
        .login-card{width:100%;max-width:440px;background:var(--sand-50);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:40px 38px;animation:riseIn .4s cubic-bezier(.2,.8,.2,1);}
        @keyframes riseIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:8px;}
        .brand-mark{width:42px;height:42px;border-radius:11px;flex:none;color:#fff;display:flex;align-items:center;justify-content:center;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));}
        .brand-mark svg{width:22px;height:22px;}
        .brand-text strong{display:block;font-size:17px;color:var(--coffee-900);}
        .brand-text span{display:block;font-size:11px;letter-spacing:.07em;text-transform:uppercase;color:var(--terracotta-600);font-weight:700;}
        h1{font-size:23px;text-align:center;color:var(--coffee-900);margin:22px 0 6px;}
        .sub{text-align:center;color:var(--ink-soft);font-size:13.8px;margin-bottom:26px;}
        .field{margin-bottom:16px;}
        .field label{display:block;font-size:12.5px;font-weight:600;color:var(--coffee-700);margin-bottom:7px;text-transform:uppercase;letter-spacing:.04em;}
        .field input{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:var(--radius-sm);background:var(--white);font-size:14.5px;color:var(--ink);}
        .field input:focus{outline:none;border-color:var(--terracotta-500);box-shadow:0 0 0 3px var(--terracotta-100);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .btn{width:100%;display:inline-flex;align-items:center;justify-content:center;padding:13px 20px;border:none;border-radius:var(--radius-sm);font-weight:700;font-size:14.5px;color:#fff;cursor:pointer;background:var(--terracotta-600);box-shadow:0 6px 16px rgba(194,89,43,.32);margin-top:6px;font-family:inherit;}
        .btn:hover{background:var(--terracotta-500);}
        .error{background:var(--danger-100);color:var(--danger);border-radius:10px;padding:12px 14px;font-size:13px;font-weight:600;margin-bottom:16px;}
        .foot{margin-top:20px;text-align:center;font-size:13px;color:var(--ink-soft);}
        .foot a{color:var(--terracotta-600);font-weight:700;}
        .steps{display:flex;gap:6px;justify-content:center;margin-bottom:6px;flex-wrap:wrap;}
        .steps span{font-size:11px;font-weight:700;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;padding:4px 11px;color:var(--coffee-700);white-space:nowrap;}
        @media (max-width:520px){
            body{padding:14px;}
            .login-card{padding:28px 20px;border-radius:16px;}
            h1{font-size:20px;margin:16px 0 5px;}
            .sub{font-size:13px;margin-bottom:20px;}
            .form-row{grid-template-columns:1fr;}
            .brand-mark{width:38px;height:38px;}
        }
        @media (max-width:360px){
            .login-card{padding:24px 16px;}
            .field input{padding:11px 12px;font-size:14px;}
            .btn{padding:12px 16px;font-size:14px;}
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <div class="brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </div>
            <div class="brand-text"><strong>Feedtan Portal</strong><span>Let's Grow Together</span></div>
        </div>
        <h1>Create your account</h1>
        <p class="sub">Sign up, then finish membership step by step.</p>
        <div class="steps"><span>1 · Account</span><span>2 · Details</span><span>3 · Approval</span><span>4 · Services</span></div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Amina Juma" required autofocus></div>
            <div class="form-row">
                <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone') }}" placeholder="07…" required></div>
                <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required></div>
            </div>
            <div class="form-row">
                <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" placeholder="Min 6 characters" required></div>
                <div class="field"><label for="password_confirmation">Confirm</label><input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required></div>
            </div>
            <button type="submit" class="btn">Create account &amp; continue</button>
        </form>

        <div class="foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>
</body>
</html>
