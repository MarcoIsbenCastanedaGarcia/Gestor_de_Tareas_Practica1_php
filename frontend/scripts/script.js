const API_AUTH = '../../backend/apis/auth.php';
const API_TASKS = '../../backend/apis/tasks.php';

// LOGIN
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const user = document.getElementById('logUser').value;
        const pass = document.getElementById('logPass').value;
        
        const fd = new FormData();
        fd.append('action', 'login');
        fd.append('usuario', user);
        fd.append('contrasena', pass);

        const res = await fetch(API_AUTH, { method: 'POST', body: fd });
        const data = await res.json();
        
        if(data.success) {
            window.location.href = 'tasks.html';
        } else {
            alert(data.message || 'Datos incorrectos');
        }
    });
}

// REGISTER
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const user = document.getElementById('regUser').value;
        const pass = document.getElementById('regPass').value;
        
        const fd = new FormData();
        fd.append('action', 'register');
        fd.append('usuario', user);
        fd.append('contrasena', pass);

        const res = await fetch(API_AUTH, { method: 'POST', body: fd });
        const data = await res.json();
        
        if(data.success) {
            alert('Registrado exitosamente. Inicia sesión.');
            window.location.href = 'login.html';
        } else {
            alert(data.message || 'Error al registrar.');
        }
    });
}

// LOGOUT
function logout() {
    const fd = new FormData();
    fd.append('action', 'logout');
    fetch(API_AUTH, { method: 'POST', body: fd }).then(() => {
        window.location.href = '../../index.php';
    });
}

// TASKS CRUD
const taskForm = document.getElementById('taskForm');
if (taskForm) {
    loadTasks();
    
    taskForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const tName = document.getElementById('tName').value;
        const tDesc = document.getElementById('tDesc').value;
        const tMat = document.getElementById('tMat').value;
        const tDate = document.getElementById('tDate').value;
        
        await fetch(API_TASKS, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ NombreTarea: tName, descripcion: tDesc, Materia: tMat, Fecha: tDate })
        });
        taskForm.reset();
        loadTasks();
    });
}

async function loadTasks() {
    const tbody = document.querySelector('#tasksTable tbody');
    if(!tbody) return;
    tbody.innerHTML = '';
    
    const res = await fetch(API_TASKS);
    const data = await res.json();
    
    if(data.error) {
        alert(data.error);
        window.location.href = 'login.html';
        return;
    }
    
    data.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${t.NombreTarea}</td>
            <td>${t.descripcion}</td>
            <td>${t.Materia}</td>
            <td>${t.Fecha}</td>
            <td><button onclick="deleteTask(${t.id})" class="btn-danger">Eliminar</button></td>
        `;
        tbody.appendChild(tr);
    });
}

async function deleteTask(id) {
    if(confirm('¿Eliminar tarea?')) {
        await fetch(API_TASKS, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        loadTasks();
    }
}

// IMPORT / EXPORT TXT (JSON)
async function exportTasks() {
    const res = await fetch(API_TASKS);
    const data = await res.json();
    const jsonStr = JSON.stringify(data, null, 2);
    
    const blob = new Blob([jsonStr], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'registros_tareas.txt';
    a.click();
    URL.revokeObjectURL(url);
}

function importTasks() {
    const fileInput = document.getElementById('fileInput');
    if(!fileInput.files.length) {
        alert('Seleccione un archivo .txt con formato JSON.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = async (e) => {
        try {
            const tasks = JSON.parse(e.target.result);
            await fetch(API_TASKS, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'import', tasks })
            });
            alert('Tareas importadas exitosamente');
            loadTasks();
        } catch (err) {
            alert('Formato JSON inválido en el archivo txt.');
        }
    };
    reader.readAsText(fileInput.files[0]);
}

// CALENDAR
async function loadCalendar() {
    const calView = document.getElementById('calendar-view');
    if(!calView) return;
    calView.innerHTML = '';
    
    const res = await fetch(API_TASKS);
    const data = await res.json();
    
    if(data.error) {
        window.location.href = 'login.html';
        return;
    }
    
    // Sort by date
    data.sort((a,b) => new Date(a.Fecha) - new Date(b.Fecha));
    
    data.forEach(t => {
        const div = document.createElement('div');
        div.className = 'cal-card';
        div.innerHTML = `
            <h4 style="margin:0 0 10px 0; color: var(--glaucous);">${t.Fecha}</h4>
            <strong>${t.NombreTarea}</strong><br>
            <small>${t.Materia}</small>
        `;
        calView.appendChild(div);
    });
}