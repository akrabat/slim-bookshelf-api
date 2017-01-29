<?php
namespace Error;

use Crell\ApiProblem\ApiProblem as BaseApiProblem;

/**
 * Simple override of Crell\ApiProblem\ApiProblem to allow setting the status
 * code as the third parameter and detail as the fouth parameter in the
 * constructor. It looks cleaner that way to my eyes.
 */
class ApiProblem extends BaseApiProblem
{
    /**
     * Constructs a new ApiProblem.
     *
     * @param string $title
     *   A short, human-readable summary of the problem type.  It SHOULD NOT
     *   change from occurrence to occurrence of the problem, except for
     *   purposes of localization.
     * @param string $type
     *   An absolute URI (RFC3986) that identifies the problem type.  When
     *   dereferenced, it SHOULD provide human-readable documentation for the
     *   problem type (e.g., using HTML).
     * @param int $status
     *   A valid HTTP status code.
     * @param string $detail
     *   The human-readable detail string about this problem.
     */
    public function __construct($title = '', $type = 'about:blank', $status = null, $detail = null)
    {
        parent::__construct($title, $type);
        $this->setStatus($status);
        $this->setDetail($detail);
    }
}
