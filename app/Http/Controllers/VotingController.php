<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Position;
use App\Models\Candidate;
use App\Jobs\SendAccessCodeToVoterJob;

class VotingController extends Controller
{
    // Existing methods remain unchanged
    public function showLoginForm()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'studentId' => 'required|string',
        ]);

        // Try to find a voter with matching credentials
        $voter = Voter::where('email', $request->email)
            ->where('student_number', $request->studentId)
            ->first();

        // Check if voter exists
        if ($voter) {
            // If voter has already voted, show an error message
            if ($voter->has_voted) {
                return redirect()->back()->withErrors([
                    'error' => 'You have already voted and cannot log in again.',
                ]);
            }

            // Store voter information in the session
            session(['voter_number' => $voter->student_number, 'logged_in' => 1]);

            // Redirect to verification page
            return redirect()->route('verify');
        }

        // If credentials are incorrect, redirect back with error
        return redirect()->back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }


    public function showVerificationForm()
    {
        $logged_in = session('logged_in');
        if (!$logged_in) {
            return redirect('/')->withErrors([
                'error' => 'Your session has expired. Please login again.',
            ]);
        }

        if (!session('access_code_sent')) {
            $voter = Voter::where('student_number', session('voter_number'))->first();
            if ($voter) {
                SendAccessCodeToVoterJob::dispatch($voter->id);
                session(['access_code_sent' => true]);
            }
        }
        return view('verify');
    }

    public function verifyCode(Request $request)
    {
        // Validate the verification code
        $request->validate([
            'verificationCode' => 'required|string|size:6',
        ]);

        // Get voter student number from session
        $studentNumber = session('voter_number');

        // If no voter number in session, redirect back to login
        if (!$studentNumber) {
            return redirect('/')->withErrors([
                'error' => 'Your session has expired. Please login again.',
            ]);
        }

        // Get the voter from database
        $voter = Voter::where('student_number', $studentNumber)->first();

        // Check if voter exists and verification code matches
        if ($voter && $voter->access_code === $request->verificationCode) {
            // Mark the voter as verified in the session
            session(['voter_verified' => 1]);
            session(['voter_id' => $voter->id]); // Also store the ID for consistency

            // Redirect to ballot page
            return redirect('/ballot');
        }

        // If verification code doesn't match, redirect back with error
        return redirect()->back()->withErrors([
            'verificationCode' => 'Invalid verification code. Please try again.',
        ]);
    }

    public function showBallot()
    {
        // Get positions with their candidates using eager loading
        $positions = Position::with('candidates')->get()->map(function ($position) {
            $position->candidates = $position->candidates->shuffle();
            return $position;
        });

        return view('ballot', ['positions' => $positions]);
    }

    public function showThanks()
    {
        return view('thanks');
    }

    public function submitBallot(Request $request)
    {
        // Ensure the voter is logged in and verified
        if (!session('voter_number') || !session('voter_verified')) {
            return redirect('/')->withErrors([
                'access' => 'Please login and verify your identity before voting.',
            ]);
        }

        // Get the authenticated voter
        $voter = Voter::where('student_number', session('voter_number'))->first();

        if (!$voter || $voter->has_voted) {
            return redirect('/')->withErrors([
                'access' => 'You have already voted.',
            ]);
        }

        // Get the selected candidates from the request
        $selectedCandidates = $request->input('candidates', []);

        if (empty($selectedCandidates)) {
            return redirect()->back()->withErrors([
                'error' => 'You must select at least one candidate to vote.',
            ]);
        }

        // Increase vote count for each selected candidate
        foreach ($selectedCandidates as $candidateId) {
            $candidate = Candidate::find($candidateId);

            if ($candidate) {
                $candidate->increment('num_votes'); // Increase the vote count
            }
        }

        $voter->update(['has_voted' => true]);

        // Destroy the session
        session()->forget(['voter_number', 'voter_verified', 'voter_id']);

        // Redirect to thank you page
        return redirect('/thanks');
    }


}
