@extends($activeTemplate.'layouts.dashboard')

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ── Reset & base ── */
.mn-wrap *{box-sizing:border-box;font-family:'Poppins',sans-serif;}
.mn-wrap{max-width:520px;margin:0 auto;padding:0 0 3rem;}

/* ── Page title ── */
.mn-title{text-align:center;margin-bottom:1.6rem;}
.mn-title h2{font-size:1.55rem;font-weight:800;color:#1a1a2e;margin-bottom:0.2rem;}
.mn-title p{font-size:0.86rem;color:#bbb;}

/* ══════════════════════════
   SUMMARY CARD
══════════════════════════ */
.mn-summary{
    background:#fff;
    border-radius:20px;
    border:1.8px solid #f0d0e4;
    overflow:hidden;
    margin-bottom:1.2rem;
    box-shadow:0 4px 20px rgba(233,30,140,0.07);
}
.mn-summary-row{
    display:flex;align-items:center;
    justify-content:space-between;
    padding:1rem 1.4rem;
    border-bottom:1px solid #fce4f0;
}
.mn-summary-row:last-child{border-bottom:none;}
.mn-summary-row .lbl{
    font-size:0.85rem;color:#aaa;font-weight:500;
}
.mn-summary-row .val{
    font-size:0.95rem;font-weight:700;color:#e91e8c;
}
.mn-summary-row .val.dark{color:#1a1a2e;}

/* ══════════════════════════
   PAYMENT DETAILS CARD
══════════════════════════ */
.mn-details{
    background:#fff;
    border-radius:20px;
    border:1.8px solid #f0d0e4;
    overflow:hidden;
    margin-bottom:1.2rem;
    box-shadow:0 4px 20px rgba(233,30,140,0.07);
}
.mn-details-head{
    display:flex;align-items:center;gap:10px;
    padding:1rem 1.4rem;
    border-bottom:1px solid #fce4f0;
    background:#fdf5f9;
}
.mn-details-head i{color:#e91e8c;font-size:18px;}
.mn-details-head span{font-size:0.95rem;font-weight:700;color:#1a1a2e;}

.mn-detail-item{
    padding:0.9rem 1.4rem;
    border-bottom:1px solid #fce4f0;
}
.mn-detail-item:last-child{border-bottom:none;}
.mn-detail-item .di-label{
    font-size:0.75rem;color:#bbb;font-weight:500;
    margin-bottom:0.3rem;text-transform:uppercase;letter-spacing:0.05em;
}
.mn-detail-item .di-row{
    display:flex;align-items:center;justify-content:space-between;gap:10px;
}
.mn-detail-item .di-val{
    font-size:1rem;font-weight:700;color:#1a1a2e;
    word-break:break-all;
}
.mn-copy-btn{
    flex-shrink:0;
    width:38px;height:38px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border:none;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:#fff;font-size:14px;
    transition:all 0.2s;box-shadow:0 3px 10px rgba(233,30,140,0.25);
}
.mn-copy-btn:hover{transform:scale(1.08);box-shadow:0 5px 16px rgba(233,30,140,0.35);}
.mn-copy-btn.copied{background:linear-gradient(135deg,#22c55e,#16a34a);}

/* ══════════════════════════
   INSTRUCTIONS CARD
══════════════════════════ */
.mn-instr{
    background:#fff;
    border-radius:20px;
    border:1.8px solid #f0d0e4;
    overflow:hidden;
    margin-bottom:1.2rem;
    box-shadow:0 4px 20px rgba(233,30,140,0.07);
}
.mn-instr-head{
    display:flex;align-items:center;gap:10px;
    padding:1rem 1.4rem;
    border-bottom:1px solid #fce4f0;
    background:#fdf5f9;
}
.mn-instr-head i{color:#e91e8c;font-size:18px;}
.mn-instr-head span{font-size:0.95rem;font-weight:700;color:#1a1a2e;}
.mn-instr-body{
    padding:1rem 1.4rem;
    font-size:0.88rem;color:#555;line-height:1.7;
}
/* Force all injected HTML inside instructions to stay small & left-aligned */
.mn-instr-body *{
    font-size:0.88rem !important;
    line-height:1.7 !important;
    text-align:left !important;
    color:#555 !important;
    font-weight:500 !important;
    white-space:normal !important;
    word-break:break-word !important;
}
.mn-instr-body br{display:block;margin:2px 0;content:'';}
/* Also fix the inline detail fallback */
.mn-detail-item div[style]{
    font-size:0.88rem !important;
    text-align:left !important;
    line-height:1.7 !important;
}
.mn-detail-item div[style] *{
    font-size:0.88rem !important;
    text-align:left !important;
    line-height:1.7 !important;
    color:#555 !important;
    font-weight:500 !important;
}

/* ══════════════════════════
   FORM CARD
══════════════════════════ */
.mn-form-card{
    background:#fff;
    border-radius:20px;
    border:1.8px solid #f0d0e4;
    overflow:hidden;
    margin-bottom:1.2rem;
    box-shadow:0 4px 20px rgba(233,30,140,0.07);
}
.mn-form-head{
    display:flex;align-items:center;gap:10px;
    padding:1rem 1.4rem;
    border-bottom:1px solid #fce4f0;
    background:#fdf5f9;
}
.mn-form-head i{color:#e91e8c;font-size:18px;}
.mn-form-head span{font-size:0.95rem;font-weight:700;color:#1a1a2e;}
.mn-form-body{padding:1.2rem 1.4rem;}

/* override viser form inputs */
.mn-form-body .form-group{margin-bottom:1rem;}
.mn-form-body label{
    display:block;font-size:0.75rem;font-weight:700;
    color:#888;margin-bottom:0.4rem;
    text-transform:uppercase;letter-spacing:0.06em;
}
.mn-form-body input,
.mn-form-body textarea,
.mn-form-body select{
    width:100%;
    padding:12px 14px;
    background:#fdf5f9 !important;
    border:1.8px solid #f0d0e4 !important;
    border-radius:12px !important;
    color:#333 !important;
    font-size:0.92rem !important;
    font-family:'Poppins',sans-serif !important;
    outline:none !important;
    transition:border-color 0.25s,box-shadow 0.25s !important;
    -webkit-text-fill-color:#333 !important;
}
.mn-form-body input:focus,
.mn-form-body textarea:focus,
.mn-form-body select:focus{
    border-color:#e91e8c !important;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09) !important;
    background:#fff !important;
}
.mn-form-body input::placeholder,
.mn-form-body textarea::placeholder{
    color:#c9b3c0 !important;
    -webkit-text-fill-color:#c9b3c0 !important;
    opacity:1;
    font-family:'Noto Nastaliq Urdu','Poppins',serif;
    font-weight:400;
}
.mn-urdu{
    direction:rtl;text-align:right;
    font-family:'Noto Nastaliq Urdu','Poppins',serif;
    background:#fdf6f9;border:1px solid #f3d3e4;border-radius:14px;
    padding:.9rem 1rem;margin-bottom:1rem;
    color:#4a3042;line-height:2;font-size:.95rem;
}
.mn-form-body input[type=file]{
    padding:14px !important;
    border-style:dashed !important;
    background:#fff !important;
    height:auto !important;
    line-height:1.4 !important;
    cursor:pointer;
}
.mn-errors{
    background:#fff1f2;border:1.5px solid #fecdd3;color:#9f1239;
    border-radius:14px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.84rem;
}
.mn-errors p{margin:0 0 .25rem;}
.mn-errors p:last-child{margin:0;}
.mn-shot{
    position:relative;display:block;min-height:148px;
    border:1.8px dashed #f0d0e4;border-radius:14px;background:#fff;
    overflow:hidden;cursor:pointer;
}
.mn-shot input[type=file]{
    position:absolute !important;inset:0 !important;width:100% !important;height:100% !important;
    opacity:0 !important;cursor:pointer !important;padding:0 !important;border:none !important;
}
.mn-shot-placeholder{
    min-height:148px;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:6px;color:#b07a96;font-size:.86rem;font-weight:600;text-align:center;padding:1rem;
    pointer-events:none;
}
.mn-shot-preview{
    display:none;width:100%;max-height:240px;object-fit:contain;background:#fff;pointer-events:none;
}
.mn-shot.has-preview .mn-shot-placeholder{display:none;}
.mn-shot.has-preview .mn-shot-preview{display:block;}
.mn-shot-hint{display:block;margin-top:.4rem;font-size:.75rem;color:#a07;}
.mn-form-body textarea{min-height:90px;resize:vertical;}

/* ── Buttons ── */
.mn-btns{display:flex;gap:10px;margin-top:1.4rem;}

.mn-btn-cancel{
    flex:1;padding:13px;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:12px;
    color:#e91e8c;font-size:0.92rem;font-weight:600;
    font-family:'Poppins',sans-serif;cursor:pointer;
    text-align:center;text-decoration:none;
    transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:7px;
}
.mn-btn-cancel:hover{background:#fce4f0;border-color:#e91e8c;}

.mn-btn-submit{
    flex:2;padding:13px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:12px;
    color:#fff;font-size:0.92rem;font-weight:700;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 5px 20px rgba(233,30,140,0.28);
    display:flex;align-items:center;justify-content:center;gap:7px;
}
.mn-btn-submit:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 8px 28px rgba(233,30,140,0.38);
}

/* ── Notice ── */
.mn-notice{
    background:#fffbeb;
    border:1.5px solid #fde68a;
    border-radius:16px;
    padding:1rem 1.3rem;
    display:flex;gap:12px;align-items:flex-start;
}
.mn-notice i{color:#f59e0b;font-size:18px;flex-shrink:0;margin-top:1px;}
.mn-notice ul{font-size:0.83rem;color:#92400e;line-height:1.8;padding-left:0;list-style:none;}
.mn-notice ul li::before{content:'• ';color:#f59e0b;font-weight:700;}
</style>
@endpush

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;600&display=swap" rel="stylesheet">

<div class="mn-wrap">

    {{-- Title --}}
    <div class="mn-title">
        <h2>Deposit</h2>
        <p>Complete your deposit in seconds</p>
    </div>

    {{-- ── Summary card ── --}}
    <div class="mn-summary">
        <div class="mn-summary-row">
            <span class="lbl">Amount</span>
            <span class="val">Rs {{ showAmount($data->amount) }}</span>
        </div>
        @if($data->charge > 0)
        <div class="mn-summary-row">
            <span class="lbl">Processing Fee</span>
            <span class="val">Rs {{ showAmount($data->charge) }}</span>
        </div>
        <div class="mn-summary-row">
            <span class="lbl">Total Payable</span>
            <span class="val">Rs {{ showAmount($data->amount + $data->charge) }}</span>
        </div>
        @endif
        <div class="mn-summary-row">
            <span class="lbl">Payment Method</span>
            <span class="val dark">{{ optional($data->gateway)->name ?? (optional($method)->name ?? 'Manual Payment') }}</span>
        </div>
    </div>

    {{-- ── Payment Details card ── --}}
    @php
        $desc = optional($data->gateway)->description ?? (optional($gateway)->description ?? '');
        // Try to extract bank name and account number from description
        preg_match('/(?:bank\s*name|bank)[:\s]+([^\n<]+)/i', strip_tags($desc), $bankMatch);
        preg_match('/(?:account\s*(?:number|no|#)|number)[:\s]+([0-9\s\-]+)/i', strip_tags($desc), $accMatch);
    @endphp

    <div class="mn-details">
        <div class="mn-details-head">
            <i class="fas fa-university"></i>
            <span>Payment Details</span>
        </div>

        @if(!empty($bankMatch[1]))
        <div class="mn-detail-item">
            <div class="di-label">Bank Name</div>
            <div class="di-row">
                <div class="di-val">{{ trim($bankMatch[1]) }}</div>
            </div>
        </div>
        @endif

        @if(!empty($accMatch[1]))
        <div class="mn-detail-item">
            <div class="di-label">Account Number</div>
            <div class="di-row">
                <div class="di-val" id="acc-num">{{ trim($accMatch[1]) }}</div>
                <button type="button" class="mn-copy-btn" id="copyAccBtn" onclick="copyText('acc-num','copyAccBtn')" title="Copy">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        @endif

        {{-- Full instructions if no structured data extracted --}}
        @if(empty($bankMatch[1]) && empty($accMatch[1]))
        <div class="mn-detail-item">
            <div class="di-label">Instructions</div>
            <div style="font-size:0.88rem;color:#555;line-height:1.7;padding-top:4px;">
                {!! $desc !!}
            </div>
        </div>
        @endif
    </div>

    {{-- ── Full instructions card (if structured data was extracted) ── --}}
    @if(!empty($bankMatch[1]) || !empty($accMatch[1]))
    <div class="mn-instr">
        <div class="mn-instr-head">
            <i class="fas fa-info-circle"></i>
            <span>Payment Instructions</span>
        </div>
        <div class="mn-instr-body">
            {!! $desc !!}
        </div>
    </div>
    @endif

    {{-- ── Submit proof form ── --}}
    <div class="mn-form-card">
        <div class="mn-form-head">
            <i class="fas fa-file-upload"></i>
            <span>Submit Payment Proof</span>
        </div>
        <div class="mn-form-body">
            <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data" id="paymentProofForm">
                @csrf

                @if ($errors->any())
                    <div class="mn-errors">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @php $manualFields = prepareManualDepositFields($gateway ? optional($gateway->form)->form_data : []); @endphp

                <div class="mn-urdu">
                    ادائیگی کی تصدیق کے لیے براہِ کرم رقم بھیجنے والے اکاؤنٹ کی درست معلومات درج کریں۔ Sender Name میں رقم بھیجنے والے کا نام اور Sender Number میں وہ موبائل نمبر درج کریں جس سے رقم ادا کی گئی ہے۔ اس کے بعد Payment Screenshot لازمی Upload کرکے اپنی Payment Submit کریں۔
                </div>

                @if($manualFields['needs_sender_name'])
                    <div class="form-group">
                        <label>Sender Name <span style="color:#e91e8c;">*</span></label>
                        <input type="text" name="sender_name" value="{{ old('sender_name') }}" placeholder="یہاں رقم بھیجنے والے کا نام لکھیں" required>
                    </div>
                @endif

                @if($manualFields['needs_sender_number'])
                    <div class="form-group">
                        <label>Sender Number <span style="color:#e91e8c;">*</span></label>
                        <input type="text" name="sender_number" value="{{ old('sender_number') }}" placeholder="یہاں وہ نمبر لکھیں جس سے رقم بھیجی گئی ہے" required>
                    </div>
                @endif

                @foreach($manualFields['fields'] as $field)
                    <div class="form-group">
                        <label>{{ __($field->name) }} @if(($field->is_required ?? '') === 'required')<span style="color:#e91e8c;">*</span>@endif</label>
                        @if(($field->type ?? '') === 'textarea')
                            <textarea name="{{ $field->label }}" @if(($field->is_required ?? '') === 'required') required @endif>{{ old($field->label) }}</textarea>
                        @elseif(($field->type ?? '') === 'file')
                            @php
                                $shotAccept = collect(explode(',', manualFieldExtensions($field)))
                                    ->map(fn ($ext) => '.' . trim($ext))
                                    ->filter()
                                    ->implode(',');
                            @endphp
                            <label class="mn-shot">
                                <input type="file" name="{{ $field->label }}" @if(($field->is_required ?? '') === 'required') required @endif accept="{{ $shotAccept }},image/jpeg,image/png,image/webp">
                                <img class="mn-shot-preview" alt="Uploaded file preview">
                                <span class="mn-shot-placeholder">
                                    <i class="fas fa-image"></i>
                                    Tap to upload
                                    <small style="font-weight:500;">JPG, PNG or WEBP, up to 8MB</small>
                                </span>
                            </label>
                            <small class="mn-shot-hint"></small>
                        @elseif(($field->type ?? '') === 'select')
                            <select name="{{ $field->label }}" @if(($field->is_required ?? '') === 'required') required @endif>
                                <option value="">@lang('Select One')</option>
                                @foreach(manualFieldOptions($field) as $option)
                                    <option value="{{ $option }}">{{ __($option) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" name="{{ $field->label }}" value="{{ old($field->label) }}" placeholder="{{ manualFieldPlaceholder($field) }}" @if(($field->is_required ?? '') === 'required') required @endif>
                        @endif
                    </div>
                @endforeach

                @if($manualFields['needs_screenshot'])
                    <div class="form-group">
                        <label>Payment Screenshot <span style="color:#e91e8c;">*</span></label>
                        <label class="mn-shot" id="paymentShotBox">
                            <input type="file" id="payment_screenshot" name="payment_screenshot" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                            <img class="mn-shot-preview" id="paymentShotPreview" alt="Payment screenshot preview">
                            <span class="mn-shot-placeholder">
                                <i class="fas fa-image"></i>
                                Tap to upload payment screenshot
                                <small style="font-weight:500;">JPG, PNG or WEBP, up to 8MB</small>
                            </span>
                        </label>
                        <small class="mn-shot-hint" id="paymentShotHint"></small>
                    </div>
                @endif

                <div class="mn-btns">
                    <a href="{{ route('user.deposit.history') }}" class="mn-btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="mn-btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Notice ── --}}
    <div class="mn-notice">
        <i class="fas fa-exclamation-triangle"></i>
        <ul>
            <li>Send the exact amount shown above</li>
            <li>Enter the name of the person who sent the payment</li>
            <li>Enter the mobile number the payment was sent from</li>
            <li>Upload a clear payment screenshot</li>
            <li>Deposit will be activated after verification</li>
        </ul>
    </div>

</div>

<script>
(function () {
    var form = document.getElementById('paymentProofForm');

    document.querySelectorAll('.mn-shot input[type=file]').forEach(function (input) {
        var box = input.closest('.mn-shot');
        var preview = box ? box.querySelector('.mn-shot-preview') : null;
        var hint = box ? box.parentElement.querySelector('.mn-shot-hint') : null;
        if (!box || !preview) return;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            box.classList.remove('has-preview');
            preview.removeAttribute('src');
            if (hint) hint.textContent = '';
            if (!file) return;

            var allowed = ['image/jpeg', 'image/png', 'image/webp'];
            var name = (file.name || '').toLowerCase();
            if (name.endsWith('.heic') || name.endsWith('.heif') || file.type === 'image/heic' || file.type === 'image/heif') {
                input.value = '';
                if (hint) hint.textContent = 'iPhone HEIC photos are not supported. Save the screenshot as JPG or PNG, then upload it.';
                return;
            }
            if (allowed.indexOf(file.type) === -1 && !/\.(jpe?g|png|webp)$/.test(name)) {
                input.value = '';
                if (hint) hint.textContent = 'Please choose a JPG, PNG, or WEBP screenshot.';
                return;
            }
            if (file.size > 8 * 1024 * 1024) {
                input.value = '';
                if (hint) hint.textContent = 'This screenshot is larger than 8MB. Choose a smaller image.';
                return;
            }

            var reader = new FileReader();
            reader.onload = function () {
                preview.src = reader.result;
                box.classList.add('has-preview');
            };
            reader.readAsDataURL(file);
        });
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            var missing = false;
            form.querySelectorAll('.mn-shot input[type=file][required]').forEach(function (fileInput) {
                if (fileInput.files && fileInput.files.length) return;
                missing = true;
                var hint = fileInput.closest('.form-group');
                hint = hint ? hint.querySelector('.mn-shot-hint') : null;
                if (hint) hint.textContent = 'Please upload the payment screenshot before submitting.';
            });
            if (missing) {
                event.preventDefault();
                return;
            }
            var button = form.querySelector('.mn-btn-submit');
            if (button) {
                button.disabled = true;
                button.textContent = 'Submitting...';
            }
        });
    }
})();

function copyText(elId, btnId) {
    const text = document.getElementById(elId).innerText.trim();
    navigator.clipboard.writeText(text).then(function () {
        const btn = document.getElementById(btnId);
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(function () {
            btn.classList.remove('copied');
            btn.innerHTML = '<i class="fas fa-copy"></i>';
        }, 2000);
    });
}

</script>

@endsection
