import random
import time
import paho.mqtt.client as mqtt

BROKER = "localhost"
PORT = 1883
TOPIC = "temp2"
INTERVAL = 2  # seconds


def on_connect(client, userdata, flags, reason_code, properties):
    if reason_code == 0:
        print(f"Connected to MQTT broker at {BROKER}:{PORT}")
    else:
        print(f"Connection failed with code {reason_code}")


def main():
    client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
    client.on_connect = on_connect

    client.connect(BROKER, PORT)
    client.loop_start()

    print(f"Publishing random values (0~500) to topic '{TOPIC}' every {INTERVAL}s")
    print("Press Ctrl+C to stop.\n")

    try:
        while True:
            value = random.randint(0, 500)
            result = client.publish(TOPIC, value)
            result.wait_for_publish()
            print(f"Published: {value}")
            time.sleep(INTERVAL)
    except KeyboardInterrupt:
        print("\nStopped.")
    finally:
        client.loop_stop()
        client.disconnect()


if __name__ == "__main__":
    main()
