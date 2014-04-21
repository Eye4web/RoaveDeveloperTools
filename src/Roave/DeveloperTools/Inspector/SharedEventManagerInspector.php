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

namespace Roave\DeveloperTools\Inspector;

use Roave\DeveloperTools\Inspection\AggregateInspection;
use Roave\DeveloperTools\Inspection\EventInspection;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Inspector to be attached to a {@see \Zend\EventManager\SharedEventManagerInterface}
 * listens to all events and collects data for each event
 *
 * @TODO complete implementation
 */
class SharedEventManagerInspector implements InspectorInterface
{
    /**
     * @var \Roave\DeveloperTools\Inspection\InspectionInterface[]
     *
     * @todo affected by the same problems of the TimeInspector (about memleaks)!
     */
    private $recorded = [];

    /**
     * @param SharedEventManagerInterface $sharedEventManager
     */
    public function __construct(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach('*', '*', [$this, 'onEventStart'], PHP_INT_MAX - 1);
        $sharedEventManager->attach('*', '*', [$this, 'onEventEnd'], 0 - (PHP_INT_MAX - 1));
    }

    /**
     * @param EventInterface $event
     */
    public function onEventStart(EventInterface $event)
    {
        $eventId = $this->newEventId();

        $event->setParam(__CLASS__ . '_eventId', $eventId);

        $this->recorded[] = new EventInspection(
            $eventId,
            true,
            $event,
            $this->getTraceToEventManager()
        );
    }

    /**
     * @param EventInterface $event
     */
    public function onEventEnd(EventInterface $event)
    {
        $this->recorded[] = new EventInspection(
            $event->getParam(__CLASS__ . '_eventId') ?: $this->newEventId(),
            false,
            $event,
            $this->getTraceToEventManager()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function inspect(EventInterface $event)
    {
        $return = new AggregateInspection($this->recorded);

        $this->recorded = [];

        return $return;
    }

    /**
     * @return string unique identifier
     */
    private function newEventId()
    {
        return uniqid(true);
    }

    /**
     * @return array the current trace, sliced to the call to the EventManager
     */
    private function getTraceToEventManager()
    {
        $trace     = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $traceSize = count($trace);

        for ($traceFrame = 2; $traceFrame < $traceSize; $traceFrame += 1) {
            if (isset($trace[$traceFrame]['object'])
                && $trace[$traceFrame]['object'] instanceof EventManagerInterface
                && isset($trace[$traceFrame]['function'])
                && (
                    $trace[$traceFrame]['function'] === 'trigger'
                    || $trace[$traceFrame]['function'] === 'triggerUntil'
                )
            ) {
                return array_slice($trace, $traceFrame);
            }
        }

        return [];
    }
}
