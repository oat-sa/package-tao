<?php
/**
 * This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; under version 2
* of the License (non-upgradable).
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*
* Copyright (c) 2014 (original work) Open Assessment Technologies SA;
*
*/
namespace oat\tao\model\controllerMap;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

/**
 * Reflection class for a @requiresRight tag in a Docblock.
 * 
 * To be use with the phpDocumentor 
 *
 * @author  Joel Bout <joel@taotesting.com>
 */
class RequiresRightTag extends Tag
{
    /** @var string The raw type component. */
    protected $parameter = '';

    /** @var string The parsed type component. */
    protected $rightId = null;

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (null === $this->content) {
            $this->content = "{$this->parameter} {$this->right} {$this->description}";
        }

        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        parent::setContent($content);

        $parts = preg_split('/\s+/Su', $this->description, 3);

        if (count($parts) >= 2) {
            $this->parameter = $parts[0];
            $this->rightId = $parts[1];
        } 

        $this->setDescription(isset($parts[2]) ? $parts[2] : '');

        $this->content = $content;
        return $this;
    }

    /**
     * Returns the name of the parameter
     *
     * @return string
     */
    public function getParameterName()
    {
        return (string) $this->parameter;
    }
    
    /**
     * Returns the identifier of the required access right
     *
     * @return string
     */
    public function getRightId()
    {
        return (string) $this->rightId;
    }
    

}
