import requests
import time
import random

# Replace with your actual Laravel API URL
url = "http://127.0.0.1:8000/api/readings"

while True:
    data = {
        "ph": round(random.uniform(6.5, 8.0), 2),
        "turbidity": round(random.uniform(1.0, 5.0), 2),
        "temperature": round(random.uniform(20.0, 30.0), 2),
        "water_level": round(random.uniform(40.0, 60.0), 2)
    }

    try:
        response = requests.post(url, json=data)
        if response.ok:
            print(f"Sent data: {data}")
        else:
            print(f"Failed to send data: {response.status_code} {response.text}")
    except Exception as e:
        print(f"Error sending data: {e}")

    time.sleep(5)  # Wait 5 seconds before sending next data
