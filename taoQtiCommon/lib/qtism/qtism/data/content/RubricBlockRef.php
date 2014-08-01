<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content;

use qtism\data\QtiIdentifiable;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\common\utils\Format;
use \SplObserver;
use \SplObjectStorage;
use \InvalidArgumentException;

/**
 * An extension of QTI that represents a reference
 * to an external QTI rubricBlock. It works in a similar
 * way than QTI's assessmentSectionRef.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RubricBlockRef extends QtiComponent implements QtiIdentifiable {
    
    /**
     * A collection of SplObservers.
     *
     * @var SplObjectStorage
     */
    private $observers = null;
    
    /**
     * The identifier of the rubricBlockRef.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * The URI referencing the file containing
     * the definition of the external rubricBlock.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $href;
    
    /**
     * Create a new RubricBlockRef object.
     * 
     * @param string $identifier A QTI identifier.
     * @param string $href A URI locating the external rubrickBlock definition.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct($identifier, $href) {
        $this->setIdentifier($identifier);
        $this->setHref($href);
        $this->setObservers(new SplObjectStorage()); 
    }
    
    /**
     * Set the identifier of the rubricBlockRef.
     * 
     * @param string $identifier A QTI identifier.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the identifier of the rubricBlockRef.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the URI locating the external rubricBlock definition.
     * 
     * @param string $href A URI.
     * @throws InvalidArgumentException If $href is not a valid URI.
     */
    public function setHref($href) {
        if (Format::isUri($href) === true) {
            $this->href = $href;
        }
        else {
            $msg = "The 'href' argument must be a valid URI, '" . $href . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the URI locating the external rubrickBlock definition.
     * 
     * @return string A URI.
     */
    public function getHref() {
        return $this->href;
    }
    
    /**
     * Get the observers of the object.
     *
     * @return SplObjectStorage An SplObjectStorage object.
     */
    protected function getObservers() {
        return $this->observers;
    }
    
    /**
     * Set the observers of the object.
     *
     * @param SplObjectStorage $observers An SplObjectStorage object.
     */
    protected function setObservers(SplObjectStorage $observers) {
        $this->observers = $observers;
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
    
    public function getQtiClassName() {
        return 'rubricBlockRef';
    }
    
    /**
     * SplSubject::attach implementation.
     *
     * @param SplObserver An SplObserver object.
     */
    public function attach(SplObserver $observer) {
        $this->getObservers()->attach($observer);
    }
    
    /**
     * SplSubject::detach implementation.
     *
     * @param SplObserver $observer An SplObserver object.
     */
    public function detach(SplObserver $observer) {
        $this->getObservers()->detach($observer);
    }
    
    /**
     * SplSubject::notify implementation.
     */
    public function notify() {
        foreach ($this->getObservers() as $observer) {
            $observer->update($this);
        }
    }
}