<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class Transaction
{
    use ServicesTrait;

    /**
     * Get all transactions by a user id.
     *
     * @param int $id The user id.
     *
     * @return null|\stdClass
     */
    public function getByUser(int $id)
    {
        return $this->build('GET', sprintf('transaction/user/%d', $id));
    }
}
