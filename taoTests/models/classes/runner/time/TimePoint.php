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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\models\runner\time;

/**
 * Class TimePoint
 * 
 * Describes a temporal point by storing a timestamp with microseconds and some flags.
 * A TimePoint can describe a START or a END temporal point used to define a time range.
 * Each TimePoint can be related to a target (CLIENT or SERVER).
 * A list of tags can be attached to a TimePoint to explain its role or its context.
 * 
 * @package oat\taoTests\models\runner\time
 */
class TimePoint implements \Serializable
{
    /**
     * Type of TimePoint: start of range
     */
    const TYPE_START = 1;

    /**
     * Type of TimePoint: end of range
     */
    const TYPE_END = 2;

    /**
     * Represents all types of TimePoint
     */
    const TYPE_ALL = 3;

    /**
     * Type of TimePoint target: client side
     */
    const TARGET_CLIENT = 1;

    /**
     * Type of TimePoint target: server side
     */
    const TARGET_SERVER = 2;

    /**
     * Represents all types of TimePoint targets
     */
    const TARGET_ALL = 3;

    /**
     * The decimal precision used to compare timestamps
     */
    const PRECISION = 10000;

    /**
     * The timestamp representing the TimePoint
     * @var float
     */
    protected $timestamp = 0.0;

    /**
     * A collection of tags attached to the TimePoint
     * @var array
     */
    protected $tags = [];

    /**
     * The type of TimePoint. Must be a value from TYPE_START or TYPE_END constants.
     * @var int
     */
    protected $type = 0;

    /**
     * The type of target. Must be a value from TARGET_CLIENT or TARGET_SERVER constants.
     * @var int
     */
    protected $target = 0;

    /**
     * The unique reference to name the TimePoint
     * @var string
     */
    protected $ref;

    /**
     * QtiTimePoint constructor.
     * @param string|array $tags
     * @param float $timestamp
     * @param int $type
     * @param int $target
     */
    public function __construct($tags = null, $timestamp = null, $type = null, $target = null)
    {
        if (isset($tags)) {
            $this->setTags($tags);
        }

        if (isset($timestamp)) {
            $this->setTimestamp($timestamp);
        }

        if (isset($type)) {
            $this->setType($type);
        }

        if (isset($target)) {
            $this->setTarget($target);
        }
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        $data = [
            'ts' => $this->getTimestamp(),
            'type' => $this->getType(),
            'target' => $this->getTarget(),
            'tags' => $this->getTags(),
        ];
        return serialize($data);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if ($data) {
            if (isset($data['tags'])) {
                $this->setTags($data['tags']);
            }

            if (isset($data['ts'])) {
                $this->setTimestamp($data['ts']);
            }

            if (isset($data['type'])) {
                $this->setType($data['type']);
            }

            if (isset($data['target'])) {
                $this->setTarget($data['target']);
            }
        }
    }

    /**
     * Sets the timestamp of the TimePoint
     * @param float $timestamp
     * @return TimePoint
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = floatval($timestamp);
        return $this;
    }

    /**
     * Gets the timestamp of the TimePoint
     * @return float
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Gets the normalized value of the timestamp. This value is the result of:
     * `normalized_timestamp = timestamp_with_microseconds * precision`
     * @return int
     */
    public function getNormalizedTimestamp()
    {
        return floor($this->getTimestamp() * self::PRECISION);
    }

    /**
     * Sets the type of TimePoint
     * @param int $type Must be a value from TYPE_START or TYPE_END constants.
     * @return TimePoint
     */
    public function setType($type)
    {
        $this->type = intval($type);
        return $this;
    }

    /**
     * Gets the type of TimePoint
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the target type of the TimePoint
     * @param int $target Must be a value from TARGET_CLIENT or TARGET_SERVER constants.
     * @return TimePoint
     */
    public function setTarget($target)
    {
        $this->target = intval($target);
        return $this;
    }

    /**
     * Gets the target type of the TimePoint
     * @return int
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Adds another tag to the TimePoint
     * @param string $tag
     * @return TimePoint
     */
    public function addTag($tag)
    {
        $this->tags[] = (string)$tag;
        $this->ref = null;
        return $this;
    }

    /**
     * Removes a tag from the TimePoint
     * @param string $tag
     * @return TimePoint
     */
    public function removeTag($tag)
    {
        $index = array_search($tag, $this->tags);

        if ($index !== false) {
            array_splice($this->tags, $index, 1);
            $this->ref = null;
        }

        return $this;
    }

    /**
     * Gets a tag from the TimePoint. By default, it will return the first tag.
     * @param int $index
     * @return string
     */
    public function getTag($index = 0)
    {
        $index = min(max(0, $index), count($this->tags));
        return $this->tags[$index];
    }

    /**
     * Sets the tags of the TimePoint
     * @param string|array $tags
     * @return TimePoint
     */
    public function setTags($tags)
    {
        $this->tags = [];
        $this->ref = null;

        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $this->addTag($tag);
            }
        } else {
            $this->addTag($tags);
        }

        return $this;
    }

    /**
     * Gets all tags from the TimePoint
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Gets a unique reference to name the TimePoint
     * @return string
     */
    public function getRef()
    {
        if (is_null($this->ref)) {
            $tags = $this->tags;
            sort($tags);
            $this->ref = md5(implode('-', $tags));
        }
        return $this->ref;
    }

    /**
     * Checks if a TimePoint matches the criteria
     * @param array $tags
     * @param int $target
     * @param int $type
     * @return array
     */
    public function match(array $tags = null, $target = self::TARGET_ALL, $type = self::TYPE_ALL)
    {
        $match = ($this->getType() & $type) && ($this->getTarget() & $target);
        
        if ($match && isset($tags)) {
            $match = (count(array_intersect($tags, $this->getTags())) == count($tags));
        }
        
        return $match;
    }

    /**
     * Compares the TimePoint with another instance.
     * The comparison is made in this order:
     * - reference
     * - target
     * - timestamp
     * - type
     * 
     * CAUTION!: The result order is not based on chronological order. 
     * Its goal is to gather TimePoint by reference and target, then sort by type and timestamp.
     * 
     * @param TimePoint $point
     * @return int
     */
    public function compare(TimePoint $point)
    {
        $diff = strcmp($this->getRef(), $point->getRef());
        if ($diff == 0) {
            $diff = $this->getTarget() - $point->getTarget();
            if ($diff == 0) {
                $diff = $this->getNormalizedTimestamp() - $point->getNormalizedTimestamp();
                if ($diff == 0) {
                    $diff = $this->getType() - $point->getType();
                }
            }
        }
        return $diff;
    }

    /**
     * Sorts a range of TimePoint
     * @param array $range
     * @return array
     */
    public static function sort(array &$range) {
        usort($range, function(TimePoint $a, TimePoint $b) {
            return $a->compare($b);
        });
        return $range;
    }
}
