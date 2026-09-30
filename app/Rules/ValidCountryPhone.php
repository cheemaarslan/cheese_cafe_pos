<?php

namespace App\Rules;

use App\Services\CountryCodeService;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates a phone number against the digit limit of the country the
 * accompanying calling code ("code" field) belongs to, instead of a single
 * site-wide digit length. Used where the user picks their own country
 * (e.g. guest login) so numbers from any country are accepted.
 */
class ValidCountryPhone implements Rule, DataAwareRule
{
    public $message = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!preg_match('/^[0-9]+$/', (string)$value)) {
            $this->message = 'The :attribute number is invalid.';
            return false;
        }

        $callingCode = $this->data['code'] ?? null;

        if (!$callingCode) {
            $this->message = 'The :attribute code is required.';
            return false;
        }

        $length = $this->lengthFor($callingCode);

        if (strlen((string)$value) !== $length) {
            $this->message = 'The :attribute number should be ' . $length . ' digits long.';
            return false;
        }

        return true;
    }

    /**
     * Get the standard digit length for the given calling code.
     *
     * @param  string  $callingCode
     * @return int
     */
    protected function lengthFor(string $callingCode): int
    {
        $countries = app(CountryCodeService::class)->listPhoneCodes()['data'];

        foreach ($countries as $country) {
            if ($country->calling_code === $callingCode) {
                return $country->phone_length;
            }
        }

        $digits = preg_replace('/\D/', '', $callingCode) ?? '';

        return max(15 - strlen($digits), 4);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
