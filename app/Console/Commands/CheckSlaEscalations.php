<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckSlaEscalations extends Command
{
    protected $signature = 'sla:check-escalations';
    protected $description = 'Check open tickets for SLA breaches and trigger automatic escalation alerts';

    public function handle(): int
    {
        $tickets = Ticket::with('location')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->get();

        $escalated = 0;

        foreach ($tickets as $ticket) {
            if ($ticket->isSlaBreached() && $ticket->escalation_level < 2) {
                $newLevel = $ticket->escalation_level + 1;
                $ticket->update([
                    'escalation_level' => $newLevel,
                    'escalated_at' => now(),
                    'status' => $ticket->status === 'Open' ? 'Overdue' : $ticket->status,
                ]);

                NotificationService::sendTicketAlert(
                    $ticket,
                    'sla_escalation',
                    "SLA ESCALATION (Level {$newLevel}): Ticket {$ticket->ticket_number} at {$ticket->location?->name} has exceeded its resolution SLA."
                );
                $escalated++;
            }
        }

        $this->info("SLA check complete. Escalated {$escalated} ticket(s).");
        return self::SUCCESS;
    }
}
