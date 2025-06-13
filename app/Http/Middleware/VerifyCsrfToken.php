<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
/**
     * @var string[]   ← correct PHPDoc
     */
  protected $except = [
        'api/save-progress',    // ← no leading slash, and no “array” type on the property
    ];
}
