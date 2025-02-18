// script.js

const emailInput = document.getElementById('Email');
const achternaamInput = document.getElementById('Achternaam');
const voornaamInput = document.getElementById('Voornaam');
const errorDisplay = document.getElementById('error');
const enterButton = document.getElementById('enterButton');
const loginStatus = document.getElementById('loginStatus');

let ws; // Declare ws outside the event listener

enterButton.addEventListener('click', function (e) {
    const emailValue = emailInput.value.trim();
    const achternaamValue = achternaamInput.value.trim();
    const voornaamValue = voornaamInput.value.trim();

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+.[^\s@]+$/;
    if (!emailRegex.test(emailValue)) {
        errorDisplay.textContent = 'E-mailadres is ongeldig. Controleer het formaat.';
        return; // Stop execution if email is invalid
    } else {
        errorDisplay.textContent = '';
    }
    //Check if Achternaam & Voornaam aren't empty
    if (!achternaamValue || !voornaamValue)
    {
        errorDisplay.textContent = 'Naam en voornaam zijn verplicht.';
        return;
    }

    // --- WebSocket Connection and Data Sending ---

    // Close the previous WebSocket connection if it exists
    if (ws) {
        ws.close();
    }

    // 1. Connect to the WebSocket Server
    ws = new WebSocket('ws://192.168.0.101:8080'); // Replace with your Raspberry Pi's IP and WebSocket port

    ws.onopen = function() {
        console.log('WebSocket connection opened');
        // 2. Send Login Data (after the connection is open)
        const userData = {
            type: "login", // Indicate this is a login attempt
            achternaam: achternaamValue,
            voornaam: voornaamValue,
            email: emailValue
        };
        ws.send(JSON.stringify(userData));
    };

    ws.onmessage = function(event) {
        // 3. Receive Response from Server
        const response = JSON.parse(event.data);
        console.log('Received from server:', response);

        if (response.status === 'success') {
            loginStatus.textContent = 'Login successful!';
        } else if (response.status === 'new_user') {
            loginStatus.textContent = 'New user registered successfully!';
        }
          else {
            loginStatus.textContent = 'Login failed: ' + response.message;
        }
    };

    ws.onerror = function(error) {
        console.error('WebSocket error:', error);
        loginStatus.textContent = 'WebSocket connection error.';
    };

    ws.onclose = function(event) {
        console.log('WebSocket connection closed:', event);
    };
});