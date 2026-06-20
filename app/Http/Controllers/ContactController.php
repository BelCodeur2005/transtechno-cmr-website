<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ContactMessageResource;
use App\Mail\LeadAcknowledgement;
use App\Mail\LeadNotification;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Public/ContactezNous');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'structure' => ['nullable', 'string', 'max:255'],
            'structure_type' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = ContactMessage::create([
            ...$validated,
            'status' => 'nouveau',
        ]);

        $this->sendNotifications($contact);

        return back()->with('success', 'Votre message a bien été envoyé. Notre équipe vous recontactera rapidement.');
    }

    /** Notification interne + accusé de réception (sans bloquer l'envoi du formulaire). */
    private function sendNotifications(ContactMessage $contact): void
    {
        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotification(
                subjectLine: 'Nouveau message — ' . ($contact->subject ?: $contact->name),
                heading: 'Message de contact',
                intro: "Un nouveau message vient d'être reçu via le formulaire de contact.",
                rows: [
                    'Nom' => $contact->name,
                    'Email' => $contact->email,
                    'Téléphone' => $contact->phone,
                    'Structure' => $contact->structure,
                    'Type' => $contact->structure_type,
                    'Sujet' => $contact->subject,
                ],
                messageBody: $contact->message,
                actionUrl: ContactMessageResource::getUrl('edit', ['record' => $contact->getKey()]),
                actionLabel: 'Lire le message',
            ));

            if ($contact->email) {
                Mail::to($contact->email)->send(new LeadAcknowledgement(
                    subjectLine: 'Nous avons bien reçu votre message — Transtechno Cameroun',
                    recipientName: $contact->name,
                    intro: 'Merci de nous avoir contactés. Votre message a bien été reçu et notre équipe vous répondra dans les meilleurs délais.',
                    details: array_filter(['Sujet' => $contact->subject]),
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email contact : ' . $e->getMessage());
        }
    }
}
