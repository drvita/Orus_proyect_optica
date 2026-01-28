<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use App\Models\ExamLifestyle;
use App\Models\ExamClinical;
use Illuminate\Support\Facades\Log;

class MigrateExamRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:exams:migrate-relations {--all : Re-sync all records even if they already have relations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates existing exam data to the new lifestyle and clinical tables (skips already migrated by default)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of Exam relations...');

        $query = Exam::query();

        if (!$this->option('all')) {
            $this->comment('Filtering exams missing lifestyle or clinical relations...');
            $query->whereDoesntHave('lifestyle')
                ->orWhereDoesntHave('clinical');
        }

        $totalExams = $query->count();

        if ($totalExams === 0) {
            $this->info('No exams found that need migration.');
            return;
        }

        $bar = $this->output->createProgressBar($totalExams);
        $bar->start();

        $query->chunk(100, function ($exams) use ($bar) {
            foreach ($exams as $exam) {
                $this->syncRelations($exam);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully.');
    }

    /**
     * Sync relationships for a single exam
     */
    private function syncRelations(Exam $exam)
    {
        $examData = $exam->toArray();

        // 1. Lifestyle
        $lifestyleFields = (new ExamLifestyle())->getFillable();
        $lifestyleData = array_intersect_key($examData, array_flip($lifestyleFields));

        // Data Cleaning for Lifestyle
        $booleanFields = ['pc', 'lap', 'tablet', 'movil', 'cefalea', 'frontal', 'temporal', 'occipital', 'temporaoi', 'temporaod', 'd_fclod', 'd_fcloi'];

        foreach ($lifestyleData as $key => $value) {
            // Fix string "0"/"1" or "true"/"false" to actual boolean
            if (in_array($key, $booleanFields)) {
                if (!is_null($value)) {
                    $lifestyleData[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
            }
            // Fix string "null"
            if ($value === 'null') {
                $lifestyleData[$key] = null;
            }
        }

        if (!empty($lifestyleData)) {
            $exam->lifestyle()->updateOrCreate([], $lifestyleData);
        }

        // 2. Clinical
        $clinicalFields = (new ExamClinical())->getFillable();
        $clinicalData = array_intersect_key($examData, array_flip($clinicalFields));

        // Data Cleaning for Clinical
        foreach ($clinicalData as $key => $value) {
            if ($value === 'null') {
                $clinicalData[$key] = null;
            }
        }

        if (!empty($clinicalData)) {
            $exam->clinical()->updateOrCreate([], $clinicalData);
        }
    }
}
