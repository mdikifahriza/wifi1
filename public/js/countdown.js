document.addEventListener("DOMContentLoaded", () => {
    const display = document.getElementById("countdown-display");
    const statusMsg = document.getElementById("status-message");

    if (typeof durationSeconds === "undefined" || !display) {
        console.error("durationSeconds atau elemen countdown-display tidak ditemukan.");
        display.textContent = "Error: Durasi tidak ditemukan";
        return;
    }

    let remaining = durationSeconds;
    let hotspotStarted = false;

    const updateDisplay = () => {
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        display.textContent = `${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
    };

    updateDisplay();

    // Jalankan hotspot otomatis saat halaman load
    statusMsg.textContent = "Menghidupkan hotspot...";
    
    fetch("/hotspot/start", { 
        method: "POST", 
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log("Hotspot berhasil dinyalakan");
            statusMsg.textContent = "Hotspot aktif - Selamat browsing!";
            hotspotStarted = true;
            startCountdown();
        } else {
            console.error("Gagal menyalakan hotspot:", data.error);
            statusMsg.textContent = "Gagal menyalakan hotspot: " + (data.error || "Unknown error");
        }
    })
    .catch(err => {
        console.error("Error saat request hotspot:", err);
        statusMsg.textContent = "Error: Tidak dapat menghubungi server";
    });

    function startCountdown() {
        const interval = setInterval(() => {
            remaining--;
            updateDisplay();

            if (remaining <= 0) {
                clearInterval(interval);
                display.textContent = "00:00";
                statusMsg.textContent = "Waktu habis. Hotspot akan dimatikan...";
                
                // Panggil stop hotspot
                fetch("/hotspot/stop", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    setTimeout(() => window.location.href = "/", 2000);
                })
                .catch(err => {
                    console.error("Gagal mematikan hotspot:", err);
                    setTimeout(() => window.location.href = "/", 2000);
                });
            }
        }, 1000);
    }
});