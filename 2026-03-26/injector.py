import random
import time
import mysql.connector
import paho.mqtt.client as mqtt

# MySQL 설정
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "qwer1234",
    "database": "jungjudb1",
}

# MQTT 설정
MQTT_BROKER = "localhost"
MQTT_PORT = 1883
MQTT_TOPIC = "temp2"

INSERT_SQL = "INSERT INTO sensor (random_data) VALUES (%s)"
INTERVAL = 2  # seconds


def main():
    # MySQL 연결
    print("Connecting to MySQL...")
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    print("MySQL connected.")

    # MQTT 연결
    mqtt_client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
    mqtt_client.connect(MQTT_BROKER, MQTT_PORT)
    mqtt_client.loop_start()
    print(f"MQTT connected. Topic: {MQTT_TOPIC}")
    print("Inserting & publishing every 2s. Press Ctrl+C to stop.\n")

    try:
        while True:
            value = random.randint(0, 500)

            # DB 저장
            cursor.execute(INSERT_SQL, (value,))
            conn.commit()

            # Node-RED로 MQTT publish
            mqtt_client.publish(MQTT_TOPIC, value)

            print(f"[DB 저장 + MQTT 발행] random_data={value}")
            time.sleep(INTERVAL)

    except KeyboardInterrupt:
        print("\nStopped.")
    finally:
        cursor.close()
        conn.close()
        mqtt_client.loop_stop()
        mqtt_client.disconnect()


if __name__ == "__main__":
    main()
