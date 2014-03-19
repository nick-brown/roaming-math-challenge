<?php

namespace Crawler;

/**
 * Class Solution
 *
 * stores and processes answers for the solution
 */
class Solution
{
    /**
     * unsigned 64-bit integer found on the Goal page
     *
     * @var int
     */
    public $goal = 0;

    /**
     * unsigned integer representing the number of unique pages that must be visited to exhaustively explore all pages
     *
     * @var int
     */
    public $node_count = 0;

    /**
     * shortest possible list of unsigned 64-bit integers from pages that must be visited to reach the goal page from
     * the start page
     *
     * @var array
     */
    private $shortest_path = array();

    /**
     * number of unique directed cycles / infinite loops
     *
     * @var int
     */
    public $directed_cycle_count = 0;

    /**
     * finds and sets the shortest path to the goal
     *
     * @param $all_goal_paths
     */
    public function setShortestGoalPath($all_goal_paths)
    {
        // Get the lengths of every path that leads to a goal
        $path_lengths = array_map('count', $all_goal_paths) ;

        // Find the shortest path length and get its key
        $shortest_path_key = array_flip($path_lengths)[min($path_lengths)];

        // Use the key to get the full path
        $this->shortest_path = $all_goal_paths[$shortest_path_key];
    }

    /**
     * returns the required information as a json string
     *
     * @return string
     */
    public function toJson()
    {
        $solution = [
            "goal" => $this->goal,
            "node_count" => $this->node_count,
            "shortest_path" => $this->shortest_path,
            "directed_cycle_count" => $this->directed_cycle_count
        ];

        return json_encode($solution);
    }
}
