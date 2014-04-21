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

namespace Roave\DeveloperTools\Mvc\Factory;

use Roave\DeveloperTools\Inspector\AggregateInspector;
use Roave\DeveloperTools\Mvc\Configuration\RoaveDeveloperToolsConfiguration;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible for instantiating a {@see \Roave\DeveloperTools\Inspector\AggregateInspector} that inspects
 * an {@see \Zend\Mvc\Application}
 */
class ApplicationInspectorFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return AggregateInspector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $config RoaveDeveloperToolsConfiguration */
        $config = $serviceLocator->get(RoaveDeveloperToolsConfiguration::class);

        return new AggregateInspector(array_map([$serviceLocator, 'get'], $config->getInspectors()));
    }
}