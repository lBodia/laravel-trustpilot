<?php
namespace McCaulay\Trustpilot\API;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class Item implements Arrayable
{
    /**
     * Set the data.
     *
     * @param  mixed  $data  The data.
     *
     * @return  self
     */
    public function data($data)
    {
        foreach ($data as $key => $value) {
            // Handle dates
            if ($key == 'createdAt' || $key == 'updatedAt') {
                $value = Carbon::parse($value);
            }

            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}
