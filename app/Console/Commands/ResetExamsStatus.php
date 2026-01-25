<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use Carbon\Carbon;

class ResetExamsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:reset-exams-status {--dry-run : Print the number of exams that would be reset without actually changing them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambia el status de 0 a 1 para todos los examenes que no sean del día actual';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $query = Exam::where('status', 0)
            ->whereDate('created_at', '<', $today);

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Simulación: Se encontrarón {$count} exámenes para restablecer.");
            return 0;
        }

        if ($count === 0) {
            $this->info("No hay exámenes con status 0 de días anteriores para restablecer.");
            return 0;
        }

        $query->update(['status' => 1]);

        $this->info("Se han restablecido {$count} exámenes con éxito.");

        return 0;
    }
}
