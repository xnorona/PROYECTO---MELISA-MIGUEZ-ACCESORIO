// js/app.js

// --- 1. CONFIGURACIÓN ---
const API_URL = 'http://localhost/daw_proyecto_php_mvc/backend/public/api';
const ADMIN_USER = "admin";
const ADMIN_PASS = "memi2026";

console.log("Sistema Iniciado. Esperando a Google...");

// --- 2. CALLBACK DE GOOGLE (Llamado automáticamente por el HTML) ---
window.handleGoogleResponse = async (response) => {
    try {
        console.log("Credencial recibida de Google. Verificando en servidor...");
        
        const res = await fetch(`${API_URL}/google-login`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ token: response.credential })
        });
        
        const text = await res.text();
        try {
            const json = JSON.parse(text);
            if (json.success) {
                iniciarSesion(json.role);
            } else {
                alert("Error de acceso: " + (json.message || "Token inválido"));
            }
        } catch (err) {
            console.error("Error PHP:", text);
            alert("Error del servidor. Revisa la consola.");
        }
    } catch (e) {
        alert("Error de conexión con el servidor.");
    }
};

document.addEventListener('DOMContentLoaded', () => {

    // --- A. NAVEGACIÓN ---
    const goReg = document.getElementById('go-register');
    const goLog = document.getElementById('go-login');
    const loginSec = document.getElementById('login-section');
    const regSec = document.getElementById('register-section');

    if (goReg) goReg.addEventListener('click', (e) => {
        e.preventDefault();
        loginSec.style.display = 'none';
        regSec.style.display = 'block';
    });

    if (goLog) goLog.addEventListener('click', (e) => {
        e.preventDefault();
        regSec.style.display = 'none';
        loginSec.style.display = 'block';
    });

    // --- B. LOGIN MANUAL ---
    const logForm = document.getElementById('login-form');
    if (logForm) {
        logForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const u = document.getElementById('user').value;
            const p = document.getElementById('pass').value;

            if(u === ADMIN_USER && p === ADMIN_PASS) {
                iniciarSesion('admin');
                return;
            }

            try {
                const res = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ user: u, pass: p })
                });
                const json = await res.json();
                if(json.success) iniciarSesion(json.role);
                else alert(json.message || "Incorrecto");
            } catch(e) { alert("Error de conexión"); }
        });
    }

    // --- C. REGISTRO ---
    const regForm = document.getElementById('register-form');
    if (regForm) {
        regForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                full_name: document.getElementById('reg-name').value,
                email: document.getElementById('reg-email').value,
                username: document.getElementById('reg-user').value,
                password: document.getElementById('reg-pass').value
            };
            try {
                const res = await fetch(`${API_URL}/signup`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const json = await res.json();
                if(json.success) { alert("Registro exitoso"); goLog.click(); }
                else alert(json.message);
            } catch(e) { alert("Error de conexión"); }
        });
    }

    // --- D. GESTIÓN DE PRODUCTOS ---
    const prodForm = document.getElementById('product-form');
    if (prodForm) {
        prodForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('product-id').value;
            const url = id ? `${API_URL}/products/${id}` : `${API_URL}/products`;
            const method = id ? 'PUT' : 'POST';
            
            const data = {
                name: document.getElementById('name').value,
                price: document.getElementById('price').value,
                stock: document.getElementById('stock').value,
                category: document.getElementById('category').value,
                image: document.getElementById('image-base64').value
            };

            await fetch(url, { method: method, body: JSON.stringify(data) });
            prodForm.reset();
            document.getElementById('product-id').value = ''; 
            document.getElementById('image-base64').value = '';
            alert(id ? "Actualizado" : "Guardado");
            window.fetchProducts(); 
        });
    }

    // --- E. EXTRAS ---
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            window.showView(link.getAttribute('data-view'));
        });
    });

    // Filtros
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            const cat = e.target.getAttribute('data-category').toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const c = card.getAttribute('data-category').toLowerCase();
                card.style.display = (cat === 'todos' || c === cat) ? 'block' : 'none';
            });
        });
    });

    // Buscador
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('#products-table tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    if(localStorage.getItem('memi_session') === 'active') unlockInterface();

    const imgIn = document.getElementById('image-input');
    if(imgIn) imgIn.onchange = function() {
        const r = new FileReader();
        r.onload = (e) => document.getElementById('image-base64').value = e.target.result;
        if(this.files[0]) r.readAsDataURL(this.files[0]);
    };

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if(confirm("¿Salir?")) {
            localStorage.clear();
            location.reload();
        }
    });
});

