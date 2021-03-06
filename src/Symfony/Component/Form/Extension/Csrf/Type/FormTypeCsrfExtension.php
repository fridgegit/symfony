<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Csrf\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class FormTypeCsrfExtension extends AbstractTypeExtension
{
    private $enabled;
    private $fieldName;

    public function __construct($enabled = true, $fieldName = '_token')
    {
        $this->enabled = $enabled;
        $this->fieldName = $fieldName;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['csrf_protection']) {
            $csrfOptions = array('intention' => $options['intention']);

            if ($options['csrf_provider']) {
                $csrfOptions['csrf_provider'] = $options['csrf_provider'];
            }

            $builder->add($options['csrf_field_name'], 'csrf', $csrfOptions)
                ->setAttribute('csrf_field_name', $options['csrf_field_name']);
        }
    }

    public function buildViewBottomUp(FormView $view, FormInterface $form)
    {
        if ($view->hasParent() && $form->hasAttribute('csrf_field_name')) {
            $name = $form->getAttribute('csrf_field_name');

            if (isset($view[$name])) {
                unset($view[$name]);
            }
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'csrf_protection' => $this->enabled,
            'csrf_field_name' => $this->fieldName,
            'csrf_provider'   => null,
            'intention'  => 'unknown',
        );
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
