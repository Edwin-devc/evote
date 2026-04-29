<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Election Ballot</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 text-center">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">MUES Elections 2026</h1>
            <p class="text-gray-600 mt-2">Select one candidate for each position</p>
        </div>

        <!-- Ballot Form -->
        <form id="ballotForm" action="{{ route('submit.ballot') }}" method="POST">
            @csrf

            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Loop through positions and display candidates -->
            @foreach ($positions as $position)
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 pb-4 border-b border-gray-200">{{ $position->name }}</h2>

                @if ($position->candidates->count() > 0)
                <div class="space-y-4 mt-4">
                    @foreach ($position->candidates as $candidate)
                    <div class="candidate-card border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                @if($candidate->photo_url)
                                <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}" class="w-16 h-16 rounded-full object-cover">
                                @else
                                <div class="w-16 h-16 rounded-full bg-gray-200"></div>
                                @endif
                            </div>
                            <div class="ml-4 flex-grow">
                                <h3 class="font-medium text-gray-800">{{ $candidate->name }}</h3>
                            </div>
                            <div>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="candidates[{{ $position->id }}]" value="{{ $candidate->id }}" class="form-radio h-5 w-5 text-blue-600" required>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 mt-4">No candidates available for this position.</p>
                @endif
            </div>
            @endforeach

            <!-- Submit Button -->
            <div class="flex justify-center mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg">
                    Submit Ballot
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.candidate-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;

                // Visual feedback for selection
                const name = radio.getAttribute('name');
                document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
                    const parentCard = input.closest('.candidate-card');
                    if (input.checked) {
                        parentCard.classList.add('border-blue-500', 'border-2');
                        parentCard.classList.remove('border-gray-200');
                    } else {
                        parentCard.classList.remove('border-blue-500', 'border-2');
                        parentCard.classList.add('border-gray-200');
                    }
                });
            });
        });

    </script>
</body>
</html>
