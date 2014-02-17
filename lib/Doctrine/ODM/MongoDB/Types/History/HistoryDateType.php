<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ODM\MongoDB\Types\History;

use Doctrine\ODM\MongoDB\Types\DateType;

/**
 * The history Date type.
 *
 * @author Thomas Rothe <th.rothe@gmail.com>
 */
class HistoryDateType extends DateType
{
    public function convertToDatabaseValue($values)
    {
        if ($values instanceof \ArrayObject) {
            $values = (array) $values;
        }
        foreach ($values as &$value) {
            $value = parent::convertToDatabaseValue($value);
        }
        return (object) $values;
    }

    public function convertToPHPValue($value)
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof \MongoDate) {
            $date = new \DateTime();
            $date->setTimestamp($value->sec);
        } elseif (is_numeric($value)) {
            $date = new \DateTime();
            $date->setTimestamp($value);
        } elseif ($value instanceof \DateTime) {
            $date = $value;
        } else {
            $date = new \DateTime($value);
        }
        return $date;
    }

    public function closureToMongo()
    {
        return 'if ($value instanceof \DateTime) { $value = $value->getTimestamp(); } elseif (is_string($value)) { $value = strtotime($value); } $return = new \MongoDate($value);';
    }

    public function closureToPHP()
    {
        return '$value = current($value); if ($value instanceof \MongoDate) { $return = new \DateTime(); $return->setTimestamp($value->sec); } elseif (is_numeric($value)) { $return = new \DateTime(); $return->setTimestamp($value); } elseif ($value instanceof \DateTime) { $return = $value; } else { $return = new \DateTime($value); }';
    }
}
