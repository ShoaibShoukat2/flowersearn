@extends($activeTemplate.'layouts.frontend')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;}

.fl-page{
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;
    background:linear-gradient(145deg,#ff4da6 0%,#e91e8c 40%,#fdf5f9 40%);
    padding:2rem 1.2rem;
    font-family:'Poppins',sans-serif;
}

.fl-card{
    width:100%;max-width:440px;
    background:#fff;
    border-radius:24px;
    padding:2.5rem 2.2rem;
    box-shadow:0 16px 60px rgba(233,30,140,0.18);
}

/* icon circle */
.fl-icon-wrap{
    width:72px;height:72px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 1.4rem;
    box-shadow:0 8px 24px rgba(233,30,140,0.30);
}
.fl-icon-wrap i{font-size:28px;color:#fff;}

.fl-card-head{text-align:center;margin-bottom:1.8rem;}
.fl-card-head h2{font-size:1.55rem;font-weight:700;color:#1a1a2e;margin-bottom:0.4rem;}
.fl-card-head p{font-size:0.87rem;color:#aaa;line-height:1.6;}

/* field */
.fl-field{margin-bottom:1.2rem;}
.fl-field label{
    display:block;font-size:0.75rem;font-weight:600;
    color:#666;margin-bottom:0.4rem;
    text-transform:uppercase;letter-spacing:0.06em;
}
.fl-input{
    display:flex;align-items:center;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:12px;overflow:hidden;
    transition:border-color 0.25s,box-shadow 0.25s;
}
.fl-input:focus-within{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background:#fff;
}
.fl-input .ico{
    width:46px;display:flex;align-items:center;justify-content:center;
    color:#e91e8c;font-size:15px;flex-shrink:0;
}
.fl-input input{
    flex:1;border:none;outline:none;
    padding:13px 14px 13px 0;
    font-size:0.92rem;font-family:'Poppins',sans-serif;
    color:#333;background:transparent;
}
.fl-input input::placeholder{color:#ccc;font-weight:300;}

/* button */
.fl-btn{
    width:100%;padding:14px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:12px;
    color:#fff;font-size:0.96rem;font-weight:600;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 22px rgba(233,30,140,0.30);
    display:flex;align-items:center;justify-content:center;gap:9px;
    margin-top:0.4rem;
}
.fl-btn:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(233,30,140,0.40);
}

/* back link */
.fl-back{
    display:flex;align-items:center;justify-content:center;
    gap:6px;margin-top:1.3rem;
    font-size:0.86rem;color:#aaa;text-decoration:none;
    transition:color 0.2s;
}
.fl-back:hover{color:#e91e8c;}
.fl-back i{font-size:13px;}
</style>

<div class="fl-page">
    <div class="fl-card">

        <div class="fl-icon-wrap">
            <i class="fas fa-key"></i>
        </div>

        <div class="fl-card-head">
            <h2>Account Recovery 🔑</h2>
            <p>Enter your email or username and we'll help you get back into your account.</p>
        </div>

        <form method="POST" action="{{ route('user.password.email') }}">
            @csrf
            <div class="fl-field">
                <label>Email or Username</label>
                <div class="fl-input">
                    <div class="ico"><i class="fas fa-user"></i></div>
                    <input type="text" name="value" value="{{ old('value') }}"
                           placeholder="Enter your email or username" required autofocus>
                </div>
            </div>

            <button type="submit" class="fl-btn">
                <i class="fas fa-paper-plane"></i> Send Recovery Link
            </button>
        </form>

        <a href="{{ route('user.login') }}" class="fl-back">
            <i class="fas fa-arrow-left"></i> Back to Sign In
        </a>

    </div>
</div>

@endsection
