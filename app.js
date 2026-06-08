document.addEventListener('DOMContentLoaded', () => {
    document.getElementById("dash")?.addEventListener("click", () => window.location='index.php');
    document.getElementById("users")?.addEventListener("click", () => window.location='users.php');
    document.getElementById("otps")?.addEventListener("click", () => window.location='otp.php');
    document.getElementById("logout")?.addEventListener("click", () => window.location='logout.php');

    if (document.getElementsByClassName("storage-bar")[0]) document.getElementsByClassName("storage-bar")[0].style="height: 2vh;background: white;width: calc(10vw * ("+document.getElementsByClassName("storage-bar")[0].dataset.percent+"));";

    document.getElementById("save-storage")?.addEventListener("click", () => {
        sendPost('manage.php', {
            id: document.getElementById("save-storage").dataset.id,
            storage: document.getElementById('storage').value
        });
    });

    document.getElementById("save-expiry")?.addEventListener("click", () => {
        sendPost('manage.php', {
            id: document.getElementById("save-expiry").dataset.id,
            expiry: document.getElementById('expiry').value
        });
    });
    
    document.querySelectorAll('.user-edit')?.forEach(img => {
        img.addEventListener('click', () => {
            submitPost('manage.php', {
                id: img.dataset.id,
                email: img.dataset.email
            });
        });
    });

    document.querySelectorAll('.otp-timer').forEach(el => {
        const expires = parseFloat(el.dataset.expires);
        setInterval(() => {
            const left = Math.round(expires - Date.now() / 1000);
            el.textContent = left > 0 ? left + 's' : 'Expired';
            if (left <= 0) el.closest('.user-cont').classList.add('expired');
        }, 1000);
    });

    if (window.location.pathname.includes("otp")){
        document.getElementById("otps").className="panel-button txt selected";
    } else if (window.location.pathname.includes("index") || window.location.pathname=="/"){
        document.getElementById("dash").className="panel-button txt selected";
    } else if (!window.location.pathname.includes("manage")) {
        document.getElementById("users").className="panel-button txt selected";
    }
});

async function sendPost(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.status === "success") {
            alert(result.message);
            if (data.storage) {
                document.getElementById('storage').placeholder = data.storage + " GB";
                document.getElementById('storage').value = "";
            }
        }
    } catch (error) {
        console.error("Error:", error);
    }
}

function submitPost(url, data) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    f.appendChild(csrf);

    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = key;
            inp.value = data[key];
            f.appendChild(inp);
        }
    }
    document.body.appendChild(f);
    f.submit();
}