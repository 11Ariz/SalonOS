import mysql.connector
import yagmail
import datetime

# Database Configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'salon_system' # Matches your updated db name
}

def send_daily_reminders():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # Query for tomorrow's appointments
        # Filters: Status must NOT be Paid/Completed
        query = """
            SELECT 
                COALESCE(u.name, CONCAT(c.first_name, ' ', c.last_name)) AS client_name,
                COALESCE(u.email, c.email) AS client_email,
                s.service_name,
                a.appointment_date
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id AND u.role = 'customer'
            LEFT JOIN clients c ON a.client_id = c.id
            LEFT JOIN services s ON a.service_id = s.id
            WHERE DATE(a.appointment_date) = CURDATE() + INTERVAL 1 DAY
            AND UPPER(a.status) NOT IN ('COMPLETED', 'PAID')
        """
        
        cursor.execute(query)
        upcoming = cursor.fetchall()

        if not upcoming:
            print("No reminders needed for tomorrow.")
            return

        yag = yagmail.SMTP('takeupfunky@gmail.com', 'nbtg nkmh wbto okcl')

        for entry in upcoming:
            if entry['client_email']:
                name = entry['client_name']
                service = entry['service_name']
                time_str = entry['appointment_date'].strftime('%I:%M %p')
                
                subject = "Reminder: Your SalonOS Session Tomorrow"
                
                # Matching Designer Template
                body = f"""
                <html>
                <body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 20px;">
                    <div style="max-width: 500px; margin: auto; background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <div style="background: #6366f1; padding: 20px; text-align: center; color: white;">
                            <h2 style="margin: 0;">See You Tomorrow!</h2>
                        </div>
                        <div style="padding: 30px; color: #1e293b;">
                            <p>Hi {name}, just a friendly reminder of your appointment:</p>
                            <p><strong>Service:</strong> {service}</p>
                            <p><strong>Time:</strong> {time_str}</p>
                            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                            <p style="font-size: 12px; color: #94a3b8;">If you can't make it, please let us know ASAP.</p>
                        </div>
                    </div>
                </body>
                </html>
                """
                yag.send(to=entry['client_email'], subject=subject, contents=body)
                print(f"Reminder sent to {entry['client_email']}")

    except mysql.connector.Error as err:
        print(f"DB Error: {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    send_daily_reminders()