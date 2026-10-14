@extends($activeTemplate.'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Noto+Nastaliq+Urdu:wght@400;600&display=swap" rel="stylesheet">
<style>
.up-wrap{font-family:'Poppins',sans-serif;max-width:720px;margin:0 auto;padding-bottom:2rem;}
.up-title{text-align:center;margin-bottom:1.4rem;}
.up-title h2{font-size:1.5rem;font-weight:800;color:#1a1a2e;margin-bottom:.25rem;}
.up-title p{color:#999;font-size:.88rem;}
.up-current{background:#fff;border:1.8px solid #f0d0e4;border-radius:18px;padding:1rem 1.2rem;margin-bottom:1.2rem;display:flex;justify-content:space-between;align-items:center;gap:12px;}
.up-current span{color:#888;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;}
.up-current strong{display:block;color:#1a1a2e;font-size:1.05rem;margin-top:2px;}
.up-current b{color:#e91e8c;font-size:1.25rem;}
.up-card{display:block;background:#fff;border:2px solid #f0e0ec;border-radius:16px;padding:1rem 1.1rem;margin-bottom:.8rem;cursor:pointer;}
.up-card.active{border-color:#e91e8c;box-shadow:0 8px 24px rgba(233,30,140,.12);}
.up-card h3{font-size:1rem;font-weight:700;margin:0 0 .35rem;color:#1a1a2e;}
.up-meta{display:flex;justify-content:space-between;gap:10px;font-size:.86rem;color:#666;}
.up-meta b{color:#e91e8c;}
.up-note{background:#fdf6f9;border:1px solid #f3d3e4;border-radius:14px;padding:.9rem 1rem;margin:1rem 0;direction:rtl;text-align:right;font-family:'Noto Nastaliq Urdu','Poppins',serif;font-size:.95rem;line-height:2;color:#4a3042;}
.up-label{display:block;font-size:.75rem;font-weight:700;color:#888;margin-bottom:.35rem;text-transform:uppercase;letter-spacing:.05em;}
.up-input{width:100%;padding:12px 14px;border:1.8px solid #f0d0e4;border-radius:12px;background:#fdf5f9;margin-bottom:.9rem;font-size:.95rem;}
.up-input::placeholder{color:#c9b3c0;font-family:'Noto Nastaliq Urdu','Poppins',serif;font-size:.9rem;}
.up-input:focus{outline:none;border-color:#e91e8c;background:#fff;}
.up-errors{background:#fff1f2;border:1.5px solid #fecdd3;color:#9f1239;border-radius:14px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.84rem;}
.up-errors p{margin:0 0 .25rem;}
.up-errors p:last-child{margin:0;}
.up-shot{position:relative;display:block;min-height:148px;border:1.8px dashed #f0d0e4;border-radius:14px;background:#fff;overflow:hidden;cursor:pointer;margin-bottom:.4rem;}
.up-shot input[type=file]{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;}
.up-shot-placeholder{min-height:148px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:#b07a96;font-size:.86rem;font-weight:600;text-align:center;padding:1rem;pointer-events:none;}
.up-shot-preview{display:none;width:100%;max-height:240px;object-fit:contain;background:#fff;}
.up-shot.has-preview .up-shot-placeholder{display:none;}
.up-shot.has-preview .up-shot-preview{display:block;}
.up-shot-hint{display:block;margin-bottom:.9rem;font-size:.75rem;color:#a07;}
.up-btn{width:100%;border:none;border-radius:14px;padding:14px;color:#fff;font-weight:700;background:linear-gradient(135deg,#ff4da6,#e91e8c);cursor:pointer;}
.up-btn:disabled{opacity:.5;cursor:not-allowed;}
.up-alert{border-radius:14px;padding:.9rem 1rem;margin-bottom:1rem;font-size:.88rem;}
.up-alert.wait{background:#fffbeb;color:#92400e;border:1px solid #fde68a;}
.up-gate{border:1px solid #f3d3e4;border-radius:12px;padding:.8rem;margin-bottom:.6rem;background:#fff;}
.up-history{margin-top:1.6rem;background:#fff;border-radius:16px;border:1px solid #f0d0e4;overflow:hidden;}
.up-history h4{margin:0;padding:.9rem 1rem;background:#fdf5f9;font-size:.95rem;}
.up-history table{width:100%;font-size:.82rem;border-collapse:collapse;}
.up-history td,.up-history th{padding:.65rem .8rem;border-top:1px solid #f6e7ef;text-align:left;}
</style>

<div class="up-wrap">
    <div class="up-title">
        <h2>Plan Upgrade</h2>
        <p>Pay only the remaining amount for the next package</p>
    </div>

    <div class="up-current">
        <div>
            <span>Current Plan</span>
            <strong>{{ $current->name }}</strong>
        </div>
        <b>Rs {{ showAmount($current->price, 0) }}</b>
    </div>

    @if($pending)
        <div class="up-alert wait">
            <strong>Upgrade request is pending.</strong><br>
            {{ $pending->fromPlan->name ?? 'Current' }} → {{ $pending->toPlan->name ?? 'New plan' }}
            for Rs {{ showAmount($pending->amount, 0) }}.
            Submitted {{ showDateTime($pending->created_at) }}.
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="up-alert wait">You are already on the highest active package.</div>
    @else
        <form action="{{ route('user.plan.upgrade.submit') }}" method="POST" enctype="multipart/form-data" id="upgradeProofForm">
            @csrf
            @if ($errors->any())
                <div class="up-errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            @foreach($plans as $plan)
                @php $remaining = round((float) $plan->price - (float) $current->price, 2); @endphp
                <label class="up-card">
                    <input type="radio" name="plan_id" value="{{ $plan->id }}" data-remaining="{{ $remaining }}" @checked($loop->first) required style="margin-bottom:.4rem;">
                    <h3>{{ $plan->name }}</h3>
                    <div class="up-meta">
                        <span>Package Rs {{ showAmount($plan->price, 0) }}</span>
                        <b>Remaining Rs {{ showAmount($remaining, 0) }}</b>
                    </div>
                </label>
            @endforeach

            <div class="up-current">
                <div>
                    <span>Amount to pay</span>
                    <strong id="payAmount">Rs {{ showAmount(round((float) $plans->first()->price - (float) $current->price, 2), 0) }}</strong>
                </div>
            </div>

            @if($gateways->count())
                <label class="up-label">Payment Method</label>
                @foreach($gateways as $gateway)
                    <label class="up-gate">
                        <input type="radio" name="method_code" value="{{ $gateway->method_code }}" @checked($loop->first) required>
                        <strong>{{ $gateway->method->name ?? $gateway->name }}</strong>
                        <div style="font-size:.84rem;color:#555;margin-top:.4rem;">{!! $gateway->method->description ?? '' !!}</div>
                    </label>
                @endforeach
            @endif

            <div class="up-note">
                ادائیگی کی تصدیق کے لیے براہِ کرم رقم بھیجنے والے اکاؤنٹ کی درست معلومات درج کریں۔ Sender Name میں رقم بھیجنے والے کا نام اور Sender Number میں وہ موبائل نمبر درج کریں جس سے رقم ادا کی گئی ہے۔ اس کے بعد Payment Screenshot لازمی Upload کرکے اپنی Payment Submit کریں۔
            </div>

            <label class="up-label">Sender Name</label>
            <input class="up-input" type="text" name="sender_name" value="{{ old('sender_name') }}" placeholder="یہاں رقم بھیجنے والے کا نام لکھیں" required @disabled($pending)>

            <label class="up-label">Sender Number</label>
            <input class="up-input" type="text" name="sender_number" value="{{ old('sender_number') }}" placeholder="یہاں وہ نمبر لکھیں جس سے رقم بھیجی گئی ہے" required @disabled($pending)>

            <label class="up-label">Payment Screenshot</label>
            <label class="up-shot" id="upgradeShotBox">
                <input type="file" id="upgrade_screenshot" name="screenshot" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required @disabled($pending)>
                <img class="up-shot-preview" id="upgradeShotPreview" alt="Payment screenshot preview">
                <span class="up-shot-placeholder">Tap to upload payment screenshot<small style="font-weight:500;">JPG, PNG or WEBP</small></span>
            </label>
            <small class="up-shot-hint" id="upgradeShotHint"></small>

            <button class="up-btn" type="submit" @disabled($pending)>Submit Upgrade Payment</button>
        </form>
    @endif

    @if($history->count())
        <div class="up-history">
            <h4>Upgrade History</h4>
            <table>
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $item)
                        <tr>
                            <td>{{ $item->fromPlan->name ?? '—' }} → {{ $item->toPlan->name ?? '—' }}</td>
                            <td>Rs {{ showAmount($item->amount, 0) }}</td>
                            <td>{{ $item->statusText() }}</td>
                            <td>{{ showDateTime($item->created_at, 'd M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.querySelectorAll('input[name=plan_id]').forEach(function (input) {
    input.addEventListener('change', function () {
        document.querySelectorAll('.up-card').forEach(function (card) { card.classList.remove('active'); });
        input.closest('.up-card').classList.add('active');
        var amount = document.getElementById('payAmount');
        if (amount) {
            amount.textContent = 'Rs ' + Number(input.dataset.remaining).toLocaleString();
        }
    });
});
var firstPlan = document.querySelector('input[name=plan_id]:checked');
if (firstPlan) firstPlan.closest('.up-card').classList.add('active');

(function () {
    var input = document.getElementById('upgrade_screenshot');
    var box = document.getElementById('upgradeShotBox');
    var preview = document.getElementById('upgradeShotPreview');
    var hint = document.getElementById('upgradeShotHint');
    var form = document.getElementById('upgradeProofForm');
    if (!input || !box || !preview) return;

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        box.classList.remove('has-preview');
        preview.removeAttribute('src');
        if (hint) hint.textContent = '';
        if (!file) return;

        var name = (file.name || '').toLowerCase();
        if (name.endsWith('.heic') || name.endsWith('.heif') || file.type === 'image/heic') {
            input.value = '';
            if (hint) hint.textContent = 'iPhone HEIC photos are not supported. Save the screenshot as JPG or PNG, then upload it.';
            return;
        }
        var reader = new FileReader();
        reader.onload = function () {
            preview.src = reader.result;
            box.classList.add('has-preview');
        };
        reader.readAsDataURL(file);
    });

    if (form) {
        form.addEventListener('submit', function () {
            var button = form.querySelector('.up-btn');
            if (button && !button.disabled) {
                button.disabled = true;
                button.textContent = 'Submitting...';
            }
        });
    }
})();
</script>
@endsection
