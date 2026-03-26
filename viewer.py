import time
import os
import mysql.connector

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "qwer1234",
    "database": "jungjudb1",
}

def main():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    while True:
        os.system("clear")
        cursor.execute("SELECT id, random_data, recorded_at FROM sensor ORDER BY id DESC LIMIT 15;")
        rows = cursor.fetchall()

        print("=" * 45)
        print("  jungjudb1.sensor  실시간 모니터링")
        print("=" * 45)
        print(f"{'ID':>5}  {'random_data':>12}  {'recorded_at'}")
        print("-" * 45)
        for row in rows:
            print(f"{row[0]:>5}  {row[1]:>12}  {row[2]}")
        print("=" * 45)
        print("Ctrl+C 로 종료")

        time.sleep(2)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nStopped.")
