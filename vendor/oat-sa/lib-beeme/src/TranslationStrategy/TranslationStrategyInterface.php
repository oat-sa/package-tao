<?php

namespace oat\beeme\TranslationStrategy;

/**
 * Translation strategy interface.
 *
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 */
interface TranslationStrategyInterface
{
    public function translate(array $tokens);
}
