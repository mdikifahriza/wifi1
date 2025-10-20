const map = Object.freeze({
    'qris': '.container img'
});
const inputmap = Object.freeze({
    'qris': 'src'
});

/* ============================================================
   🧱 Modal Builder
============================================================ */
const createModal = (message = 'Memproses Pembayaran...', type = 'loading') => {
    // Modal overlay
    const modal = document.createElement('div');
    modal.id = 'payment-modal';
    modal.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    `;

    // Animations
    if (!document.getElementById('modal-animation')) {
        const style = document.createElement('style');
        style.id = 'modal-animation';
        style.textContent = `
            @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
            @keyframes slideUp { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
            @keyframes spin { 0% {transform:rotate(0deg);} 100% {transform:rotate(360deg);} }
        `;
        document.head.appendChild(style);
    }

    const statusBox = createStatusUI(message, type);
    modal.appendChild(statusBox);
    document.body.appendChild(modal);

    return modal;
};

/* ============================================================
   🎨 Status UI Builder
============================================================ */
const createStatusUI = (message, type = 'loading') => {
    const box = document.createElement('div');
    box.className = 'payment-status-container';
    box.style.cssText = `
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 40px 32px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        text-align: center;
        max-width: 400px;
        width: 90%;
        animation: slideUp 0.3s ease;
    `;

    const icon = document.createElement('div');
    icon.style.cssText = `
        width: 90px; height: 90px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 48px;
    `;

    const msg = document.createElement('div');
    msg.style.cssText = `font-size:20px; font-weight:600; line-height:1.4;`;

    const sub = document.createElement('div');
    sub.style.cssText = `font-size:14px; color:#6b7280;`;

    const btns = document.createElement('div');
    btns.style.cssText = `display:flex; flex-direction:column; gap:10px; width:100%; margin-top:12px;`;

    if (type === 'loading') {
        icon.style.background = '#dbeafe';
        icon.innerHTML = `
            <div style="
                border:5px solid #e0e7ff;
                border-top:5px solid #3b82f6;
                border-radius:50%;
                width:60px;height:60px;
                animation: spin 1s linear infinite;
            "></div>`;
        msg.textContent = message;
        sub.textContent = 'Mohon tunggu sebentar...';
    }

if (type === 'success') {
    icon.style.background = '#dcfce7';
    icon.style.color = '#16a34a';
    icon.textContent = '✓';
    msg.textContent = message;
    msg.style.color = '#16a34a';
    sub.textContent = 'Transaksi Anda berhasil diproses 🎉';

    // 🚀 Hilangkan tombol, langsung auto redirect ke countdown
    setTimeout(() => {
        window.location.href = '/countdown';
    }, 2000);
}


    if (type === 'error') {
        icon.style.background = '#fee2e2';
        icon.style.color = '#dc2626';
        icon.textContent = '✕';
        msg.textContent = message;
        msg.style.color = '#dc2626';
        sub.textContent = 'Transaksi gagal atau dibatalkan.';

        const retry = makeButton('↻ Coba Lagi', '#3b82f6', () => window.location.reload());
        const home = makeButton('🏠 Kembali ke Beranda', '#f3f4f6', () => window.location.href = '/');
        home.style.color = '#374151';
        home.style.border = '2px solid #e5e7eb';
        home.onmouseover = () => home.style.background = '#e5e7eb';
        btns.appendChild(retry);
        btns.appendChild(home);
    }

    box.append(icon, msg, sub, btns);
    return box;
};

/* ============================================================
   🧩 Utilitas kecil
============================================================ */
const makeButton = (text, bg, click) => {
    const btn = document.createElement('button');
    btn.textContent = text;
    btn.style.cssText = `
        padding:14px 28px;
        background:${bg};
        color:white;
        border:none;
        border-radius:10px;
        font-size:16px;
        font-weight:600;
        cursor:pointer;
        transition:all 0.3s ease;
        width:100%;
    `;
    btn.onmouseover = () => btn.style.filter = 'brightness(1.1)';
    btn.onmouseout = () => btn.style.filter = 'brightness(1)';
    btn.onclick = click;
    return btn;
};

const updateModalStatus = (message, type) => {
    const modal = document.getElementById('payment-modal');
    if (!modal) return;
    const oldBox = modal.querySelector('.payment-status-container');
    const newBox = createStatusUI(message, type);
    if (oldBox) oldBox.replaceWith(newBox);
};

/* ============================================================
   🔁 Polling Status Transaksi
============================================================ */
const statusChecker = function (data, resolve, reject) {
    const poll = () => {
        setTimeout(() => {
            fetch(route.poll, {
                method: "POST",
                body: JSON.stringify({ key: route.orderID }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': data.token
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(y => {
                console.log('Status pembayaran:', y);
                if (y.state === "settlement") return resolve("Pembayaran Berhasil 🎉");
                if (y.state === "canceled" || y.state === "expired") return reject("Gagal / Dibatalkan");
                poll();
            })
            .catch(err => reject(err));
        }, 3000);
    };
    poll();
};

/* ============================================================
   💳 Jalur Simulasi Pembayaran
============================================================ */
const paySimulation = async function (url, formData, resolve, reject) {
    try {
        const res = await fetch(url, {
            method: "POST",
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json().catch(() => ({}));

        if (!res.ok) throw new Error(`Gagal ${res.status}`);

        const modal = document.getElementById('payment-modal');
        updateModalStatus('Menunggu konfirmasi pembayaran...', 'loading');

        new Promise((resv, rej) => statusChecker(data, resv, rej))
            .then(msg => {
                updateModalStatus(msg, 'success');
                if (typeof resolve === 'function') resolve(msg);
            })
            .catch(err => {
                updateModalStatus('Pembayaran Gagal / Dibatalkan', 'error');
                if (typeof reject === 'function') reject(err);
            });
    } catch (e) {
        console.error(e);
        updateModalStatus('Terjadi kesalahan jaringan', 'error');
    }
};

/* ============================================================
   🧾 Jalur QRIS Payment
============================================================ */
const qrisPayment = function (elem, selector) {
    elem.innerHTML = "<div style='text-align:center;padding:20px;'>Memuat...</div>";
    fetch(route.form)
        .then(x => x.text())
        .then(x => {
            const parsed = new DOMParser().parseFromString(x, "text/html");
            elem.replaceChildren(...parsed.body.children);
        })
        .then(() => {
            const form = elem.firstChild;
            document.getElementById('qrCodeUrl').value = selector;

            form.addEventListener('submit', e => {
                e.preventDefault();

                const modal = createModal('Memproses Pembayaran...', 'loading');
                paySimulation(
                    form.action,
                    new FormData(form),
                    () => console.log('Pembayaran sukses'),
                    () => console.log('Pembayaran gagal')
                );
            });
        })
        .catch(err => {
            console.error(err);
            const modal = createModal('Gagal Memuat Form Pembayaran', 'error');
        });
};

/* ============================================================
   🚀 Entry Point
============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const paymentType = document.getElementById('tipe-pembayaran')?.innerHTML.trim();
    const selector = document.querySelector(map[paymentType])?.getAttribute(inputmap[paymentType]);
    const elem = document.getElementById('confirmation');

    if (!elem) return console.warn("Elemen #confirmation hilang");
    if (!selector) return console.warn("Tipe payment salah atau QR hilang");

    if (paymentType === 'qris') qrisPayment(elem, selector);
});
