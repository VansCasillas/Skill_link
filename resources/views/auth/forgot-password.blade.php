<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi · Skill Link</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 20px;
            box-sizing: border-box;
            background: #CBDEF6;
            color: #0F172A;
            font-family: system-ui, sans-serif
        }

        .card {
            width: min(100%, 440px);
            padding: 32px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(10, 51, 102, .14)
        }

        h1 { margin: 0 0 8px; font-size: 25px }
        p { color: #475569; font-size: 14px; line-height: 1.5 }
        label { display: block; margin: 20px 0 6px; font-size: 13px; font-weight: 600 }
        input { width: 100%; height: 44px; padding: 0 12px; box-sizing: border-box; border: 1px solid #94A3B8; border-radius: 9px; font: inherit }
        button { width: 100%; margin-top: 16px; padding: 12px; border: 0; border-radius: 9px; background: #0A87D9; color: #fff; font: inherit; font-weight: 700; cursor: pointer }
        a { display: block; margin-top: 18px; color: #0A6DB0; text-align: center; font-size: 13px; font-weight: 600; text-decoration: none }
        .status { padding: 12px; border-radius: 9px; background: #ECFDF3; color: #067647; font-size: 13px }
        .error { margin-top: 6px; color: #D92D20; font-size: 12px }
    </style>
</head>

<body>
    <main class="card">
        <h1>Lupa kata sandi?</h1>
        <p>Masukkan email akun Anda. Jika terdaftar, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

        @if (session('status'))
            <div class="status" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')
                <div class="error" role="alert">{{ $message }}</div>
            @enderror
            <button type="submit">Kirim tautan reset</button>
        </form>

        <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
    </main>
</body>

</html>
