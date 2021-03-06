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

namespace RoaveTest\DeveloperTools\Inspection;

use ArrayObject;
use PHPUnit_Framework_Error;
use Roave\DeveloperTools\Inspection\AggregateInspection;
use Roave\DeveloperTools\Inspection\InspectionInterface;
use Roave\DeveloperTools\Inspection\TimeInspection;

/**
 * Tests for {@see \Roave\DeveloperTools\Inspection\AggregateInspection}
 *
 * @covers \Roave\DeveloperTools\Inspection\AggregateInspection
 */
class AggregateInspectionTest extends AbstractInspectionTest
{
    /**
     * {@inheritDoc}
     */
    protected function getInspection()
    {
        return new AggregateInspection([new TimeInspection(123, 456), new AggregateInspection([])]);
    }

    /**
     * @covers \Roave\DeveloperTools\Inspection\AggregateInspection::getInspectionData
     */
    public function testGetData()
    {
        $data = $this->getInspection()->getInspectionData();

        $this->assertInternalType('array', $data);
        $this->assertCount(2, $data);
        $this->assertInstanceOf(TimeInspection::class, $data[0]);
        $this->assertInstanceOf(AggregateInspection::class, $data[1]);
    }

    /**
     * @covers \Roave\DeveloperTools\Inspection\AggregateInspection::__construct
     */
    public function testAllowsTraversableParameters()
    {
        $mockInspection = $this->getMock(InspectionInterface::class);
        $inspection     = new AggregateInspection(new ArrayObject([$mockInspection]));

        $this->assertEquals($mockInspection, $inspection->getInspectionData()[0]);
    }

    /**
     * @covers \Roave\DeveloperTools\Inspection\AggregateInspection::__construct
     */
    public function testDisallowsInvalidInspectionTypes()
    {
        $this->setExpectedException(PHPUnit_Framework_Error::class);

        new AggregateInspection(['invalid']);
    }
}
