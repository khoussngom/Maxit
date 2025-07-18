<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Lookups
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */


namespace Twilio\Rest\Lookups\V2;

use Twilio\Exceptions\TwilioException;
use Twilio\Values;
use Twilio\Version;
use Twilio\InstanceContext;


class LookupOverrideContext extends InstanceContext
    {
    /**
     * Initialize the LookupOverrideContext
     *
     * @param Version $version Version that contains the resource
     * @param string $field
     * @param string $phoneNumber
     */
    public function __construct(
        Version $version,
        $field,
        $phoneNumber
    ) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [
        'field' =>
            $field,
        'phoneNumber' =>
            $phoneNumber,
        ];

        $this->uri = '/PhoneNumbers/' . \rawurlencode($phoneNumber)
        .'/Overrides/' . \rawurlencode($field)
        .'';
    }

    /**
     * Create the LookupOverrideInstance
     *
     * @return LookupOverrideInstance Created LookupOverrideInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function create(): LookupOverrideInstance
    {

        $headers = Values::of(['Content-Type' => 'application/json', 'Accept' => 'application/json' ]);
        $data = $overridesRequest->toArray();
        $payload = $this->version->create('POST', $this->uri, [], $data, $headers);

        return new LookupOverrideInstance(
            $this->version,
            $payload,
            $this->solution['field'],
            $this->solution['phoneNumber']
        );
    }


    /**
     * Delete the LookupOverrideInstance
     *
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(): bool
    {

        $headers = Values::of(['Content-Type' => 'application/x-www-form-urlencoded' ]);
        return $this->version->delete('DELETE', $this->uri, [], [], $headers);
    }


    /**
     * Fetch the LookupOverrideInstance
     *
     * @return LookupOverrideInstance Fetched LookupOverrideInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): LookupOverrideInstance
    {

        $headers = Values::of(['Content-Type' => 'application/x-www-form-urlencoded', 'Accept' => 'application/json' ]);
        $payload = $this->version->fetch('GET', $this->uri, [], [], $headers);

        return new LookupOverrideInstance(
            $this->version,
            $payload,
            $this->solution['field'],
            $this->solution['phoneNumber']
        );
    }


    /**
     * Update the LookupOverrideInstance
     *
     * @return LookupOverrideInstance Updated LookupOverrideInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(): LookupOverrideInstance
    {

        $headers = Values::of(['Content-Type' => 'application/json', 'Accept' => 'application/json' ]);
        $data = $overridesRequest->toArray();
        $payload = $this->version->update('PUT', $this->uri, [], $data, $headers);

        return new LookupOverrideInstance(
            $this->version,
            $payload,
            $this->solution['field'],
            $this->solution['phoneNumber']
        );
    }


    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Lookups.V2.LookupOverrideContext ' . \implode(' ', $context) . ']';
    }
}
