<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class Ticket
{
    use ServicesTrait;

    /**
     * Get a Ticket by his steam_id.
     *
     * @param int $id The id of the user.
     *
     * @return null|\stdClass
     */
    public function get(int $id)
    {
        return $this->build('GET', sprintf('ticket/%d', $id));
    }

    /**
     * Create a Ticket.
     *
     * @param array $data All data used to create the ban.
     *
     * @return null|\stdClass
     */
    public function create(array $data)
    {
        return $this->build('POST', 'ticket/create', $data);
    }

    /**
     * Update a setting by his name.
     *
     * @param array $data All data to update.
     *
     * @return null|\stdClass
     */
    public function update(int $id, array $data)
    {
        return $this->build('PUT', sprintf('ticket/%d', $id), $data);
    }

    /**
     * Get a Ticket by his ticket opened message id.
     *
     * @param int $id The id of the message.
     *
     * @return null|\stdClass
     */
    public function getByTicketMessage(int $id)
    {
        return $this->build('GET', sprintf('ticket/ticketmessage/%d', $id));
    }
}
