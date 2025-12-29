from http.server import HTTPServer, SimpleHTTPRequestHandler
import json
from urllib.parse import parse_qs
from db import get_connection
from datetime import date

class Handler(SimpleHTTPRequestHandler):
    def _send_json(self, data):
        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self.end_headers()
        self.wfile.write(json.dumps(data).encode())

    # GET requests
    def do_GET(self):
        if self.path.startswith("/dashboard-data"):
            self.dashboard_data()
        elif self.path.startswith("/students"):
            self.students_data()
        elif self.path.startswith("/attendance"):
            self.attendance_data()
        elif self.path.startswith("/marks"):
            self.marks_data()
        else:
            super().do_GET()

    # POST requests
    def do_POST(self):
        if self.path.startswith("/add-student"):
            length = int(self.headers.get('Content-Length'))
            body = self.rfile.read(length)
            data = json.loads(body)
            self.add_student(data)
        elif self.path.startswith("/submit-attendance"):
            length = int(self.headers.get('Content-Length'))
            body = self.rfile.read(length)
            data = json.loads(body)
            self.submit_attendance(data)
        else:
            super().do_POST()

    # Dashboard data
    def dashboard_data(self):
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM Students")
        total_students = cursor.fetchone()[0]

        cursor.execute("SELECT COUNT(*) FROM Attendance WHERE present=1 AND date=CAST(GETDATE() AS DATE)")
        present_today = cursor.fetchone()[0]

        conn.close()

        self._send_json({
            "total_students": total_students,
            "present_today": present_today,
            "absent_today": total_students - present_today
        })

    # Students list
    def students_data(self):
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT id, name, class, email FROM Students")
        rows = cursor.fetchall()
        conn.close()
        students = [{"id": r[0], "name": r[1], "class": r[2], "email": r[3]} for r in rows]
        self._send_json(students)

    # Attendance list
    def attendance_data(self):
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT s.id, s.name, s.class, a.present
            FROM Attendance a
            JOIN Students s ON a.student_id = s.id
            WHERE a.date = ?
        """, date.today())
        rows = cursor.fetchall()
        conn.close()
        attendance = [{"id": r[0], "name": r[1], "class": r[2], "present": bool(r[3])} for r in rows]
        self._send_json(attendance)

    # Marks list
    def marks_data(self):
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT s.id, s.name, m.subject, m.marks
            FROM Marks m
            JOIN Students s ON m.student_id = s.id
        """)
        rows = cursor.fetchall()
        conn.close()
        marks = [{"id": r[0], "name": r[1], "subject": r[2], "marks": r[3]} for r in rows]
        self._send_json(marks)

    # Add student
    def add_student(self, data):
        name = data.get("name")
        cls = data.get("class")
        email = data.get("email")
        if not name or not cls or not email:
            self._send_json({"success": False, "error": "Missing fields"})
            return
        try:
            conn = get_connection()
            cursor = conn.cursor()
            cursor.execute("INSERT INTO Students (name, class, email) VALUES (?, ?, ?)", name, cls, email)
            conn.commit()
            conn.close()
            self._send_json({"success": True})
        except Exception as e:
            self._send_json({"success": False, "error": str(e)})

    # Submit attendance
    def submit_attendance(self, data):
        try:
            conn = get_connection()
            cursor = conn.cursor()
            for entry in data:
                student_id = entry['id']
                present = 1 if entry['status'] == "Present" else 0
                # Check if attendance already exists
                cursor.execute("SELECT id FROM Attendance WHERE student_id=? AND date=CAST(GETDATE() AS DATE)", student_id)
                exists = cursor.fetchone()
                if exists:
                    cursor.execute("UPDATE Attendance SET present=? WHERE id=?", present, exists[0])
                else:
                    cursor.execute("INSERT INTO Attendance (student_id, present) VALUES (?, ?)", student_id, present)
            conn.commit()
            conn.close()
            self._send_json({"success": True})
        except Exception as e:
            self._send_json({"success": False, "error": str(e)})

if __name__ == "__main__":
    server_address = ("", 8000)
    httpd = HTTPServer(server_address, Handler)
    print("Server running at http://localhost:8000")
    httpd.serve_forever()
