<?php

namespace Tests\Support\Parity;

/**
 * One domain's parity checks for the CI3 -> CI4 data-layer port.
 *
 * Each check runs the ORIGINAL CI3 SQL (copied verbatim from the old code, with
 * the sample parameter values substituted the way CI3 concatenated them) and
 * the NEW CI4 model method, against the same database, and compares the rows.
 *
 * Only read queries belong here. Writes are reviewed, not executed: the live
 * data contains MyISAM tables, which cannot be rolled back.
 */
interface ParitySuite
{
    /** Short domain name, e.g. "franchise". */
    public function domain(): string;

    /**
     * @return list<array{
     *   label: string,          // "home_model::get_franchises (home_model.php:812)"
     *   old_sql: string,        // the CI3 query exactly as CI3 would have sent it
     *   new: callable,          // fn(): mixed - calls the CI4 model method
     *   compare?: string,       // 'rows' (default: same rows, same order), 'set' (same rows, any order),
     *                           // 'row' (first row only; old_sql gives row_array()), 'count', 'scalar'
     *   note?: string,          // why an intended difference exists (e.g. MySQL 8 GROUP BY fix)
     * }>
     */
    public function checks(): array;
}
