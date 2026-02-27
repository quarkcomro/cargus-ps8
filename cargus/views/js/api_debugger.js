/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Select buttons and console container
    const btnTestLocations = document.querySelector('button[name="test_locations"]'); // Adjust selector based on actual HTML
    const btnTestTarife = document.querySelector('button[name="test_tarife"]');
    const btnTestServicii = document.querySelector('button[name="test_servicii"]');
    const consoleOutput = document.querySelector('.api-tester-console-output'); // Assuming this class exists on the black console div

    function logToConsole(message, type = 'info') {
        if (!consoleOutput) return;
        
        const timestamp = new Date().toLocaleTimeString();
        let color = '#fff'; // default white
        
        if (type === 'error') color = '#ff4c4c'; // red
        if (type === 'success') color = '#4caf50'; // green
        
        const newLine = document.createElement('div');
        newLine.style.color = color;
        newLine.style.fontFamily = 'monospace';
        newLine.style.marginBottom = '5px';
        newLine.innerHTML = `[${timestamp}] ${message}`;
        
        consoleOutput.appendChild(newLine);
        consoleOutput.scrollTop = consoleOutput.scrollHeight; // Auto-scroll to bottom
    }

    function runApiTest(actionName, loadingMessage) {
        logToConsole(loadingMessage, 'info');
        
        // ajaxUrl is usually injected globally via HookAdminControllerSetMedia in main module class
        if (typeof cargus_debugger_ajax_url === 'undefined') {
            logToConsole('Error: AJAX URL is not defined. Check controller configuration.', 'error');
            return;
        }

        fetch(cargus_debugger_ajax_url + '&action=' + actionName, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                logToConsole('OK: ' + data.message, 'success');
            } else {
                logToConsole('FAILED: ' + data.message, 'error');
            }
        })
        .catch(error => {
            logToConsole('NETWORK ERROR: ' + error.message, 'error');
        });
    }

    // Event Listeners
    if (btnTestLocations) {
        btnTestLocations.addEventListener('click', function(e) {
            e.preventDefault();
            runApiTest('TestLocations', 'Testing location connectivity...');
        });
    }

    if (btnTestTarife) {
        btnTestTarife.addEventListener('click', function(e) {
            e.preventDefault();
            runApiTest('TestTarife', 'Testing pricing calculation endpoint...');
        });
    }

    if (btnTestServicii) {
        btnTestServicii.addEventListener('click', function(e) {
            e.preventDefault();
            runApiTest('TestServicii', 'Testing services retrieval endpoint...');
        });
    }
});
