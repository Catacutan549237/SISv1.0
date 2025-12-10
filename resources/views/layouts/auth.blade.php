<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIS - Student Information System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --forest-green: #2d5016;
            --sage-green: #4a7c2c;
            --light-sage: #6b9b47;
            --mint-green: #8fb569;
            --cream: #f5f7f2;
            --dark-text: #1a1a1a;
            --gray-text: #666;
            --white: #ffffff;
            --error-red: #c53030;
            --success-green: #38a169;
        }

        body {
            font-family: 'Inter', sans-serif;
            /* background: linear-gradient(135deg, var(--forest-green) 0%, var(--sage-green) 100%); */
            background: url('/images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            background: linear-gradient(135deg, var(--forest-green) 0%, var(--sage-green) 100%);
            padding: 40px 30px;
            text-align: center;
            color: var(--white);
        }

        .auth-logo {
            width: 60px;
            height: 60px;
            background: var(--white);
            border-radius: 12px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            color: var(--forest-green);
        }

        .auth-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .auth-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .auth-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--sage-green);
            box-shadow: 0 0 0 3px rgba(74, 124, 44, 0.1);
        }

        .form-input.error {
            border-color: var(--error-red);
        }

        .error-message {
            color: var(--error-red);
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .success-message {
            color: var(--success-green);
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sage-green) 0%, var(--light-sage) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 124, 44, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .form-footer a {
            color: var(--sage-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--forest-green);
            text-decoration: underline;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--sage-green);
        }

        .checkbox-group label {
            font-size: 14px;
            color: var(--gray-text);
            cursor: pointer;
        }

        select.form-input {
            cursor: pointer;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: var(--error-red);
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: var(--success-green);
            border: 1px solid #cfc;
        }
    </style>
    @yield('styles')
</head>
<body>
    @yield('content')
    @yield('scripts')
</body>
</html>
