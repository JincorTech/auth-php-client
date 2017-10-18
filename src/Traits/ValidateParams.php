<?php

namespace JincorTech\AuthClient\Traits;

use InvalidArgumentException;

/**
 * Trait ValidateParams.
 */
trait ValidateParams
{
    /**
     * @param array $params
     * @param array $requiredKeys
     */
    protected function validateParams(array $params, array $requiredKeys)
    {
        foreach ($requiredKeys as $key) {
            if (! isset($params[$key])) {
                throw new InvalidArgumentException(sprintf('Required key: %s is not specified', $key));
            }
        }
    }
}
