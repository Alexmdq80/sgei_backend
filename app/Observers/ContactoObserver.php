<?php

namespace App\Observers;

use App\Models\Contacto;
use App\Services\UserService;

class ContactoObserver
{
    /**
     * Handle the Contacto "created" event.
     */
    public function created(Contacto $contacto): void
    {
        $this->triggerLinking($contacto);
    }

    /**
     * Handle the Contacto "updated" event.
     */
    public function updated(Contacto $contacto): void
    {
        $this->triggerLinking($contacto);
    }

    /**
     * Trigger the linking process if email is present.
     */
    protected function triggerLinking(Contacto $contacto): void
    {
        if ($contacto->email && $contacto->persona) {
            app(UserService::class)->linkPersonaToUser($contacto->persona);
        }
    }
}
