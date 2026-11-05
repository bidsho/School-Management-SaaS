<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ExamResults extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $filterSession = '';
    public string $filterTerm = '';

    /**
     * Get the authenticated student's profile securely.
     */
    private function getStudent(): StudentModel
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Student'), 403);
        abort_unless($user->school_id, 403);

        return StudentModel::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    public function updatedFilterSession(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTerm(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $student = $this->getStudent();

        /*
        |--------------------------------------------------------------------------
        | Student Exam Results
        |--------------------------------------------------------------------------
        */

        $baseQuery = ExamScore::query()
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($query) use ($student) {
                $query->where('school_id', $student->school_id);
            });

        /*
        |--------------------------------------------------------------------------
        | Available Examinations
        |--------------------------------------------------------------------------
        */

        $availableExams = Exam::query()
            ->where('school_id', $student->school_id)
            ->whereHas('scores', function ($query) use ($student) {
                $query->where('school_id', $student->school_id)
                    ->where('student_id', $student->id);
            })
            ->with([
                'academicSession',
                'term',
            ])
            ->get();

        $sessions = $availableExams
            ->pluck('academicSession')
            ->filter()
            ->unique('id')
            ->values();

        $terms = $availableExams
            ->pluck('term')
            ->filter()
            ->unique('id')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Filter Results
        |--------------------------------------------------------------------------
        */

        $resultsQuery = (clone $baseQuery)
            ->with([
                'exam.academicSession',
                'exam.term',
                'exam.classRoom',
                'subject',
            ])
            ->when($this->filterSession !== '', function ($query) {
                $query->whereHas('exam', function ($examQuery) {
                    $examQuery->where(
                        'academic_session_id',
                        $this->filterSession
                    );
                });
            })
            ->when($this->filterTerm !== '', function ($query) {
                $query->whereHas('exam', function ($examQuery) {
                    $examQuery->where(
                        'term_id',
                        $this->filterTerm
                    );
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Result Statistics
        |--------------------------------------------------------------------------
        */

        $allFilteredResults = (clone $resultsQuery)->get();

        $resultCount = $allFilteredResults->count();

        $averageScore = $resultCount > 0
            ? round((float) $allFilteredResults->avg('total_score'), 2)
            : 0;

        $highestScore = $resultCount > 0
            ? (float) $allFilteredResults->max('total_score')
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Paginated Results
        |--------------------------------------------------------------------------
        */

        $results = $resultsQuery
            ->orderByDesc('created_at')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | School Information
        |--------------------------------------------------------------------------
        */

        $school = School::query()
            ->whereKey($student->school_id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | School Logo From School Settings
        |--------------------------------------------------------------------------
        */

        $schoolSettings = SchoolSetting::query()
            ->where('school_id', $student->school_id)
            ->first();

        $schoolLogo = $schoolSettings?->logo;

        $schoolLogoExists = $schoolLogo
            && Storage::disk('public')->exists($schoolLogo);

        $schoolLogoUrl = $schoolLogoExists
            ? Storage::disk('public')->url($schoolLogo)
            : null;

        return view('livewire.student.exam-results', [
            'student' => $student,
            'school' => $school,
            'schoolSettings' => $schoolSettings,
            'schoolLogo' => $schoolLogo,
            'schoolLogoExists' => $schoolLogoExists,
            'schoolLogoUrl' => $schoolLogoUrl,
            'sessions' => $sessions,
            'terms' => $terms,
            'results' => $results,
            'resultCount' => $resultCount,
            'averageScore' => $averageScore,
            'highestScore' => $highestScore,
        ])->layout('components.layouts.student', [
            'title' => 'My Exam Results',
        ]);
    }
}