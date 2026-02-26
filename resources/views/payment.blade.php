<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment QR</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body{font-family:system-ui,Segoe UI,Arial;margin:0;background:#f6f7fb}
    .wrap{max-width:980px;margin:40px auto;padding:0 16px}
    .card{background:#fff;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,.08);padding:18px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    label{font-size:13px;color:#444}
    input,select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:10px;outline:none}
    button{padding:10px 14px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer}
    button:disabled{opacity:.6;cursor:not-allowed}
    .row{display:flex;gap:10px;align-items:end}
    .qrbox{display:flex;align-items:center;justify-content:center;min-height:300px;border:1px dashed #d8d8d8;border-radius:14px;background:#fafafa}
    .status{padding:10px 12px;border-radius:10px;background:#fff3cd;color:#7a5d00;border:1px solid #ffe69c}
    .ok{background:#d1e7dd;color:#0f5132;border-color:#badbcc}
    .err{background:#f8d7da;color:#842029;border-color:#f5c2c7}
    code{background:#f1f1f1;padding:2px 6px;border-radius:6px}
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h2 style="margin:6px 0 12px">KHQR Payment</h2>

    <div class="grid">
      <!-- Left: Form -->
      <div>
        <div class="row">
          <div style="flex:1">
            <label>Amount (optional)</label>
            <input id="amount" type="number" step="0.01" placeholder="10.50">
          </div>
          <div style="width:160px">
            <label>Currency</label>
            <select id="currency">
              <option value="USD">USD</option>
              <option value="KHR">KHR</option>
            </select>
          </div>
        </div>

        <div style="margin-top:10px">
          <label>Bill Number (optional)</label>
          <input id="billNumber" type="text" placeholder="INV-001">
        </div>

        <div style="margin-top:14px" class="row">
          <button id="btnGenerate">Generate QR</button>
          <button id="btnStop" disabled style="background:#111827">Stop Checking</button>
        </div>

        <div id="msg" style="margin-top:14px" class="status">
          Fill the form and click <b>Generate QR</b>.
        </div>

        <div style="margin-top:10px;font-size:13px;color:#666">
          MD5: <code id="md5Text">-</code>
        </div>
      </div>

      <!-- Right: QR -->
      <div>
        <div class="qrbox">
          <img id="qrImg" alt="QR Code" style="max-width:260px;display:none" />
          <div id="qrPlaceholder" style="color:#777">QR will appear here</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  const amountEl = document.getElementById('amount');
  const currencyEl = document.getElementById('currency');
  const billEl = document.getElementById('billNumber');

  const btnGenerate = document.getElementById('btnGenerate');
  const btnStop = document.getElementById('btnStop');

  const msg = document.getElementById('msg');
  const md5Text = document.getElementById('md5Text');

  const qrImg = document.getElementById('qrImg');
  const qrPlaceholder = document.getElementById('qrPlaceholder');

  let md5 = null;
  let timer = null;

  function setStatus(text, type='warn'){
    msg.className = 'status';
    if(type === 'ok') msg.classList.add('ok');
    if(type === 'err') msg.classList.add('err');
    msg.innerHTML = text;
  }

  function showQR(dataUrl){
    qrPlaceholder.style.display = 'none';
    qrImg.style.display = 'block';
    qrImg.src = dataUrl;
  }

  function stopChecking(){
    if(timer) clearInterval(timer);
    timer = null;
    btnStop.disabled = true;
  }

  async function generateQR(){
    stopChecking();
    md5 = null;
    md5Text.textContent = '-';
    qrImg.style.display = 'none';
    qrPlaceholder.style.display = 'block';

    btnGenerate.disabled = true;
    setStatus('Generating QR...', 'warn');

    const payload = {
      currency: currencyEl.value,
      billNumber: billEl.value || undefined,
    };

    const amountVal = amountEl.value.trim();
    if(amountVal !== '') payload.amount = Number(amountVal);

    try{
      const res = await fetch("{{ route('generate') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload)
      });

      const json = await res.json();

      if(!res.ok || !json.success){
        throw new Error(json.message || 'Generate failed');
      }

      md5 = json.data.md5Hash;
      md5Text.textContent = md5;

      showQR(json.data.qrCodeImage);
      setStatus('QR generated. Please scan and pay. Checking payment...', 'warn');

      btnStop.disabled = false;
      startChecking();
    }catch(e){
      setStatus('Error: ' + e.message, 'err');
    }finally{
      btnGenerate.disabled = false;
    }
  }

  async function checkPayment(){
    if(!md5) return;

    try{
      const res = await fetch("{{ route('check') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ md5 })
      });

      const json = await res.json();

      if(!res.ok || !json.success){
        throw new Error(json.message || 'Check failed');
      }

      if(json.data.paid === true){
        setStatus('PAID ✅ Transaction: <code>' + (json.data.transaction?.hash || '-') + '</code>', 'ok');
        stopChecking();
      }else{
        setStatus('Not paid yet... still checking.', 'warn');
      }
    }catch(e){
      setStatus('Error: ' + e.message, 'err');
      // keep checking unless you want to stop on error
    }
  }

  function startChecking(){
    stopChecking();
    // check immediately then every 3 seconds
    checkPayment();
    timer = setInterval(checkPayment, 3000);
  }

  btnGenerate.addEventListener('click', generateQR);
  btnStop.addEventListener('click', () => {
    stopChecking();
    setStatus('Stopped checking. You can generate again.', 'warn');
  });
</script>
</body>
</html>
