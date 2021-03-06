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
 * and is licensed under the MIT license.
 */

namespace RoaveTest\DeveloperTools\Inspector;

use ArrayObject;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;
use Roave\DeveloperTools\Inspection\AggregateInspection;
use Roave\DeveloperTools\Inspection\InspectionInterface;
use Roave\DeveloperTools\Inspector\AggregateInspector;
use Roave\DeveloperTools\Inspector\InspectorInterface;
use Zend\EventManager\EventInterface;

/**
 * Tests for {@see \Roave\DeveloperTools\Inspector\AggregateInspector}
 *
 * @covers \Roave\DeveloperTools\Inspector\AggregateInspector
 */
class AggregateInspectorTest extends PHPUnit_Framework_TestCase
{
    public function testAggregateInspectorWithoutInspectors()
    {
        $inspector  = new AggregateInspector([]);
        $inspection = $inspector->inspect($this->getMock(EventInterface::class));

        $this->assertInstanceOf(AggregateInspection::class, $inspection);
        $this->assertEmpty($inspection->getInspectionData());
    }

    public function testAggregateInspectorWithSingleInspector()
    {
        $event       = $this->getMock(EventInterface::class);
        $inspector1  = $this->getMock(InspectorInterface::class);
        $inspector2  = $this->getMock(InspectorInterface::class);
        $inspection1 = $this->getMock(InspectionInterface::class);
        $inspection2 = $this->getMock(InspectionInterface::class);
        $inspector   = new AggregateInspector([$inspector1, $inspector2]);

        $inspector1->expects($this->any())->method('inspect')->with($event)->will($this->returnValue($inspection1));
        $inspector2->expects($this->any())->method('inspect')->with($event)->will($this->returnValue($inspection2));

        $this->assertEquals([$inspection1, $inspection2], $inspector->inspect($event)->getInspectionData());
    }

    public function testAllowsTraversableParameters()
    {
        $event         = $this->getMock(EventInterface::class);
        $mockInspector = $this->getMock(InspectorInterface::class);
        $inspection    = $this->getMock(InspectionInterface::class);
        $inspector     = new AggregateInspector(new ArrayObject([$mockInspector]));

        $mockInspector->expects($this->any())->method('inspect')->with($event)->will($this->returnValue($inspection));

        $this->assertEquals($inspection, $inspector->inspect($event)->getInspectionData()[0]);
    }

    public function testDisallowsInvalidInspectorTypes()
    {
        $this->setExpectedException(PHPUnit_Framework_Error::class);

        new AggregateInspector(['invalid']);
    }
}
