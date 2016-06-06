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

namespace qtism\data\content\interactions;

use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * A graphic gap-match interaction is a graphical interaction with a set of gaps that are 
 * defined as areas (hotspots) of the graphic image and an additional set of gap choices 
 * that are defined outside the image. The candidate must associate the gap choices with 
 * the gaps in the image and be able to review the image with the gaps filled in context, 
 * as indicated by their choices. Care should be taken when designing these interactions 
 * to ensure that the gaps in the image are a suitable size to receive the required gap 
 * choices. It must be clear to the candidate which hotspot each choice has been associated 
 * with. When associated, choices must appear wholly inside the gaps if at all possible and, 
 * where overlaps are required, should not hide each other completely. If the candidate 
 * indicates the association by positioning the choice over the gap (e.g., drag and drop) 
 * the system should 'snap' it to the nearest position that satisfies these requirements.
 * 
 * The graphicGapMatchInteraction must be bound to a response variable with base-type 
 * directedPair and multiple cardinality. The choices represent the source of the pairing 
 * and the gaps in the image (the hotspots) the targets. Unlike the simple gapMatchInteraction, 
 * each gap can have several choices associated with it if desired, furthermore, the same 
 * choice may be associated with an associableHotspot multiple times, in which case the 
 * corresponding directed pair appears multiple times in the value of the response variable.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GraphicGapMatchInteraction extends GraphicInteraction {
    
    /**
     * From IMS QTI:
     * 
     * An ordered list of choices for filling the gaps. There may be 
     * fewer choices than gaps if required.
     * 
     * @var GapImgCollection
     * @qtism-bean-property
     */
    private $gapImgs;
    
    /**
     * From IMS QTI:
     * 
     * The hotspots that define the gaps that are to be filled
     * by the candidate.
     * 
     * @var AssociableHotspotCollection
     * @qtism-bean-property
     */
    private $associableHotspots;
    
    /**
     * Create a new GraphicGapMatchInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the response associated with the interaction.
     * @param Object $object An image as an Object object.
     * @param GapImgCollection $gapImgs A collection of GapImg objects.
     * @param AssociableHotspotCollection $associableHotspots A collection of AssociableHotspot object.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, $object, GapImgCollection $gapImgs, AssociableHotspotCollection $associableHotspots, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $object, $id, $class, $lang, $label);
        $this->setGapImgs($gapImgs);
        $this->setAssociableHotspots($associableHotspots);
    }
    
    /**
     * Set the list of choices for filling the gaps.
     * 
     * @param GapImgCollection $gapImgs A collection of GapImg objects.
     * @throws InvalidArgumentException If $gapImgs is empty.
     */
    public function setGapImgs(GapImgCollection $gapImgs) {
        if (count($gapImgs) > 0) {
            $this->gapImgs = $gapImgs;
        }
        else {
            $msg = "A GraphicGapMatch interaction must composed of at least 1 GapImg object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the list of choices for filling the gaps.
     * 
     * @return GapImgCollection A collection of GapImg objects.
     */
    public function getGapImgs() {
        return $this->gapImgs;
    }
    
    /**
     * Set the hotspots that define the gaps that are to be filled by the candidate.
     * 
     * @param AssociableHotspotCollection $associableHotspots A collection of AssociableHotspot objects.
     * @throws InvalidArgumentException If $associableHotspots is empty.
     */
    public function setAssociableHotspots(AssociableHotspotCollection $associableHotspots) {
        if (count($associableHotspots) > 0) {
            $this->associableHotspots = $associableHotspots;
        }
        else {
            $msg = "A GraphicGapMatch interaction must be composed of at least 1 AssociableHotspot object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hotspots that define the gaps that are to be filled by the candidate.
     * 
     * @return AssociableHotspotCollection A collection of AssociableHotspot objects.
     */
    public function getAssociableHotspots() {
        return $this->associableHotspots;
    }

    public function getComponents() {
        return new QtiComponentCollection(array_merge(array($this->getObject()), $this->getGapImgs()->getArrayCopy(), $this->getAssociableHotspots()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'graphicGapMatchInteraction';
    }
}