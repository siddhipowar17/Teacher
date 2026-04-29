"""
AI Anomaly Detection Module
Uses Isolation Forest algorithm from scikit-learn to detect
anomalous sensor readings in IoT data streams.
"""

import numpy as np
from sklearn.ensemble import IsolationForest


class AnomalyDetector:
    """Detects anomalies in IoT sensor data using Isolation Forest."""

    def __init__(self, contamination=0.15):
        self.model = IsolationForest(
            contamination=contamination,
            random_state=42,
            n_estimators=100
        )
        self._train_model()

    def _train_model(self):
        """Train the model on synthetic normal IoT sensor data."""
        np.random.seed(42)
        normal_temp = np.random.normal(25, 3, 200)
        normal_humidity = np.random.normal(60, 5, 200)
        normal_pressure = np.random.normal(1013, 10, 200)

        training_data = np.column_stack([normal_temp, normal_humidity, normal_pressure])
        self.model.fit(training_data)

    def predict(self, temperature, humidity, pressure):
        """
        Predict whether sensor readings are normal or anomalous.
        Returns: dict with prediction label and confidence score.
        """
        features = np.array([[temperature, humidity, pressure]])
        prediction = self.model.predict(features)[0]
        score = self.model.decision_function(features)[0]

        is_anomaly = bool(prediction == -1)
        confidence = float(min(abs(score) * 100, 100))

        return {
            "temperature": round(temperature, 2),
            "humidity": round(humidity, 2),
            "pressure": round(pressure, 2),
            "prediction": "Anomaly" if is_anomaly else "Normal",
            "is_anomaly": is_anomaly,
            "confidence": round(confidence, 2),
            "risk_level": self._get_risk_level(is_anomaly, confidence)
        }

    def _get_risk_level(self, is_anomaly, confidence):
        """Determine risk level based on prediction and confidence."""
        if not is_anomaly:
            return "Low"
        if confidence > 70:
            return "Critical"
        if confidence > 40:
            return "High"
        return "Medium"

    def batch_predict(self, data_points):
        """Run predictions on multiple data points."""
        return [self.predict(d["temperature"], d["humidity"], d["pressure"]) for d in data_points]
