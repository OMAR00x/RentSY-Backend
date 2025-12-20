<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Exception;

class FirebaseService
{
    protected ?Messaging $messaging = null;
    protected bool $initialized = false;
    protected ?string $initError = null;

    public function __construct()
    {
        $this->initializeFirebase();
    }

    /**
     * Initialize Firebase Messaging
     */
    protected function initializeFirebase(): void
    {
        try {
            $credentialsPath = config('firebase.credentials');

            if (!file_exists($credentialsPath)) {
                $this->initError = "Firebase credentials file not found at: {$credentialsPath}";
                return;
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
            $this->initialized = true;
        } catch (Exception $e) {
            $this->initError = "Firebase initialization failed: " . $e->getMessage();
        }
    }

    /**
     * Check if Firebase is properly initialized
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Get initialization error if any
     */
    public function getInitError(): ?string
    {
        return $this->initError;
    }

    /**
     * Get the messaging instance
     */
    public function getMessaging(): ?Messaging
    {
        return $this->messaging;
    }
}
