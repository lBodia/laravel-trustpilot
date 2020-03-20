<?php
namespace McCaulay\Trustpilot\Query;

use Illuminate\Support\Collection;
use McCaulay\Trustpilot\Query\Queryable;

class Builder
{
    /**
     * The queryable resource.
     *
     * @var \McCaulay\Trustpilot\Query\Queryable
     */
    private $queryable;

    /**
     * The where conditions to restrict the results.
     *
     * @var array
     */
    private $where = [];

    /**
     * Order the results by a column, ascending or descending.
     *
     * @var array
     */
    private $order = [];

    /**
     * The number of items to pull back per page.
     *
     * @var int
     */
    private $limit = null;

    /**
     * The page to pull back.
     *
     * @var int
     */
    private $page = null;

    /**
     * The offset.
     *
     * @var int
     */
    private $offset = null;

    /**
     * Initialise the builder with a queryable resource.
     *
     * @param \McCaulay\Trustpilot\Query\Queryable $queryable
     */
    public function __construct(Queryable $queryable)
    {
        $this->queryable = $queryable;
    }

    /**
     * Build the query.
     *
     * @return array
     */
    private function build(): array
    {
        $query = [];

        // Append where conditions
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                // Convert boolean value to string
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                // Handle date time format
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d\TH:i:s');
                }

                // Set key / value
                $query[$key] = $value;
            }
        }

        // Append order by
        if (!empty($this->order)) {
            $query['orderBy'] = $this->order;
        }

        // Append limit
        if (!empty($this->limit)) {
            $query['perPage'] = $this->limit;
        }

        // Append page
        if (!empty($this->offset) || !empty($this->page)) {
            if (!empty($this->offset)) {
                $this->page = floor($this->offset / $this->limit) + 1;
            }

            $query['page'] = $this->page;
        }

        return $query;
    }

    /**
     * Get the queried items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return $this->queryable->perform($this->build());
    }

    /**
     * Get all the items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return $this->page(null)
            ->offset(null)
            ->limit(null)
            ->get();
    }

    /**
     * Get the first item.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->page(null)
            ->offset(null)
            ->limit(1)
            ->get();
    }

    /**
     * Set the order.
     *
     * @param string $field
     * @param string $order ("asc" or "desc")
     * @return \McCaulay\Trustpilot\Query\Builder
     */
    public function order(string $field, $order): Builder
    {
        $this->order[] = strtolower($field) . '.' . strtolower($order);
        return $this;
    }

    /**
     * Set the limit.
     *
     * @param int $limit
     * @return \McCaulay\Trustpilot\Query\Builder
     */
    public function limit(int $limit): Builder
    {
        // Handle boundaries
        if ($limit < 1 || $limit > 100) {
            $limit = null;
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the page.
     *
     * @param int $page
     * @return \McCaulay\Trustpilot\Query\Builder
     */
    public function page(int $page): Builder
    {
        // Handle boundaries
        if ($page < 1) {
            $page = null;
        }

        $this->page = $page;
        return $this;
    }

    /**
     * Set the offset.
     *
     * @param int $offset
     * @return \McCaulay\Trustpilot\Query\Builder
     */
    public function offset(int $offset): Builder
    {
        // Handle boundaries
        if ($offset < 1) {
            $offset = null;
        }

        $this->offset = $offset;
        return $this;
    }
}