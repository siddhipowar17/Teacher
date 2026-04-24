"""
Zero-Trust Security Module
Implements device authentication and verification for IoT devices.
Every device must be verified before being granted access.
"""

import hashlib
import secrets
from datetime import datetime, timezone


class ZeroTrustAuth:
    """Zero-trust device authentication and management."""

    def __init__(self):
        self.trusted_devices = {}
        self.blocked_devices = {}
        self.auth_log = []
        self._initialize_devices()

    def _initialize_devices(self):
        """Register initial set of IoT devices with varied statuses."""
        devices = [
            {"id": "IOT-SENSOR-001", "name": "Temperature Sensor Alpha", "type": "Temperature", "location": "Zone A - Server Room"},
            {"id": "IOT-SENSOR-002", "name": "Humidity Monitor Beta", "type": "Humidity", "location": "Zone B - Warehouse"},
            {"id": "IOT-SENSOR-003", "name": "Pressure Gauge Gamma", "type": "Pressure", "location": "Zone C - Lab"},
            {"id": "IOT-SENSOR-004", "name": "Multi-Sensor Delta", "type": "Multi", "location": "Zone A - Server Room"},
            {"id": "IOT-SENSOR-005", "name": "Environment Sensor Epsilon", "type": "Environment", "location": "Zone D - Office"},
        ]

        blocked = [
            {"id": "IOT-ROGUE-101", "name": "Unknown Device X", "type": "Unknown", "location": "External", "reason": "Unregistered device detected"},
            {"id": "IOT-ROGUE-102", "name": "Compromised Node Y", "type": "Sensor", "location": "Zone B", "reason": "Suspicious data pattern detected"},
            {"id": "IOT-ROGUE-103", "name": "Spoofed Gateway Z", "type": "Gateway", "location": "External", "reason": "Certificate validation failed"},
        ]

        for device in devices:
            token = self._generate_token(device["id"])
            self.trusted_devices[device["id"]] = {
                **device,
                "token": token,
                "status": "Trusted",
                "last_verified": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC"),
                "trust_score": round(85 + secrets.randbelow(16), 2)
            }

        for device in blocked:
            self.blocked_devices[device["id"]] = {
                **device,
                "status": "Blocked",
                "blocked_at": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC"),
                "threat_level": "High"
            }

    def _generate_token(self, device_id):
        """Generate a secure authentication token for a device."""
        raw = f"{device_id}-{secrets.token_hex(16)}"
        return hashlib.sha256(raw.encode()).hexdigest()[:32]

    def verify_device(self, device_id):
        """Verify if a device is trusted under zero-trust policy."""
        if device_id in self.blocked_devices:
            self._log_auth(device_id, "DENIED", "Device is blocked")
            return {"authenticated": False, "status": "Blocked", "device": self.blocked_devices[device_id]}

        if device_id in self.trusted_devices:
            device = self.trusted_devices[device_id]
            device["last_verified"] = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
            self._log_auth(device_id, "GRANTED", "Device verified successfully")
            return {"authenticated": True, "status": "Trusted", "device": device}

        self._log_auth(device_id, "DENIED", "Device not registered")
        return {"authenticated": False, "status": "Unknown", "message": "Device not registered in the system"}

    def get_all_devices(self):
        """Return all trusted and blocked devices."""
        return {
            "trusted": list(self.trusted_devices.values()),
            "blocked": list(self.blocked_devices.values()),
            "total_trusted": len(self.trusted_devices),
            "total_blocked": len(self.blocked_devices)
        }

    def block_device(self, device_id, reason="Policy violation"):
        """Block a device and move it from trusted to blocked."""
        if device_id in self.trusted_devices:
            device = self.trusted_devices.pop(device_id)
            device["status"] = "Blocked"
            device["reason"] = reason
            device["blocked_at"] = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
            device["threat_level"] = "High"
            self.blocked_devices[device_id] = device
            self._log_auth(device_id, "BLOCKED", reason)
            return {"success": True, "message": f"Device {device_id} has been blocked"}
        return {"success": False, "message": "Device not found in trusted list"}

    def _log_auth(self, device_id, action, detail):
        """Log authentication events."""
        self.auth_log.append({
            "device_id": device_id,
            "action": action,
            "detail": detail,
            "timestamp": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
        })

    def get_auth_log(self):
        """Return recent authentication log entries."""
        return self.auth_log[-20:]
