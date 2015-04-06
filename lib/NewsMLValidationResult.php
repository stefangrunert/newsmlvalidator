<?php
/**
 * Disclaimer: The only purpose of this project is to give a proof of concept about the possibility to validate.
 * (X)HTML5 + Microdata documents embedded in NewsML-G2
 *
 * @author Stefan Grunert, stefan@aptoma.com
 */

/**
 * Class NewsMLValidation
 *
 * Keeps validation results
 */
class NewsMLValidationResult
{
    public function __construct($validatedPart)
    {
        $this->validatedPart = $validatedPart;
    }
    public $guid = '';
    public $validatedPart = '';
    public $hasError = false;
    public $errorMsg = '';
    public $responseBody = '';
}