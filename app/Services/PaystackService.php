<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    protected function client()
    {
        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            throw new RuntimeException(
                'Paystack secret key is not configured.'
            );
        }

        return Http::withToken($secretKey)
            ->acceptJson()
            ->timeout(30)
            ->baseUrl(
                rtrim(config('services.paystack.payment_url'), '/')
            );
    }

    public function initialize(
        string $email,
        int $amountInKobo,
        string $reference,
        string $callbackUrl,
        array $metadata = []
    ): array {
        $response = $this->client()->post('/transaction/initialize', [
            'email' => $email,
            'amount' => $amountInKobo,
            'currency' => 'NGN',
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => $metadata,
        ]);

        $data = $response->json();

        if (!$response->successful() || !($data['status'] ?? false)) {
            throw new RuntimeException(
                $data['message'] ?? 'Unable to initialize Paystack payment.'
            );
        }

        return $data['data'];
    }

    public function verify(string $reference): array
    {
        $response = $this->client()->get(
            '/transaction/verify/' . urlencode($reference)
        );

        $data = $response->json();

        if (!$response->successful() || !($data['status'] ?? false)) {
            throw new RuntimeException(
                $data['message'] ?? 'Unable to verify Paystack payment.'
            );
        }

        return $data['data'];
    }
}
