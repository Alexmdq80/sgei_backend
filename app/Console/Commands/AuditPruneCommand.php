<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditPruneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:prune 
                            {--group=all : The audit group to prune (entities, academic, system, auth, all)}
                            {--days= : Override the default number of days to keep}
                            {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually prune old audit logs from segmented tables';

    /**
     * Default retention periods (in days).
     */
    protected array $defaults = [
        'system' => 30,
        'auth'   => 180,
        'academic' => 365,
        'entities' => 730,
    ];

    /**
     * Mapping group to actual table names.
     */
    protected array $tables = [
        'system'   => 'audit_system',
        'auth'     => 'authentication_audits',
        'academic' => 'audit_academic',
        'entities' => 'audit_entities',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $group = $this->option('group');
        $force = $this->option('force');

        if ($group === 'all') {
            $groupsToPrune = array_keys($this->tables);
        } elseif (array_key_exists($group, $this->tables)) {
            $groupsToPrune = [$group];
        } else {
            $this->error("Invalid group: {$group}. Available: " . implode(', ', array_keys($this->tables)) . ", all");
            return 1;
        }

        if (!$force && !$this->confirm("Are you sure you want to prune logs for: " . implode(', ', $groupsToPrune) . "?", false)) {
            $this->info("Pruning cancelled.");
            return 0;
        }

        foreach ($groupsToPrune as $g) {
            $days = $this->option('days') ?: $this->defaults[$g];
            $table = $this->tables[$g];
            $cutoffDate = now()->subDays($days);

            $this->info("Pruning group [{$g}] (table: {$table}). Keeping logs from the last {$days} days (since {$cutoffDate->toDateString()})...");

            $deleted = DB::table($table)
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $this->info("✓ Deleted {$deleted} old records from {$table}.");
        }

        $this->info("Audit log pruning completed successfully.");
        return 0;
    }
}
