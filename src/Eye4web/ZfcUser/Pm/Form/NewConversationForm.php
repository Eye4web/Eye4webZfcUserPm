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

namespace Eye4web\ZfcUser\Pm\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class NewConversationForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'create-conversation')
    {
        parent::__construct($name);

        $this->add(
            [
            'name' => 'headline',
            'options' => [
                'label' => 'Headline',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
            'type'  => 'Text',
            ]
        );

        $this->add(
            [
            'name' => 'message',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Message',
            ],
            'type'  => 'Textarea',
            ]
        );

        $this->add(
            [
            'name' => 'to',
            'options' => [
                'label' => 'To',
            ],
            'type'  => 'Hidden',
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 999999999999,
                ],
            ],
            ]
        );

        $this->add(
            [
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Button',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Send',
                'class' => 'btn btn-success',
            ],
            'options' => [
                'label' => 'Send',
            ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name' => 'headline',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                        ],
                    ],
                ],
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
            [
                'name' => 'message',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 10,
                        ],
                    ],
                ],
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
            [
                'name' => 'to',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
        ];
    }
}
