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

return [
    'service_manager' => array(
        'factories' => array(
            'Eye4web\ZfcUser\Pm\Options\ModuleOptions' => 'Eye4web\ZfcUser\Pm\Factory\Options\ModuleOptionsFactory',
            'Eye4web\ZfcUser\Pm\Service\PmService' => 'Eye4web\ZfcUser\Pm\Factory\Service\PmServiceFactory',
            'Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper' => 'Eye4web\ZfcUser\Pm\Factory\Mapper\DoctrineORM\PmMapperFactory'
        ),
        'invokables' => array(
            'Eye4web\ZfcUser\Pm\Form\NewConversationForm' => 'Eye4web\ZfcUser\Pm\Form\NewConversationForm',
            'Eye4web\ZfcUser\Pm\Form\NewMessageForm' => 'Eye4web\ZfcUser\Pm\Form\NewMessageForm'
        ),
    ),
    'controllers' => [
        'factories' => [
            'Eye4web\ZfcUser\Pm\Controller\PmController' => 'Eye4web\ZfcUser\Pm\Factory\Controller\PmControllerFactory'
        ]
    ],
    'view_helpers' => [
        'factories' => [
            'ZfcUserPm' => 'Eye4web\ZfcUser\Pm\Factory\View\Helper\ZfcUserPmHelperFactory'
        ]
    ],
    'router' => [
        'routes' => [
            'eye4web' => [
                'type' => 'Eye4web\Base\Mvc\Router\Http\NamespaceRoute',
                'child_routes' => [
                    'zfc-user' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route'    => '/user',
                            'defaults' => [
                                'controller' => 'Eye4web\ZfcUser\Pm\Controller\PmController',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'pm' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route'    => '/pm',
                                    'defaults' => [
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'new-conversation' => [
                                        'type' => 'Zend\Mvc\Router\Http\Literal',
                                        'options' => [
                                            'route'    => '/new-conversation',
                                            'defaults' => [
                                                'action'     => 'newConversation',
                                            ],
                                        ],
                                    ],
                                    'read-conversation' => [
                                        'type' => 'Zend\Mvc\Router\Http\Segment',
                                        'options' => [
                                            'route'    => '/:conversationId',
                                            'defaults' => [
                                                'action'     => 'readConversation',
                                            ],
                                            'constraints' => [
                                                'conversationId' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
                                            ]
                                        ],
                                    ],
                                ]
                            ],
                        ]
                    ],
                ]
            ]
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'eye4web_zfcuser_pm_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => [
                    'default' => __DIR__ . '/doctrine',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Eye4web\ZfcUser\Pm\Entity' => 'eye4web_zfcuser_pm_driver'
                ]
            ]
        ],
    ],
];
