"""
Edge Computing Simulation Module
Simulates local data processing at the edge before sending
filtered and validated data to the blockchain.
"""

import random
import time
from datetime import datetime, timezone


class EdgeProcessor:
    """Simulates edge computing node for IoT data preprocessing."""

    def __init__(self):
        self.processing_log = []
        self.stats = {
            "total_processed": 0,
            "filtered_out": 0,
            "passed_through": 0,
            "avg_latency_ms": 0
        }

    def generate_sensor_data(self, count=6):
        """Generate simulated IoT sensor readings."""
        sensors = [
            {"id": "IOT-SENSOR-001", "name": "Temp Sensor Alpha", "type": "Temperature"},
            {"id": "IOT-SENSOR-002", "name": "Humidity Monitor Beta", "type": "Humidity"},
            {"id": "IOT-SENSOR-003", "name": "Pressure Gauge Gamma", "type": "Pressure"},
            {"id": "IOT-SENSOR-004", "name": "Multi-Sensor Delta", "type": "Multi"},
            {"id": "IOT-SENSOR-005", "name": "Env Sensor Epsilon", "type": "Environment"},
            {"id": "IOT-SENSOR-006", "name": "Temp Sensor Zeta", "type": "Temperature"},
        ]

        data_points = []
        for i in range(min(count, len(sensors))):
            sensor = sensors[i]
            # Occasionally inject anomalous readings
            if random.random() < 0.15:
                temp = random.uniform(50, 80)
                humidity = random.uniform(90, 100)
                pressure = random.uniform(1050, 1080)
            else:
                temp = random.uniform(20, 30)
                humidity = random.uniform(45, 75)
                pressure = random.uniform(1000, 1025)

            data_points.append({
                "sensor_id": sensor["id"],
                "sensor_name": sensor["name"],
                "sensor_type": sensor["type"],
                "temperature": round(temp, 2),
                "humidity": round(humidity, 2),
                "pressure": round(pressure, 2),
                "timestamp": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC"),
                "edge_node": f"EDGE-NODE-{random.randint(1, 3):02d}"
            })

        return data_points

    def process_at_edge(self, data_points):
        """
        Process sensor data at the edge node.
        Performs local filtering, validation, and aggregation.
        """
        start_time = time.time()
        processed = []

        for point in data_points:
            result = {
                **point,
                "edge_processed": True,
                "processing_steps": []
            }

            # Step 1: Data validation
            result["processing_steps"].append("Data validation: PASSED")

            # Step 2: Range check
            temp_ok = -40 <= point["temperature"] <= 120
            humid_ok = 0 <= point["humidity"] <= 100
            press_ok = 800 <= point["pressure"] <= 1200

            if not (temp_ok and humid_ok and press_ok):
                result["processing_steps"].append("Range check: FILTERED OUT")
                result["edge_status"] = "Filtered"
                self.stats["filtered_out"] += 1
            else:
                result["processing_steps"].append("Range check: PASSED")
                result["edge_status"] = "Validated"
                self.stats["passed_through"] += 1

            # Step 3: Local aggregation
            result["processing_steps"].append("Local aggregation: COMPLETE")

            # Step 4: Compression simulation
            result["data_compressed"] = True
            result["compression_ratio"] = round(random.uniform(0.3, 0.6), 2)
            result["processing_steps"].append(f"Compression: {result['compression_ratio']:.0%} reduction")

            self.stats["total_processed"] += 1
            latency = random.uniform(2, 15)
            result["edge_latency_ms"] = round(latency, 2)

            processed.append(result)

        total_latency = time.time() - start_time
        self.stats["avg_latency_ms"] = round(total_latency * 1000 / max(len(data_points), 1), 2)

        self._log_processing(len(data_points), len([p for p in processed if p["edge_status"] == "Validated"]))

        return processed

    def _log_processing(self, total, validated):
        """Log edge processing event."""
        self.processing_log.append({
            "timestamp": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC"),
            "total_input": total,
            "validated": validated,
            "filtered": total - validated
        })

    def get_stats(self):
        """Return edge processing statistics."""
        return self.stats

    def get_processing_log(self):
        """Return recent processing log entries."""
        return self.processing_log[-10:]
