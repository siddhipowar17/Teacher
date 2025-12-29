import pyodbc

def get_connection():
    return pyodbc.connect(
        "DRIVER={ODBC Driver 17 for SQL Server};"
        "SERVER=localhost;"          # change if needed (DESKTOP-XXX\\SQLEXPRESS)
        "DATABASE=EduTechDB1;"
        "Trusted_Connection=yes;"
    )
