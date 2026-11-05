<?php

namespace App\Livewire\Parent\Results;

use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\SchoolParent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.parent')]
#[Title('Exam Results')]
class ResultsIndex extends Component
{
    #[Locked]
    public int $parentId;

    public ?int $studentId = null;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        $parent = $user->parentProfile()
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $this->parentId = (int) $parent->id;

        $firstChild = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->orderBy('students.first_name')
            ->first();

        $this->studentId = $firstChild
            ? (int) $firstChild->id
            : null;
    }

    private function parentProfile(): SchoolParent
    {
        $user = Auth::user();

        abort_unless($user && $user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        return $user->parentProfile()
            ->whereKey($this->parentId)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    private function childrenQuery(): Builder
    {
        $parent = $this->parentProfile();
        $schoolId = Auth::user()->school_id;

        return Student::query()
            ->whereIn(
                'students.id',
                $parent->students()
                    ->wherePivot('school_id', $schoolId)
                    ->select('students.id')
            );
    }

    private function validateSelectedChild(): void
    {
        if ($this->studentId === null) {
            return;
        }

        abort_unless(
            $this->childrenQuery()
                ->whereKey($this->studentId)
                ->exists(),
            404
        );
    }

    public function updatedStudentId(): void
    {
        $this->validateSelectedChild();
    }

    private function publishedResultsQuery(): Builder
    {
        $this->validateSelectedChild();

        return ExamScore::query()
            ->where('student_id', $this->studentId)
            ->whereHas('exam', function (Builder $query) {
                $query->where('status', 'published');
            })
            ->with([
                'exam.academicSession',
                'exam.term',
                'exam.classRoom',
                'exam.subject',
                'subject',
            ]);
    }

    public function render()
    {
        $children = $this->childrenQuery()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $scores = collect();

        if ($this->studentId !== null) {
            $scores = $this->publishedResultsQuery()
                ->get()
                ->sortByDesc(
                    fn ($score) =>
                        $score->exam?->exam_date?->timestamp ?? 0
                )
                ->values();
        }

        $resultsByExam = $scores->groupBy('exam_id');

        $percentages = $scores->map(function ($score) {
            $maximum = (float) ($score->exam?->total_marks ?? 0);

            if ($maximum <= 0) {
                return null;
            }

            return ((float) $score->total_score / $maximum) * 100;
        })->filter(fn ($percentage) => $percentage !== null);

        $summary = [
            'exams' => $resultsByExam->count(),
            'subjects' => $scores->count(),
            'average' => $percentages->isNotEmpty()
                ? round($percentages->avg(), 1)
                : null,
        ];

        return view(
            'livewire.parent.results.results-index',
            compact('children', 'scores', 'resultsByExam', 'summary')
        );
    }
}
