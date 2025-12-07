<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'program_id' => $validated['program_id'] ?? null,
            'student_id' => $this->generateStudentId(),
            'year_level' => '1st Year',
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectBasedOnRole(User $user)
    {
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'professor' => redirect()->route('professor.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('login'),
        };
    }

    private function generateStudentId()
    {
        $year = date('Y');
        $lastStudent = User::where('role', 'student')
            ->where('student_id', 'like', $year . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_id, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return $year . '-' . $newNumber;
    }
}
