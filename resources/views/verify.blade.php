<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-8 text-center">
            <h1 class="text-2xl font-bold text-white">Verification Required</h1>
            <p class="text-blue-100 mt-2">Enter the 6-digit code sent to your email</p>
        </div>

        <!-- Form content -->
        <div class="p-8">
            <form id="verificationForm" class="space-y-8" method="POST" action="{{ route('verify.code') }}">
                @csrf
                <div>
                    <label for="verificationCode" class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
                    <input type="text" id="verificationCode" name="verificationCode" maxlength="6" class="w-full h-12 text-center text-xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="123456" pattern="[0-9]{6}" required>
                    <p class="mt-2 text-sm text-gray-500">Enter all 6 digits of your verification code</p>
                    @if ($errors->has('verificationCode'))
                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('verificationCode') }}</p>
                    @endif
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md hover:shadow-lg">
                        Verify
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">Didn't receive a code?</p>
                <button id="resendButton" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline mt-1">
                    Resend Code
                </button>
            </div>
        </div>
    </div>
</body>
</html>
