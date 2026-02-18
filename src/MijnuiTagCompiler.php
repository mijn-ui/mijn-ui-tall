<?php

namespace Mijnui\Mijnui;

use Illuminate\View\Compilers\ComponentTagCompiler;

class MijnuiTagCompiler extends ComponentTagCompiler
{
    public function componentString(string $component, array $attributes)
    {
        if ($component === 'mijnui::delegate-component') {
            $component = $attributes['component'];

            $class = \Illuminate\View\AnonymousComponent::class;

            return "##BEGIN-COMPONENT-CLASS##@component('{$class}', 'mijnui::' . {$component}, [
    'view' => md5('mijnui') . '::' . {$component},
    'data' => \$__env->getCurrentComponentData(),
])
<?php \$component->withAttributes(\$attributes->getAttributes()); ?>";
        }

        return parent::componentString($component, $attributes);
    }

    public function compileOpeningTags(string $value)
    {
        $pattern = "/<\s*mijnui[\:]([\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
                            )
                            |
                            (?:
                                [\w\-:.@%]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            $component = $matches[1];
            $output = '';

            // Handle dot syntax for slots
            if (str_contains($component, '.')) {
                $parts = explode('.', $component);
                $slotName = end($parts);
                $slotableSuffixes = ['trigger', 'content', 'header', 'footer', 'title', 'description', 'action', 'close'];

                if (in_array($slotName, $slotableSuffixes)) {
                    $output .= "@slot('{$slotName}') ";
                }
            }

            $output .= $this->componentString('mijnui::' . $component, $attributes);

            return $output;
        }, $value);
    }

    public function compileSelfClosingTags(string $value)
    {
        $pattern = "/
            <
                \s*
                mijnui[\:]([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
                            )
                            |
                            (?:
                                [\w\-:.@%]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
            \/>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            $component = $matches[1];
            $output = '';
            $isSlot = false;

            // Handle dot syntax for slots
            if (str_contains($component, '.')) {
                $parts = explode('.', $component);
                $slotName = end($parts);
                $slotableSuffixes = ['trigger', 'content', 'header', 'footer', 'title', 'description', 'action', 'close'];

                if (in_array($slotName, $slotableSuffixes)) {
                    $output .= "@slot('{$slotName}') ";
                    $isSlot = true;
                }
            }

            // Support inline "slot" attributes...
            if (isset($attributes['slot'])) {
                $slot = $attributes['slot'];

                unset($attributes['slot']);

                return '@slot(' . $slot . ') ' . $this->componentString('mijnui::' . $component, $attributes) . "\n@endComponentClass##END-COMPONENT-CLASS##" . ' @endslot';
            }

            $output .= $this->componentString('mijnui::' . $component, $attributes) . "\n@endComponentClass##END-COMPONENT-CLASS##";

            if ($isSlot) {
                $output .= ' @endslot';
            }

            return $output;
        }, $value);
    }

    public function compileClosingTags(string $value)
    {
        $pattern = "/<\/\s*mijnui[\:]([\w\-\:\.]*)\s*>/";

        return preg_replace_callback($pattern, function (array $matches) {
            $component = $matches[1];
            $output = ' @endComponentClass##END-COMPONENT-CLASS##';

            if (str_contains($component, '.')) {
                $parts = explode('.', $component);
                $slotName = end($parts);
                $slotableSuffixes = ['trigger', 'content', 'header', 'footer', 'title', 'description', 'action', 'close'];

                if (in_array($slotName, $slotableSuffixes)) {
                    $output .= " @endslot";
                }
            }

            return $output;
        }, $value);
    }
}
