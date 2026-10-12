<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opening Task</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;}
        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px 16px;
            font-family:'Poppins',sans-serif;
            background:
                radial-gradient(ellipse at top, rgba(255,77,158,.35), transparent 55%),
                linear-gradient(165deg,#1a0535 0%,#2d0a4e 100%);
            color:#fff;
        }
        .task-card{
            width:100%;
            max-width:420px;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.16);
            border-radius:24px;
            padding:2rem 1.4rem 1.5rem;
            text-align:center;
            box-shadow:0 20px 50px rgba(0,0,0,.28);
        }
        .task-flower{font-size:3.2rem;line-height:1;margin-bottom:.6rem;}
        h1{font-size:1.35rem;font-weight:800;margin:0 0 .35rem;}
        p{margin:0;color:rgba(255,255,255,.72);font-size:.9rem;line-height:1.5;}
        .task-count{
            margin:1.3rem 0 .4rem;
            font-size:2.6rem;
            font-weight:800;
            letter-spacing:.02em;
        }
        .task-track{
            height:10px;
            border-radius:999px;
            background:rgba(255,255,255,.16);
            overflow:hidden;
            margin:0 0 1.2rem;
        }
        #myBar{
            height:100%;
            width:0;
            border-radius:999px;
            background:linear-gradient(90deg,#ff4da6,#e91e8c);
        }
        button{
            width:100%;
            border:none;
            border-radius:14px;
            padding:14px 16px;
            font-family:inherit;
            font-size:.95rem;
            font-weight:700;
            color:#fff;
            background:linear-gradient(135deg,#ff4da6,#e91e8c);
            cursor:pointer;
        }
        button:disabled{opacity:.55;cursor:default;}
    </style>
</head>
<body>
    @php $seconds = max(1, (int) ($ptc->duration ?? 5)); @endphp
    <div class="task-card">
        <div class="task-flower">🌸</div>
        <h1>{{ $ptc->title ?: 'Your task' }}</h1>
        <p>Stay on this screen. The task will open when the timer finishes.</p>
        <div class="task-count" id="taskCount">{{ $seconds }}</div>
        <div class="task-track"><div id="myBar"></div></div>
        <form action="{{ route('user.ptc.confirm', encrypt($ptc->id.'|'.auth()->user()->id)) }}" id="confirm-form" method="post">
            @csrf
            <button id="confirm" type="submit" disabled>Opening task...</button>
        </form>
    </div>
    <script>
        (function () {
            var seconds = {{ $seconds }};
            var left = seconds;
            var locked = true;
            var form = document.getElementById('confirm-form');
            var bar = document.getElementById('myBar');
            var count = document.getElementById('taskCount');
            var button = document.getElementById('confirm');
            var started = Date.now();

            form.addEventListener('submit', function (event) {
                if (locked) event.preventDefault();
            });

            var timer = setInterval(function () {
                var gone = (Date.now() - started) / 1000;
                var progress = Math.min(100, (gone / seconds) * 100);
                left = Math.max(0, Math.ceil(seconds - gone));
                if (bar) bar.style.width = progress + '%';
                if (count) count.textContent = left > 0 ? left : '✨';
                if (gone < seconds) return;

                clearInterval(timer);
                locked = false;
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Opening task...';
                }
                form.submit();
            }, 200);
        })();
    </script>
</body>
</html>
