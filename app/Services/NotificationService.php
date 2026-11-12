<?php

namespace App\Services;

use App\Models\AmcContract;
use App\Models\NotificationLog;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function sendTicketAlert(Ticket $ticket, string $eventType, string $message): void
    {
        $recipient = $ticket->location?->primary_contact_number
            ?? $ticket->reportedBy?->phone
            ?? Setting::getValue('support_phone', '8208900802');

        self::dispatch('WhatsApp', $recipient, $eventType, $message, $ticket->id, null);

        if (Setting::getValue('sms_alerts_enabled', '1') === '1') {
            self::dispatch('SMS', $recipient, $eventType, $message, $ticket->id, null);
        }

        if ($ticket->reportedBy?->email && Setting::getValue('email_alerts_enabled', '0') === '1') {
            self::dispatch('Email', $ticket->reportedBy->email, $eventType, $message, $ticket->id, null);
        }
    }

    public static function sendAmcAlert(AmcContract $contract, string $message): void
    {
        $recipient = $contract->client?->contact_phone
            ?? Setting::getValue('support_phone', '8208900802');

        self::dispatch('WhatsApp', $recipient, 'amc_due', $message, null, $contract->id);
    }

    protected static function dispatch(
        string $channel,
        string $recipient,
        string $eventType,
        string $message,
        ?int $ticketId = null,
        ?int $amcContractId = null
    ): void {
        $status = 'Sent';
        $response = 'Logged in CRM notification queue (configure provider webhook in Settings for live delivery).';

        $webhookUrl = $channel === 'WhatsApp'
            ? Setting::getValue('whatsapp_webhook_url')
            : ($channel === 'SMS' ? Setting::getValue('sms_webhook_url') : null);

        if ($webhookUrl) {
            try {
                $httpRes = Http::timeout(5)->post($webhookUrl, [
                    'channel' => $channel,
                    'recipient' => $recipient,
                    'event' => $eventType,
                    'message' => $message,
                    'ticket_id' => $ticketId,
                ]);
                $status = $httpRes->successful() ? 'Delivered' : 'Failed';
                $response = substr($httpRes->body(), 0, 500);
            } catch (\Throwable $e) {
                $status = 'Failed';
                $response = $e->getMessage();
                Log::warning("Notification webhook failed ({$channel}): " . $e->getMessage());
            }
        }

        NotificationLog::create([
            'ticket_id' => $ticketId,
            'amc_contract_id' => $amcContractId,
            'channel' => $channel,
            'recipient' => $recipient,
            'event_type' => $eventType,
            'message' => $message,
            'status' => $status,
            'provider_response' => $response,
        ]);
    }
}
