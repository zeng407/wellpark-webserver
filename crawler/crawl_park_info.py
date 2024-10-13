import requests
import time
import json
from datetime import datetime
import os

def fetch_data():
    url = "https://hispark.hccg.gov.tw/OpenData/GetParkInfo?1111104155049"
    response = requests.get(url, verify=False)  # Disable SSL verification
    if response.status_code == 200:
        return response.json()
    else:
        print(f"Failed to fetch data: {response.status_code}")
        return None

def save_data(data):
    # Ensure the output directory exists
    output_dir = "output"
    os.makedirs(output_dir, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d%H%M%S")
    filename = os.path.join(output_dir, f"park_info_{timestamp}.json")
    with open(filename, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
    print(f"Data saved to {filename}")

def main():
    while True:
        data = fetch_data()
        if data:
            save_data(data)
        time.sleep(300)  # Sleep for 5 minutes

if __name__ == "__main__":
    main()