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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManager;

use PHPCR\NodeInterface;
use PHPCR\PropertyType;

class DocumentNameMapper implements DocumentNameMapperInterface
{
    /**
     * Determine the document name from a given node
     *
     * @param DocumentManager
     * @param NodeInterface $node
     * @param string $documentName
     *
     * @return string
     *
     * @throws \RuntimeException if no class name could be determined
     */
    public function getClassName(DocumentManager $dm, NodeInterface $node, $documentName = null)
    {
        if (isset($documentName)) {
            $className = $documentName;
        } else if ($node->hasProperty('phpcr:class')) {
            $className = $node->getProperty('phpcr:class')->getString();
        } else if ($node->hasProperty('phpcr:alias')) {
            $aliasName = $node->getProperty('phpcr:alias')->getString();
            $class = $dm->getMetadataFactory()->getMetadataForAlias($aliasName);
            $className = $class->name;
        }

        // default to the built in generic document class
        if (empty($className)) {
            $className = 'Doctrine\ODM\PHPCR\Document\Generic';
        }

        return $className;
    }

    /**
     * Determine the document name from a given node
     *
     * @param DocumentManager
     * @param NodeInterface $node
     * @param string $className
     */
    public function writeMetadata(DocumentManager $dm, NodeInterface $node, $className)
    {
        $node->setProperty('phpcr:class', $className, PropertyType::STRING);
    }
}
