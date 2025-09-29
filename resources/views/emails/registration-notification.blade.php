<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #374151;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 20px;
        }

        .message {
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .user-info {
            background-color: #f9fafb;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #374151;
        }

        .user-info h3 {
            color: #374151;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .user-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .user-detail strong {
            color: #374151;
        }

        .user-detail span {
            color: #6b7280;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            background-color: #374151;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: #4b5563;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #9ca3af;
            font-size: 12px;
            line-height: 1.5;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header, .content, .footer {
                padding: 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .greeting {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Welcome to Your New Account</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $user->name }},
            </div>

            <div class="message">
                Welcome to {{ config('app.name') }}! Your account has been successfully created. You can now access the system using your credentials.
            </div>

            <div class="user-info">
                <h3>Your Account Details</h3>
                <div class="user-detail">
                    <strong>Name:</strong>
                    <span>{{ $user->name }}</span>
                </div>
                <div class="user-detail">
                    <strong>Email:</strong>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="user-detail">
                    <strong>Password:</strong>
                    <span>12345678 (*change the password upon login)</span>
                </div>
                <div class="user-detail">
                    <strong>Registration Date:</strong>
                    <span>{{ $user->created_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                @if($user->email_verified_at)
                <div class="user-detail">
                    <strong>Email Verified:</strong>
                    <span>Yes</span>
                </div>
                @else
                <div class="user-detail">
                    <strong>Email Verified:</strong>
                    <span>Not yet verified</span>
                </div>
                @endif
            </div>

            <div class="button-container">
                <a href="{{ config('app.url') }}" class="btn">
                    Access {{ config('app.name') }}
                </a>
            </div>

            <div class="message">
                You can now log in to the system and start using all available features. If you have any questions or need assistance, please don't hesitate to contact our support team.
            </div>
        </div>

        <div class="footer">
            <p>
                This is an automated notification from {{ config('app.name') }}.<br>
                If you have any questions, please contact your system administrator.
            </p>
        </div>
    </div>
</body>
</html>
