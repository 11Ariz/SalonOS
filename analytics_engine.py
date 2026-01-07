import sys, pandas as pd, mysql.connector, json, os
from fpdf import FPDF
import matplotlib.pyplot as plt
import warnings

warnings.filterwarnings("ignore")

def get_report_data(report_type, start, end, db):
    # FIXED QUERIES: Ensuring data integrity and role filtering
    queries = {
        "ServicePopularity": f"""
            SELECT s.service_name, COUNT(a.id) as total_bookings 
            FROM services s 
            JOIN appointments a ON s.id = a.service_id 
            WHERE a.appointment_date BETWEEN '{start} 00:00:00' AND '{end} 23:59:59' 
            GROUP BY s.service_name""",
            
        "RevenueTrend": f"""
            SELECT DATE(a.appointment_date) as date, SUM(s.price) as daily_revenue 
            FROM appointments a 
            JOIN services s ON a.service_id = s.id 
            WHERE UPPER(a.status) IN ('COMPLETED', 'PAID') 
            AND a.appointment_date BETWEEN '{start} 00:00:00' AND '{end} 23:59:59' 
            GROUP BY 1 ORDER BY 1 ASC""",
            
        "StylistPerformance": f"""
            SELECT u.name as stylist, SUM(s.price) as total_revenue_generated 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id 
            JOIN services s ON a.service_id = s.id 
            WHERE UPPER(a.status) IN ('COMPLETED', 'PAID') 
            AND u.role IN ('stylist', 'admin')
            AND a.appointment_date BETWEEN '{start} 00:00:00' AND '{end} 23:59:59'
            GROUP BY u.name""",
            
        "CustomerLTV": """
            SELECT 
                COALESCE(u_cust.name, CONCAT(c.first_name, ' ', c.last_name), 'Walk-in') as customer_name, 
                SUM(s.price) as lifetime_value 
            FROM appointments a 
            LEFT JOIN users u_cust ON a.user_id = u_cust.id AND u_cust.role = 'customer'
            LEFT JOIN clients c ON a.client_id = c.id
            JOIN services s ON a.service_id = s.id 
            WHERE UPPER(a.status) IN ('COMPLETED', 'PAID') 
            GROUP BY 1 ORDER BY 2 DESC""",
            
        "InventoryHealth": """
            SELECT item_name, stock_level, min_threshold, 
            CASE WHEN stock_level <= min_threshold THEN 'LOW STOCK' ELSE 'HEALTHY' END as status 
            FROM inventory""",
            
        "PeakBookingHours": f"""
            SELECT HOUR(appointment_date) as hour_24, COUNT(*) as booking_volume 
            FROM appointments 
            WHERE appointment_date BETWEEN '{start} 00:00:00' AND '{end} 23:59:59' 
            GROUP BY 1 ORDER BY 1""",
            
        "RevenueVsCost": f"""
            SELECT 
                (SELECT SUM(price) FROM appointments a JOIN services s ON a.service_id = s.id WHERE UPPER(a.status) IN ('COMPLETED', 'PAID')) as total_revenue,
                (SELECT SUM(stock_level * unit_cost) FROM inventory) as current_inventory_cost
            FROM (SELECT 1) AS dummy"""
    }
    query = queries.get(report_type, queries["ServicePopularity"])
    return pd.read_sql(query, db)

def main():
    try:
        if len(sys.argv) < 4:
            print(json.dumps({"status": "error", "message": "Missing arguments"}))
            return

        report_type, start, end = sys.argv[1], sys.argv[2], sys.argv[3]
        
        db = mysql.connector.connect(host="localhost", user="root", password="", port=3306, database="salon_system")
        df = get_report_data(report_type, start, end, db)
        
        if not os.path.exists('reports'):
            os.makedirs('reports')

        pdf = FPDF()
        pdf.add_page()
        pdf.set_font("Arial", 'B', 16)
        pdf.set_text_color(63, 70, 229)
        pdf.cell(0, 15, f"SALONOS ANALYTICS: {report_type.upper()}", ln=True, align='C')
        
        pdf.set_font("Arial", '', 10)
        pdf.set_text_color(100, 116, 139)
        pdf.cell(0, 10, f"Generated for Period: {start} to {end}", ln=True, align='C')
        pdf.ln(10)

        # ADD GRAPH FOR REVENUE TREND
        if report_type == "RevenueTrend" and not df.empty:
            plt.figure(figsize=(6, 3))
            plt.plot(df['date'].astype(str), df['daily_revenue'], marker='o', color='#6366f1', linewidth=2)
            plt.title('Daily Revenue Trend')
            plt.xticks(rotation=45)
            plt.tight_layout()
            graph_path = "reports/temp_chart.png"
            plt.savefig(graph_path)
            pdf.image(graph_path, x=40, w=130)
            pdf.ln(10)
            os.remove(graph_path)

        if df.empty:
            pdf.set_font("Arial", 'I', 12)
            pdf.cell(0, 10, "No data found for this report and period.", ln=True, align='C')
        else:
            pdf.set_fill_color(238, 242, 255)
            pdf.set_font("Arial", 'B', 10)
            col_width = 190 / len(df.columns)
            for col in df.columns:
                pdf.cell(col_width, 10, str(col).replace('_', ' ').upper(), border=1, fill=True, align='C')
            pdf.ln()

            pdf.set_font("Arial", size=9)
            pdf.set_text_color(0, 0, 0)
            for _, row in df.iterrows():
                for val in row:
                    pdf.cell(col_width, 10, str(val), border=1, align='C')
                pdf.ln()

        output_path = f"reports/{report_type}.pdf"
        pdf.output(output_path)
        print(json.dumps({"status": "success", "file": output_path}))
        
    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    main()