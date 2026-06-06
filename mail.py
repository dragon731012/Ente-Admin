import asyncio
from aiosmtpd.controller import Controller
import re
import os
import time
import json

port = int(os.getenv("MAIL_PORT", 1025))
otpfile = "/var/www/otps.txt"

class EnteMail:
    def __init__(self):
        self.cleanup()

    async def handle_DATA(self, server, session, envelope):
        self.cleanup()

        email_text = envelope.content.decode('utf-8', errors='ignore')
        
        recipient_email = "unknown"
        if envelope.rcpt_tos:
            recipient_email = envelope.rcpt_tos[0]

        match = re.search(r'Verification code:\s*(\d{6})', email_text)
        if match:
            otp_code = match.group(1)
            
            payload = {
                "email": recipient_email.lower().strip(),
                "code": otp_code,
                "timestamp": int(time.time())
            }
            
            current_list = self.get_codes()
            current_list.append(payload)
            self.save_codes(current_list)

        return "250 OK"

    def get_codes(self):
        if not os.path.exists(otpfile):
            return []
        try:
            with open(otpfile, 'r') as f:
                data = json.load(f)
                return data if isinstance(data, list) else []
        except Exception:
            return []

    def save_codes(self, code_list):
        try:
            with open(otpfile, 'w') as f:
                json.dump(code_list, f, indent=4)
        except Exception as e:
            print("Error: ", e)

    def cleanup(self):
        existing_codes = self.get_codes()
        if not existing_codes:
            return

        now = int(time.time())
        day_in_seconds = 24 * 60 * 60
        
        fresh_codes = [c for c in existing_codes if (now - c.get("timestamp", 0)) < day_in_seconds]
        
        if len(fresh_codes) != len(existing_codes):
            self.save_codes(fresh_codes)

if __name__ == '__main__':
    controller = Controller(EnteMail(), hostname='0.0.0.0', port=port)
    controller.start()
    try:
        asyncio.get_event_loop().run_forever()
    except KeyboardInterrupt:
        controller.stop()