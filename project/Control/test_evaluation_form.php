<!DOCTYPE html>
<html>
<head>
    <title>Test Evaluation Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form div { margin: 10px 0; }
        label { display: inline-block; width: 100px; }
        input, select, textarea { width: 300px; padding: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Test Evaluation Submission</h1>
    
    <form id="evalForm">
        <div>
            <label for="userId">User ID:</label>
            <input type="number" id="userId" value="1">
        </div>
        <div>
            <label for="eventId">Event:</label>
            <select id="eventId">
                <option value="">Loading events...</option>
            </select>
        </div>
        <div>
            <label for="note">Note (0-5):</label>
            <input type="number" id="note" min="0" max="5" value="5">
        </div>
        <div>
            <label for="commentaire">Comment:</label>
            <textarea id="commentaire" rows="4">This is a test comment from the form</textarea>
        </div>
        <button type="button" onclick="submitEvaluation()">Submit Evaluation</button>
    </form>
    
    <div id="result"></div>
    
    <script>
    async function loadEvents() {
        try {
            const response = await fetch('get_events.php');
            const events = await response.json();
            
            const eventSelect = document.getElementById('eventId');
            eventSelect.innerHTML = '';
            
            if (events.error) {
                eventSelect.innerHTML = '<option value="">Error loading events</option>';
                return;
            }
            
            if (events.length === 0) {
                eventSelect.innerHTML = '<option value="">No events available</option>';
                return;
            }
            
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                const eventDate = event.date ? new Date(event.date).toLocaleDateString() : 'No date';
                option.textContent = `Event ${event.id}: ${event.titre} (${eventDate})`;
                eventSelect.appendChild(option);
            });
            
            console.log('Loaded events:', events);
        } catch (error) {
            console.error('Error loading events:', error);
            document.getElementById('eventId').innerHTML = '<option value="">Error loading events</option>';
        }
    }
    
    async function submitEvaluation() {
        const userId = document.getElementById('userId').value;
        const eventId = document.getElementById('eventId').value;
        const note = document.getElementById('note').value;
        const commentaire = document.getElementById('commentaire').value;
        
        if (!eventId) {
            alert('Please select an event');
            return;
        }
        
        const data = {
            action: 'submit',
            idUtilisateur: parseInt(userId),
            idEvenement: parseInt(eventId),
            note: parseInt(note),
            commentaire: commentaire
        };
        
        console.log('Sending data:', data);
        
        try {
            const response = await fetch('manage_evaluation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            console.log('Response:', result);
            
            // Display result
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<h3>Result:</h3><pre>' + JSON.stringify(result, null, 2) + '</pre>';
            
            if (result.success) {
                resultDiv.style.backgroundColor = '#d4edda';
                resultDiv.style.borderColor = '#c3e6cb';
            } else {
                resultDiv.style.backgroundColor = '#f8d7da';
                resultDiv.style.borderColor = '#f5c6cb';
            }
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = '<h3>Error:</h3><pre>' + error.message + '</pre>';
            document.getElementById('result').style.backgroundColor = '#f8d7da';
            document.getElementById('result').style.borderColor = '#f5c6cb';
        }
    }
    
    // Load events when page loads
    window.onload = loadEvents;
    </script>
</body>
</html>