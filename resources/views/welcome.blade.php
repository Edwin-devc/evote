<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Voting System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">

        <!-- Header -->
        <div class="bg-indigo-600 px-6 py-4">
            <h1 class="text-xl md:text-2xl font-bold text-white text-center">MUES Election 2026</h1>
        </div>

        <!-- Error Message -->
        @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-md mt-4 mx-6 flex items-center space-x-2">
            <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.998 3.001a1.5 1.5 0 0 1 1.5 1.5v6.996a1.5 1.5 0 1 1-3 0V4.501a1.5 1.5 0 0 1 1.5-1.5zm0 16.002a1.5 1.5 0 1 1 0 3.001 1.5 1.5 0 0 1 0-3.001z" />
            </svg>
            <div>
                <ul class="list-none text-sm">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Form content -->
        <div class="p-6 md:p-8">
            <p class="text-gray-600 text-center mb-6">Please verify your identity to access the voting system</p>

            <form id="votingForm" class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@gmail.com" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                    </div>

                    <div>
                        <label for="studentId" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                        <input type="text" id="studentId" name="studentId" placeholder="24/U/7689" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg">
                        Login
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Your vote is anonymous and secure</p>
            </div>

        </div>
    </div>
</body>
</html>
