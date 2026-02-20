<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class Paypal
{
    use ServicesTrait;

    /**
     * Get a paypal by a user id.
     *
     * @param int $id The user id.
     *
     * @return null|\stdClass
     */
    public function getByUser(int $id)
    {
        return $this->build('GET', sprintf('paypal/user/%d', $id));
    }
}
