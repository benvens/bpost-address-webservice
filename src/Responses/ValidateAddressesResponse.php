<?php

namespace Spatie\BpostAddressWebservice\Responses;

use Spatie\BpostAddressWebservice\Address;
use Spatie\BpostAddressWebservice\Issues\Error;
use Spatie\BpostAddressWebservice\Issues\Warning;
use Spatie\BpostAddressWebservice\ValidatedAddress;

class ValidateAddressesResponse
{
    /** @var array */
    protected $responseBody = [];

    /** @var \Spatie\BpostAddressWebservice\Address */
    protected $originalAddresses = [];

    public function __construct(array $responseBody, array $originalAddresses)
    {
        $this->responseBody = $responseBody;

        if(isset($responseBody['ValidateAddressesResponse']['GeneralError']['ErrorCode'])) {
            dd($responseBody);
            throw new \Exception(
                'BPost failed with ErrorCode: '
                .$responseBody['ValidateAddressesResponse']['GeneralError']['ErrorCode']
                .', and ErrorSeverity: ' . $responseBody['ValidateAddressesResponse']['GeneralError']['ErrorSeverity'] .'.'
            );
        }

        $this->originalAddresses = $originalAddresses;
    }

    public function validatedAddresses(): array
    {
        $validationResults = $this->responseBody['ValidateAddressesResponse']['ValidatedAddressResultList']['ValidatedAddressResult'] ?? [];
        
        return array_map(function (array $validationResult) {
            $errors = [];
            $warnings = [];

            foreach ($validationResult['Error'] ?? [] as $error) {
                if ($error['ErrorSeverity'] === 'warning') {
                    $warnings[] = new Warning($error['ErrorCode'], lcfirst($error['ComponentRef']));
                }

                if ($error['ErrorSeverity'] === 'error') {
                    $errors[] = new Error($error['ErrorCode'], lcfirst($error['ComponentRef']));
                }
            }
            return new ValidatedAddress(
                Address::fromResponse($validationResult['ValidatedAddressList']['ValidatedAddress'][0] ?? []),
                $this->originalAddresses[$validationResult['@id']],
                $errors,
                $warnings
            );
        }, $validationResults);
    }

    public function responseBody() : array
    {
        return $this->responseBody;
    }
}
