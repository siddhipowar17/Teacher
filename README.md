# AI-Based Lightweight Blockchain Framework with Edge Computing and Zero-Trust Security for Secure IoT Communication

A visually stunning, fully functional web application demonstrating the integration of **AI anomaly detection**, **lightweight blockchain**, **edge computing**, and **zero-trust security** for secure IoT communication.

![Python](https://img.shields.io/badge/Python-3.10+-blue?logo=python)
![Flask](https://img.shields.io/badge/Flask-3.x-green?logo=flask)
![scikit-learn](https://img.shields.io/badge/scikit--learn-1.6-orange?logo=scikitlearn)

## Features

### 1. AI Anomaly Detection
- **Isolation Forest** algorithm from scikit-learn
- Real-time anomaly detection on IoT sensor data
- Color-coded results: Green (Normal) / Red (Anomaly)
- Confidence scores and risk level assessment
- Manual sensor value testing interface

### 2. Lightweight Blockchain
- SHA-256 hash-based blockchain implementation
- Genesis block initialization
- Chain integrity verification
- Visual chain-style block display with hash details
- Block explorer with detailed table view

### 3. Zero-Trust Security
- Device authentication and verification
- Trusted vs Blocked device management
- Trust score tracking per device
- Authentication event logging
- Visual device status panels (green = trusted, red = blocked)

### 4. Edge Computing Simulation
- Local data preprocessing before blockchain storage
- Data validation, range checking, and compression
- Processing latency tracking
- Edge node assignment simulation

### 5. Modern Dark-Themed UI
- **Glassmorphism** design with backdrop blur
- Dark theme (black + blue gradient)
- Animated charts with **Chart.js**
- Glowing buttons, hover effects, smooth transitions
- Responsive layout (mobile + desktop)
- Card-based data display
- Loading animations and status indicators

## Integration Flow

```
IoT Sensor Data → Edge Processing → AI Detection → Device Verification → Blockchain Storage → UI Display
```

## Project Structure

```
ai-blockchain-iot/
├── app.py                    # Flask application (routes + APIs)
├── requirements.txt          # Python dependencies
├── modules/
│   ├── __init__.py
│   ├── ai_detector.py        # Isolation Forest anomaly detection
│   ├── blockchain.py         # SHA-256 lightweight blockchain
│   ├── edge_computing.py     # Edge computing simulation
│   └── zero_trust.py         # Zero-trust device authentication
├── templates/
│   ├── base.html             # Base template with navbar + sidebar
│   ├── dashboard.html        # IoT sensor dashboard
│   ├── ai_analysis.html      # AI anomaly detection page
│   ├── blockchain.html       # Blockchain explorer page
│   └── devices.html          # Device authentication page
└── static/
    └── css/
        └── style.css         # Full stylesheet (glassmorphism + dark theme)
```

## Setup & Installation

### Prerequisites
- Python 3.10 or higher
- pip package manager

### Install & Run

```bash
# Clone the repository
git clone <repo-url>
cd ai-blockchain-iot

# Create virtual environment (recommended)
python -m venv venv
source venv/bin/activate  # Linux/Mac
# venv\Scripts\activate   # Windows

# Install dependencies
pip install -r requirements.txt

# Run the application
python app.py
```

The application will be available at **http://localhost:5000**

## Pages

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/` | IoT sensor data overview with live cards and charts |
| AI Analysis | `/ai-analysis` | Anomaly detection results with manual testing |
| Blockchain | `/blockchain` | Chain visualization and block explorer |
| Device Auth | `/devices` | Zero-trust device authentication panel |

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/sensor-data` | Get edge-processed sensor readings |
| POST | `/api/ai-predict` | Run AI prediction on single data point |
| GET | `/api/ai-batch-predict` | Batch AI predictions on sensor data |
| GET | `/api/blockchain/chain` | Get the full blockchain |
| POST | `/api/blockchain/add` | Add a new block |
| GET | `/api/devices` | Get all device information |
| POST | `/api/devices/verify` | Verify a device (zero-trust) |
| POST | `/api/devices/block` | Block a device |
| GET | `/api/devices/auth-log` | Get authentication log |
| GET | `/api/edge/stats` | Get edge computing statistics |
| GET | `/api/pipeline` | Run full integration pipeline |

## Technologies Used

- **Backend:** Python, Flask
- **AI/ML:** scikit-learn (Isolation Forest)
- **Security:** SHA-256 hashing, Zero-Trust Architecture
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Charts:** Chart.js
- **Icons:** Lucide Icons
- **Fonts:** Google Fonts (Inter, JetBrains Mono)

## License

This project is created for academic and research demonstration purposes.
