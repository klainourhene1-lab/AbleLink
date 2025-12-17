<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR - AbeLink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        #scanner-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 30px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #667eea;
        }
        
        #reader {
            width: 100%;
        }
        
        .result {
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }
        
        .result.success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            display: block;
        }
        
        .result.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
            display: block;
        }
        
        .result h3 {
            margin-bottom: 10px;
        }
        
        .result p {
            margin: 5px 0;
        }
        
        .manual-input {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px dashed #ddd;
        }
        
        .manual-input h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎫 Scanner de billets</h1>
        <p class="subtitle">Scannez le QR code du participant pour vérifier son inscription</p>
        
        <div id="scanner-container">
            <div id="reader"></div>
        </div>
        
        <div id="result" class="result"></div>
        
        <div class="manual-input">
            <h3>Vérification manuelle</h3>
            <div class="input-group">
                <input type="text" id="participationId" placeholder="ID de participation">
                <button onclick="verifyManual()">Vérifier</button>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrCode;
        
        function onScanSuccess(decodedText, decodedResult) {
            try {
                // Parse format: ABELINK-EVENT-{eventId}-PART-{participationId}-{email}
                if (decodedText.startsWith('ABELINK-EVENT-')) {
                    const parts = decodedText.split('-');
                    
                    if (parts.length >= 5) {
                        const eventId = parts[2];
                        const participationId = parts[4];
                        const email = parts.slice(5).join('-'); // Email might contain dashes
                        
                        showResult('success', 
                            `✓ Participant vérifié`,
                            `
                            <p><strong>Email:</strong> ${email}</p>
                            <p><strong>ID Participation:</strong> ${participationId}</p>
                            <p><strong>ID Événement:</strong> ${eventId}</p>
                            <p style="margin-top: 15px; padding: 10px; background: #d1ecf1; border-radius: 4px; color: #0c5460;">
                                ✓ Accès autorisé - Bienvenue à l'événement!
                            </p>
                            `
                        );
                        
                        // Play success sound
                        playSound('success');
                        
                        // Stop scanning temporarily
                        html5QrCode.pause();
                        setTimeout(() => {
                            html5QrCode.resume();
                            document.getElementById('result').style.display = 'none';
                        }, 4000);
                    } else {
                        showResult('error', '❌ QR Code invalide', 'Format de QR code non reconnu.');
                    }
                } else {
                    showResult('error', '❌ QR Code invalide', 'Ce QR code ne correspond pas à un billet AbeLink.');
                }
            } catch (e) {
                showResult('error', '❌ Erreur de lecture', 'Impossible de lire ce QR code: ' + e.message);
            }
        }
        
        function onScanError(errorMessage) {
            // Ignore scan errors (they happen frequently)
        }
        
        function showResult(type, title, content) {
            const resultDiv = document.getElementById('result');
            resultDiv.className = `result ${type}`;
            resultDiv.innerHTML = `<h3>${title}</h3>${content}`;
        }
        
        function playSound(type) {
            // Create a simple beep sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = type === 'success' ? 800 : 400;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        }
        
        function verifyManual() {
            const participationId = document.getElementById('participationId').value;
            
            if (!participationId) {
                showResult('error', '❌ Erreur', 'Veuillez entrer un ID de participation.');
                return;
            }
            
            // In a real application, you would verify this against the database
            showResult('success', 
                `✓ Vérification manuelle`,
                `<p><strong>ID Participation:</strong> ${participationId}</p>
                 <p>Note: Vérifiez l'identité du participant</p>`
            );
            
            playSound('success');
        }
        
        // Initialize QR Code scanner
        window.addEventListener('load', () => {
            html5QrCode = new Html5Qrcode("reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        onScanSuccess,
                        onScanError
                    ).catch(err => {
                        console.error('Error starting scanner:', err);
                        showResult('error', '❌ Erreur caméra', 'Impossible d\'accéder à la caméra. Utilisez la vérification manuelle.');
                    });
                }
            }).catch(err => {
                console.error('Error getting cameras:', err);
                showResult('error', '❌ Erreur caméra', 'Impossible d\'accéder à la caméra. Utilisez la vérification manuelle.');
            });
        });
    </script>
</body>
</html>
