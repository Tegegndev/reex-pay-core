<?php

namespace Database\Seeders;

use App\Models\ReferralContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ReferralContentSeeder extends Seeder
{
    /**
     * Run the database seeds to create default referral content with English and Spanish support.
     */
    public function run(): void
    {
        Log::info('Starting creation of default referral content with English and Spanish support');

        // Check if we already have referral content
        if (ReferralContent::count() > 0) {
            Log::info('Referral content already exists. Skipping seeder.');

            return;
        }

        // Multi-language heading (English and Spanish)
        $heading = [
            'en' => 'Share your unique referral link and earn for every successful signup.',
            'es' => 'Comparte tu enlace de referencia único y gana por cada registro exitoso.',
        ];

        // Multi-language positive guidelines (English and Spanish)
        $positiveGuidelines = [
            'en' => [
                'Easily share the link on social media platforms.',
                'Promote your link through any marketing channel.',
                'Share with friends and family members.',
                'Use your referral link in email signatures.',
                'Post in relevant online communities and forums.',
            ],
            'es' => [
                'Comparte fácilmente el enlace en plataformas de redes sociales.',
                'Promociona tu enlace a través de cualquier canal de marketing.',
                'Comparte con amigos y familiares.',
                'Usa tu enlace de referencia en firmas de correo electrónico.',
                'Publica en comunidades y foros en línea relevantes.',
            ],
        ];

        // Multi-language negative guidelines (English and Spanish)
        $negativeGuidelines = [
            'en' => [
                'Multiple accounts from the same device are not allowed.',
                'Automated signups using bots are prohibited.',
                'Fake or misleading information is strictly forbidden.',
                'Spamming or aggressive marketing tactics are not permitted.',
                'Creating accounts for yourself using your own referral link is not allowed.',
            ],
            'es' => [
                'No se permiten múltiples cuentas desde el mismo dispositivo.',
                'Los registros automatizados usando bots están prohibidos.',
                'La información falsa o engañosa está estrictamente prohibida.',
                'El spam o las tácticas de marketing agresivas no están permitidas.',
                'Crear cuentas para ti mismo usando tu propio enlace de referencia no está permitido.',
            ],
        ];

        // Create the ReferralContent record with English and Spanish support
        ReferralContent::create([
            'heading'             => $heading,
            'positive_guidelines' => $positiveGuidelines,
            'negative_guidelines' => $negativeGuidelines,
            'image_path'          => 'general/static/svg/gift.svg',
        ]);

        Log::info('Successfully created default referral content with English and Spanish support');
    }
}
