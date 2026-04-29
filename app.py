"""
AI-Based Lightweight Blockchain Framework
with Edge Computing and Zero-Trust Security for Secure IoT Communication

Main Flask Application
"""

from flask import Flask, render_template, jsonify, request
from modules.blockchain import Blockchain
from modules.ai_detector import AnomalyDetector
from modules.zero_trust import ZeroTrustAuth
from modules.edge_computing import EdgeProcessor

app = Flask(__name__)

# Initialize modules
blockchain = Blockchain()
ai_detector = AnomalyDetector()
zero_trust = ZeroTrustAuth()
edge_processor = EdgeProcessor()


# ─── Page Routes ────────────────────────────────────────────────

@app.route("/")
def dashboard():
    """Main dashboard showing IoT sensor data overview."""
    return render_template("dashboard.html", active_page="dashboard")


@app.route("/ai-analysis")
def ai_analysis():
    """AI anomaly detection analysis page."""
    return render_template("ai_analysis.html", active_page="ai")


@app.route("/blockchain")
def blockchain_page():
    """Blockchain visualization page."""
    return render_template("blockchain.html", active_page="blockchain")


@app.route("/devices")
def devices():
    """Device authentication and management page."""
    return render_template("devices.html", active_page="devices")


# ─── API Endpoints ──────────────────────────────────────────────

@app.route("/api/sensor-data")
def api_sensor_data():
    """Generate and return IoT sensor data processed at the edge."""
    raw_data = edge_processor.generate_sensor_data(6)
    processed = edge_processor.process_at_edge(raw_data)
    return jsonify({"data": processed, "edge_stats": edge_processor.get_stats()})


@app.route("/api/ai-predict", methods=["POST"])
def api_ai_predict():
    """Run AI anomaly detection on sensor data."""
    data = request.get_json()
    if not data:
        return jsonify({"error": "No data provided"}), 400

    result = ai_detector.predict(
        data.get("temperature", 25),
        data.get("humidity", 60),
        data.get("pressure", 1013)
    )
    return jsonify(result)


@app.route("/api/ai-batch-predict")
def api_ai_batch_predict():
    """Run AI predictions on a batch of generated sensor data."""
    raw_data = edge_processor.generate_sensor_data(6)
    processed = edge_processor.process_at_edge(raw_data)
    predictions = ai_detector.batch_predict(processed)
    return jsonify({"predictions": predictions})


@app.route("/api/blockchain/chain")
def api_blockchain_chain():
    """Return the full blockchain."""
    return jsonify({
        "chain": blockchain.get_chain(),
        "length": len(blockchain.chain),
        "is_valid": blockchain.is_chain_valid()
    })


@app.route("/api/blockchain/add", methods=["POST"])
def api_blockchain_add():
    """Add a new block to the blockchain."""
    data = request.get_json()
    if not data:
        return jsonify({"error": "No data provided"}), 400

    new_block = blockchain.add_block(data)
    return jsonify({
        "message": "Block added successfully",
        "block": new_block,
        "chain_length": len(blockchain.chain),
        "is_valid": blockchain.is_chain_valid()
    })


@app.route("/api/devices")
def api_devices():
    """Return all device information."""
    return jsonify(zero_trust.get_all_devices())


@app.route("/api/devices/verify", methods=["POST"])
def api_verify_device():
    """Verify a device under zero-trust policy."""
    data = request.get_json()
    device_id = data.get("device_id", "") if data else ""
    if not device_id:
        return jsonify({"error": "Device ID required"}), 400
    result = zero_trust.verify_device(device_id)
    return jsonify(result)


@app.route("/api/devices/block", methods=["POST"])
def api_block_device():
    """Block a device."""
    data = request.get_json()
    device_id = data.get("device_id", "") if data else ""
    reason = data.get("reason", "Policy violation") if data else "Policy violation"
    if not device_id:
        return jsonify({"error": "Device ID required"}), 400
    result = zero_trust.block_device(device_id, reason)
    return jsonify(result)


@app.route("/api/devices/auth-log")
def api_auth_log():
    """Return authentication log."""
    return jsonify({"log": zero_trust.get_auth_log()})


@app.route("/api/edge/stats")
def api_edge_stats():
    """Return edge computing statistics."""
    return jsonify(edge_processor.get_stats())


@app.route("/api/pipeline")
def api_full_pipeline():
    """
    Execute the full integration pipeline:
    IoT Data → Edge Processing → AI Detection → Device Verification → Blockchain Storage
    """
    # Step 1: Generate IoT data
    raw_data = edge_processor.generate_sensor_data(6)

    # Step 2: Edge processing
    edge_processed = edge_processor.process_at_edge(raw_data)

    # Step 3: AI anomaly detection
    ai_results = ai_detector.batch_predict(edge_processed)

    # Step 4: Device verification
    device_results = []
    for item in edge_processed:
        verification = zero_trust.verify_device(item["sensor_id"])
        device_results.append({
            "sensor_id": item["sensor_id"],
            "verified": verification["authenticated"]
        })

    # Step 5: Store in blockchain
    pipeline_data = {
        "sensor_count": len(edge_processed),
        "anomalies_detected": sum(1 for r in ai_results if r["is_anomaly"]),
        "devices_verified": sum(1 for d in device_results if d["verified"]),
        "edge_stats": edge_processor.get_stats()
    }
    block = blockchain.add_block(pipeline_data)

    return jsonify({
        "sensor_data": edge_processed,
        "ai_predictions": ai_results,
        "device_verifications": device_results,
        "blockchain_entry": block,
        "pipeline_status": "Complete"
    })


if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
