<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Company Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Accept Company Invitation</h1>
            <p>You've been invited to join <span class="company-name" id="companyName">Loading...</span></p>
        </div>

        <div class="error" id="errorMessage"></div>
        <div class="success" id="successMessage"></div>

        <div class="info">
            <p><strong>Hello <span id="inviteeName">Loading...</span>,</strong></p>
            <p>You have been invited to join <strong id="companyNameInfo">Loading...</strong> as an employee.</p>
            <p>Please set a password to complete your account setup.</p>
        </div>

        <form id="acceptForm">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="Enter your password">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Confirm your password">
            </div>
            <button type="submit" class="btn">Accept Invitation & Create Account</button>
        </form>
    </div>

    <script>
        const token = '{{ $token }}';
        
        // Load invitation details
        fetch(`/api/accept-invitation/${token}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('companyName').textContent = data.data.invitation.company_name;
                    document.getElementById('companyNameInfo').textContent = data.data.invitation.company_name;
                    document.getElementById('inviteeName').textContent = data.data.invitation.name;
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Failed to load invitation details');
            });

        document.getElementById('acceptForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if (password !== passwordConfirmation) {
                showError('Passwords do not match');
                return;
            }
            
            if (password.length < 8) {
                showError('Password must be at least 8 characters long');
                return;
            }
            
            // Submit the form
            fetch(`/api/accept-invitation/${token}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    password: password,
                    password_confirmation: password_confirmation
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Invitation accepted successfully! You can now log in to your account.');
                    document.getElementById('acceptForm').style.display = 'none';
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Failed to accept invitation. Please try again.');
            });
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('successMessage').style.display = 'none';
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
        }
    </script>
</body>
</html>
