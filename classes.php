<?php

/* Class Crawler
 *
 * finds target goal page given a starting page and collections properties crawled pages
 */
class Crawler {
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
        $this->solution->set_shortest_goal_path($this->all_paths_to_goal);
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
        $contents = file_get_contents('http://www.crunchyroll.com/tech-challenge/roaming-math/nick@nickbrown.me/' . $integer);

        if($contents == 'GOAL')
        {
            $this->solution->goal = $integer;
            $this->all_paths_to_goal[] = $this->current_path;

        }
        elseif($contents != 'DEADEND')
        {
            $expressions_array = explode("\n", $contents);

            foreach($expressions_array as $exp)
            {
                $int = Evaluate::process($exp);

                if( ! in_array($int, $this->visited))
                {
                    $this->visited[] = $int;
                }

                if(in_array($int, $this->current_path)){

                    $unique = TRUE;
                    foreach($this->directed_cycles as $cycle)
                    {
                        if(in_array($int, $cycle))
                        {
                            $unique = FALSE;
                            break;
                        }
                    }

                    if($unique == TRUE)
                    {
                        $this->directed_cycles[] = $this->current_path;
                    }
                }
                else
                {
                    $this->current_path[] = $int;
                    $this->run($int);
                    array_pop($this->current_path);
                }
            }
        }
    }
}


/**
 * Class Evaluate
 *
 * utility class that provides evaluation methods
 */
class Evaluate {
    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function add($val1, $val2)
    {
        return $val1 + $val2;
    }

    /**
     * @param $value
     *
     * @return number
     */
    private static function abs($value)
    {
        return abs($value);
    }

    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function multiply($val1, $val2)
    {
        return $val1 * $val2;
    }

    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function subtract($val1, $val2)
    {
        return $val1 - $val2;
    }

    /**
     * finds an executes an expression that has integers for arguments
     *
     * @param $expression
     *
     * @return mixed
     */
    private static function execute_next_method($expression)
    {
        // Breaks
        return preg_replace_callback('/([a-z]+)\((-?[0-9]+)(,-?[0-9]+)?\)/',
            function($match)
            {
                if(isset($match[3]))
                {
                    $match[3] = ltrim($match[3], ',');
                }

                switch($match[1])
                {
                    case 'add':
                        return self::add($match[2], $match[3]);
                        break;
                    case 'multiply':
                        return self::multiply($match[2], $match[3]);
                        break;
                    case 'subtract':
                        return self::subtract($match[2], $match[3]);
                        break;
                    case 'abs':
                        return self::abs($match[2]);
                        break;
                }
            },
            $expression,
            1
        );
    }

    /**
     * takes an expression and processes each method, returning a single integer
     *
     * @param $expression
     *
     * @return int
     */
    public static function process($expression)
    {
        // For each open parentheses ( in the string, locate and execute the next method that can be executed
        for($x = 0, $count = preg_match_all('/\(/', $expression); $x < $count; $x++)
        {
            $expression = self::execute_next_method($expression);
        }

        // Cast the final string as an integer so json_encode will not surround with quotes
        return (int) $expression;
    }
}

/**
 * Class Solution
 *
 * stores and processes answers for the solution
 */
class Solution {
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
     * shortest possible list of unsigned 64-bit integers from pages that must be visited to reach the goal page from the start page
     *
     * @var array
     */
    private $shortest_path = Array();

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
    public function set_shortest_goal_path($all_goal_paths)
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
    public function to_json()
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