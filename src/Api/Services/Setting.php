<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class Setting
{
    use ServicesTrait;

    /**
     * Get a Setting by his name.
     *
     * @param string $name The name of the setting.
     *
     * @return null|\stdClass
     */
    public function get(string $name)
    {
        return $this->build('GET', 'setting', ['name' => $name]);
    }

    /**
     * Update a setting by his name.
     *
     * @param array $data All data to update.
     *
     * @return null|\stdClass
     */
    public function update(array $data)
    {
        return $this->build('PUT', 'setting', $data);
    }
}
