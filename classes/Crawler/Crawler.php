<?php

namespace Crawler;

/* Class Crawler
 *
 * finds target goal page given a starting page and collections properties crawled pages
 */
class Crawler
{
    public function __construct()
    {
        // Instantiate a new solution object for this Crawl
        $this->solution = new Solution();

        // Evaluate the starting point, set it as the first point on our current path and visited, and run the Crawler
        $integer = Evaluate::process("abs(add(add(5,add(227,abs(5558))),abs(multiply(-1,12198))))");
        $this->current_path[] = $this->visited[] = $integer;
        $this->run($integer);

        // After the run is complete, add missing information to the solution object
        $this->solution->node_count = count($this->visited);
        $this->solution->directed_cycle_count = count($this->directed_cycles);
        $this->solution->setShortestGoalPath($this->all_paths_to_goal);
    }

    /**
     * this crawler's solution object
     *
     * @var Solution
     */
    public $solution;

    /**
     * all pages that have been visited
     *
     * @var array
     */
    private $visited = [];

    /**
     * the current path the crawler has taken from the beginning of its run
     *
     * @var array
     */
    private $current_path = [];

    /**
     * every path that leads to a goal
     *
     * @var array
     */
    private $all_paths_to_goal = [];

    /**
     * list of directed cycles: unique directed cycles, or infinite loops. A directed cycle is where a list page
     * contains a link to another page that ultimately links back to the same page
     *
     * @var array
     */
    private $directed_cycles = [];

    /**
     * retrieves content from the page with the given integer, then processes it
     *
     * @param $integer
     */
    public function run($integer)
    {
        $contents = file_get_contents(
            'http://www.crunchyroll.com/tech-challenge/roaming-math/nick@nickbrown.me/' .$integer
        );

        if ($contents == 'GOAL') {
            $this->solution->goal = $integer;
            $this->all_paths_to_goal[] = $this->current_path;
        } elseif ($contents != 'DEADEND') {
            $expressions_array = explode("\n", $contents);

            foreach ($expressions_array as $exp) {
                $int = Evaluate::process($exp);

                if (!in_array($int, $this->visited)) {
                    $this->visited[] = $int;
                }

                if (in_array($int, $this->current_path)) {

                    $unique = true;
                    foreach ($this->directed_cycles as $cycle) {
                        if (in_array($int, $cycle)) {
                            $unique = false;
                            break;
                        }
                    }

                    if ($unique == true) {
                        $this->directed_cycles[] = $this->current_path;
                    }
                } else {
                    $this->current_path[] = $int;
                    $this->run($int);
                    array_pop($this->current_path);
                }
            }
        }
    }
}
