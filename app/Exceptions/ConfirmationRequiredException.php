<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

class ConfirmationRequiredException extends RuntimeException
{
    public function __construct(
        public readonly string $action,
        string $message,
        public readonly array $context = [],
    ) {
        parent::__construct($message, 409);
    }

    public function render(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'requires_confirmation' => true,
            'action'                => $this->action,
            'message'               => $this->getMessage(),
            'context'               => $this->context,
        ], 409);
    }
}