// --- FUNCIONES GLOBALES ---
function iniciarSesion(role) {
    localStorage.setItem('memi_session', 'active');
    localStorage.setItem('user_role', role);
    unlockInterface();
}

function unlockInterface() {
    document.getElementById('lobby').style.display = 'none';
    document.getElementById('main-header').style.display = 'block';
    document.getElementById('content-area').style.display = 'block';
    document.body.classList.remove('auth-mode');
    const navInv = document.getElementById('nav-inv');
    if(navInv) navInv.style.display = (localStorage.getItem('user_role') === 'admin') ? 'block' : 'none';
    window.fetchProducts();
    window.showView('inicio');
}

window.showView = function(view) {
    document.querySelectorAll('.view').forEach(v => v.style.display = 'none');
    const target = document.getElementById('view-' + view);
    if(target) target.style.display = 'block';
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    const link = document.querySelector(`[data-view="${view}"]`);
    if(link) link.classList.add('active');
};

window.goToCatalog = () => window.showView('catalogo');

window.fetchProducts = async function() {
    try {
        const res = await fetch(`${API_URL}/products`);
        if(res.ok) {
            const data = await res.json();
            renderTable(data);
            renderCatalog(data);
        }
    } catch(e) {}
};

function renderTable(products) {
    const tbody = document.querySelector('#products-table tbody');
    if(!tbody) return;
    if(products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Sin productos</td></tr>';
        return;
    }
    tbody.innerHTML = products.map(p => `
        <tr>
            <td>${p.id}</td>
            <td><img src="${p.image || ''}" style="width:50px; height:50px; object-fit:cover; border-radius:5px;"></td>
            <td style="color:#e2e8f0; font-weight:bold;">${p.name}</td>
            <td>$${parseFloat(p.price).toFixed(2)}</td>
            <td>${p.stock}</td>
            <td>
                <button onclick="editItem(${p.id})" style="background:#3b82f6; border:none; color:white; padding:5px; border-radius:4px; margin-right:5px; cursor:pointer;"><i class="fas fa-edit"></i></button>
                <button onclick="deleteProduct(${p.id})" style="background:#ef4444; border:none; color:white; padding:5px; border-radius:4px; cursor:pointer;"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
}

function renderCatalog(products) {
    const grid = document.getElementById('catalog-grid');
    if(!grid) return;
    if(products.length === 0) {
        grid.innerHTML = '<p style="color:white; text-align:center; width:100%; grid-column: 1/-1;">No hay productos.</p>';
        return;
    }
    grid.innerHTML = products.map(p => `
        <div class="product-card" data-category="${(p.category || 'otros').toLowerCase()}" style="background: #1e293b; border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s ease;">
            <div style="width: 100%; height: 320px;">
                <img src="${p.image || './img/placeholder.jpg'}" alt="${p.name}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="padding: 16px; background: linear-gradient(to top, #1e293b 80%, transparent 100%);">
                <h3 style="color: #f8fafc; font-size: 1.1rem; font-weight: 600; margin: 0 0 8px 0;">${p.name}</h3>
                <div style="display: flex; justify-content: space-between;">
                    <p style="color: #3b82f6; font-weight: 700; font-size: 1.2rem; margin: 0;">$${parseFloat(p.price).toFixed(2)}</p>
                    <button style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;">${p.category || 'Accesorio'}</button>
                </div>
            </div>
        </div>
    `).join('');
}

window.deleteProduct = async (id) => {
    if(confirm('¿Eliminar?')) { await fetch(`${API_URL}/products/${id}`, { method: 'DELETE' }); window.fetchProducts(); }
};

window.editItem = async (id) => {
    try {
        const res = await fetch(`${API_URL}/products/${id}`);
        const p = await res.json();
        document.getElementById('product-id').value = p.id;
        document.getElementById('name').value = p.name;
        document.getElementById('price').value = p.price;
        document.getElementById('stock').value = p.stock;
        document.getElementById('category').value = p.category.toLowerCase();
        document.getElementById('image-base64').value = p.image || '';
        window.showView('inventario');
    } catch(e) {}
};