// ==========================
// SUBMENU TOGGLE WITH ANIMATION
// ==========================
function toggleSubmenu(el) {
    const submenu = el.querySelector('.submenu');
    document.querySelectorAll('.has-submenu').forEach(item => {
        if (item !== el) {
            item.classList.remove('active');
            const sm = item.querySelector('.submenu');
            if (sm) sm.style.maxHeight = null;
        }
    });
    el.classList.toggle('active');
    if (el.classList.contains('active') && submenu) {
        submenu.style.maxHeight = submenu.scrollHeight + "px";
    } else if (submenu) {
        submenu.style.maxHeight = null;
    }
}

// ==========================
// SHOW SECTION
// ==========================
function showSection(id, el) {
    document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
    const section = document.getElementById(id);
    if(section) section.classList.add('active');
    if(el) el.classList.add('active');
}

// ==========================
// LOAD DASHBOARD
// ==========================
async function loadDashboard() {
    try {
        const res = await fetch('/dashboard-data');
        const data = await res.json();
        document.getElementById('totalStudents').innerText = data.total_students || 0;
        document.getElementById('presentToday').innerText = data.present_today || 0;
        document.getElementById('absentToday').innerText = data.absent_today || 0;
        renderAttendanceChart(data.total_students || 0, data.present_today || 0);
    } catch(err) {
        console.error("Dashboard load error:", err);
    }
}

// ==========================
// ATTENDANCE CHART
// ==========================
function renderAttendanceChart(total, present) {
    const absent = total - present;
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if(window.attendanceChartInstance) window.attendanceChartInstance.destroy();
    window.attendanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                label: 'Today Attendance',
                data: [present, absent],
                backgroundColor: ['#4fc3ff','#ff5c5c'],
                borderColor: ['#4fc3ff','#ff5c5c'],
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero:true, ticks: { stepSize: Math.ceil(total/10) } }
            }
        }
    });
}

// ==========================
// RENDER STUDENTS
// ==========================
async function renderStudents() {
    try {
        const res = await fetch('/students');
        const students = await res.json();
        let html = `<table>
            <thead><tr><th>ID</th><th>Name</th><th>Class</th><th>Email</th></tr></thead><tbody>`;
        students.forEach(s => html += `<tr>
            <td>${s.id}</td>
            <td>${s.name}</td>
            <td>${s.class}</td>
            <td>${s.email}</td>
        </tr>`);
        html += `</tbody></table>`;
        document.getElementById('student-info').innerHTML = html;
    } catch(err) {
        console.error("Students load error:", err);
    }
}

// ==========================
// RENDER ATTENDANCE
// ==========================
async function renderAttendance() {
    try {
        const res = await fetch('/attendance');
        const data = await res.json();
        let html = `<table>
            <thead><tr><th>ID</th><th>Name</th><th>Class</th><th>Status</th></tr></thead><tbody>`;
        data.forEach(d => {
            html += `<tr>
                <td>${d.id}</td>
                <td>${d.name}</td>
                <td>${d.class}</td>
                <td>
                    <select class="attendance-select" data-id="${d.id}">
                        <option value="">Select</option>
                        <option value="Present" ${d.present?'selected':''}>Present</option>
                        <option value="Absent" ${!d.present?'selected':''}>Absent</option>
                    </select>
                </td>
            </tr>`;
        });
        html += `</tbody></table>
            <div style="text-align:center; margin-top:10px;">
                <button class="btn" onclick="submitAttendance()">Submit Attendance</button>
            </div>`;
        document.getElementById('attendance-info').innerHTML = html;
    } catch(err) {
        console.error("Attendance load error:", err);
    }
}

// ==========================
// SUBMIT ATTENDANCE
// ==========================
async function submitAttendance() {
    try {
        const selects = document.querySelectorAll('.attendance-select');
        const data = Array.from(selects).map(s => ({
            id: s.dataset.id,
            status: s.value || "Absent"
        }));
        const res = await fetch('/submit-attendance', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(data)
        });
        const result = await res.json();
        if(result.success){
            alert('Attendance submitted successfully!');
            loadDashboard();
            renderAttendance(); // refresh attendance table
        } else {
            alert('Error: ' + result.error);
        }
    } catch(err) {
        console.error("Attendance submit error:", err);
    }
}

// ==========================
// RENDER MARKS
// ==========================
async function renderMarks() {
    try {
        const res = await fetch('/marks');
        const data = await res.json();
        let html = `<table>
            <thead><tr><th>ID</th><th>Name</th><th>Subject</th><th>Marks</th></tr></thead><tbody>`;
        data.forEach(m => html += `<tr>
            <td>${m.id}</td>
            <td>${m.name}</td>
            <td>${m.subject}</td>
            <td>${m.marks}</td>
        </tr>`);
        html += `</tbody></table>`;
        document.getElementById('marks-info').innerHTML = html;
    } catch(err) {
        console.error("Marks load error:", err);
    }
}

// ==========================
// ADD STUDENT
// ==========================
async function addStudent() {
    try {
        const name = document.getElementById('studentName').value.trim();
        const cls = document.getElementById('studentClass').value.trim();
        const email = document.getElementById('studentEmail').value.trim();
        if(!name || !cls || !email){ alert("Please fill all fields"); return; }
        const res = await fetch('/add-student',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({name, class:cls, email})
        });
        const data = await res.json();
        if(data.success){
            alert('Student added successfully!');
            document.getElementById('studentName').value='';
            document.getElementById('studentClass').value='';
            document.getElementById('studentEmail').value='';
            renderStudents();
            loadDashboard();
        } else alert('Error: ' + data.error);
    } catch(err) {
        console.error("Add student error:", err);
    }
}

// ==========================
// INITIALIZE
// ==========================
window.onload = function(){
    loadDashboard();
    renderStudents();
    renderAttendance();
    renderMarks();
};
