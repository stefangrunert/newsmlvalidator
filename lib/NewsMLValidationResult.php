<?php

/**
 * Class NewsMLValidation
 *
 * Keeps validation results
 */
class NewsMLValidationResult
{
    public $guid = '';
    public $validatedPart = '';
    public $hasError = false;
    public $errorMsg = '';
    public $responseBody = '';
    public $errors = [];

    public function __construct($validatedPart)
    {
        $this->validatedPart = $validatedPart;
    }
}
