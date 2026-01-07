import sys
import yagmail

def send_confirmation():
    # Ensure all arguments are passed from PHP
    if len(sys.argv) < 5:
        return

    receiver_email = sys.argv[1]
    client_name = sys.argv[2]
    service_name = sys.argv[3]
    appointment_date = sys.argv[4]

    # Initialize Yagmail with your credentials
    # App Password: nbtg nkmh wbto okcl
    yag = yagmail.SMTP('takeupfunky@gmail.com', 'nbtg nkmh wbto okcl')

    subject = f"Booking Confirmed: {service_name} at SalonOS"

    # Designer HTML Template
    contents = f"""
    <html>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8fafc; color: #1e293b;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
            <div style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 30px; text-align: center;">
                <h1 style="margin: 0; color: #ffffff; letter-spacing: 3px; font-style: italic;">SALONOS</h1>
            </div>
            <div style="padding: 40px;">
                <h2 style="color: #6366f1;">Hello {client_name},</h2>
                <p>Your appointment for <strong>{service_name}</strong> is confirmed!</p>
                <div style="background: #f1f5f9; padding: 20px; border-radius: 15px; margin: 20px 0;">
                    <p style="margin: 0; font-size: 14px; color: #64748b;">Scheduled Date & Time:</p>
                    <p style="margin: 5px 0 0; font-size: 18px; font-weight: bold; color: #1e293b;">{appointment_date}</p>
                </div>
                <p style="font-size: 14px; color: #94a3b8;">We look forward to seeing you. Please arrive 10 minutes early.</p>
            </div>
            <div style="background: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; font-size: 12px; color: #94a3b8;">&copy; 2026 SalonOS Intelligence</p>
            </div>
        </div>
    </body>
    </html>
    """

    try:
        yag.send(to=receiver_email, subject=subject, contents=contents)
        print("Success: Confirmation email sent.")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    send_confirmation()