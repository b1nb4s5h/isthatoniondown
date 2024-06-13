import requests
import subprocess

# Retrieve pending URLs
response = requests.get('https://YOUR-DOMAIN.com/get_pending_urls.php', headers={'API-Token': 'some-token-here'})

if response.status_code == 200:
    pending_urls = response.json()
    for url in pending_urls:
        id = url['id']
        url = url['url']

        # Use curl to check the status of the URL
        curl_command = f"curl -s -o /dev/null -w '%{{http_code}}' {url}"
        try:
            status_code = subprocess.check_output(curl_command, shell=True).decode('utf-8').strip()
            if status_code == '200':
                status = 'UP'
            else:
                status = 'DOWN'
        except subprocess.CalledProcessError as e:
            # If curl returns a non-zero exit code, set status to DOWN
            status = 'DOWN'
        except Exception as e:
            # Catch any other exceptions and set status to DOWN
            print(f"Error running curl for ID {id}: {e}")
            status = 'DOWN'

        # Update the status using the POST method
        response = requests.post('https://YOUR-DOMAIN.com/update_status.php', headers={'API-Token': 'some-token-here', 'Content-Type': 'application/x-www-form-urlencoded'}, data={'id': id, 'status': status})

        if response.status_code == 200:
            print(f"Updated status for ID {id} to {status}")
        else:
            print(f"Error updating status for ID {id}: {response.text}")
else:
    print("Error retrieving pending URLs:", response.text)
