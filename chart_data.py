import pandas as pd
import mysql.connector
import json
from datetime import datetime, timedelta
import sys
import warnings
warnings.filterwarnings("ignore")

def get_data():
    try:
        db = mysql.connector.connect(
            host="localhost",
            user="root",
            port=3306, # Port 3308 as per your setup
            password="", 
            database="salon_system"
        )
        
        # Look back 7 days from today
        end_date = datetime.now()
        start_date = end_date - timedelta(days=6)
        
        # Query: Only sum revenue for COMPLETED or PAID status
        query = f"""
            SELECT DATE(a.appointment_date) as date, SUM(s.price) as revenue
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE UPPER(a.status) IN ('COMPLETED', 'PAID') 
            AND a.appointment_date >= '{start_date.strftime('%Y-%m-%d')} 00:00:00'
            GROUP BY 1 ORDER BY 1 ASC
        """
        
        df = pd.read_sql(query, db)
        db.close()

        # Create a full 7-day range to ensure no gaps in the chart
        all_days = pd.date_range(start=start_date, end=end_date).date
        df['date'] = pd.to_datetime(df['date']).dt.date
        df = df.set_index('date').reindex(all_days, fill_value=0).reset_index()

        # Format output for Chart.js
        payload = {
            "labels": [d.strftime('%a') for d in df['index']], # 'Mon', 'Tue', etc.
            "values": [float(v) for v in df['revenue']]
        }
        print(json.dumps(payload))

    except Exception as e:
        print(json.dumps({"error": str(e)}))

if __name__ == "__main__":
    get_data()