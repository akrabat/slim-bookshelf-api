<?php
namespace Error\Exception;

use Crell\ApiProblem\ApiProblem;

/**
 * Exception that when thrown creates an ApiProblem that the Error handler
 * can then use to render
 */
class ProblemException extends \RuntimeException
{
    /**
     * @var ApiProblem
     */
    protected $problem;

    /**
     * @param ApiProblem  $problem  ApiProblem object
     * @param integer $code         HTTP status code if $problem is a string
     * @param \Throwable  $previous Previous exception or error
     */
    public function __construct(ApiProblem $problem, $code = 0, $previous = null)
    {
        $this->setProblem($problem);
        $message = $problem->getTitle();
        parent::__construct($message, $code, $previous);
    }

    /**
     * Getter for problem
     *
     * @return mixed
     */
    public function getProblem()
    {
        return $this->problem;
    }

    /**
     * Setter for problem
     *
     * @param mixed $problem Value to set
     * @return self
     */
    public function setProblem($problem)
    {
        $this->problem = $problem;
        return $this;
    }
}
