<?php

namespace Mijnui\Mijnui;

use Illuminate\View\Compilers\ComponentTagCompiler;

/**
 * Compiles <mijnui:*> tags into standard Blade anonymous component syntax.
 *
 * Extends Laravel's ComponentTagCompiler to intercept tags prefixed with "mijnui:"
 * and transform them into the corresponding Blade component calls under the 'mijnui' namespace.
 *
 * @package Mijnui\Mijnui
 */
class MijnuiTagCompiler extends ComponentTagCompiler
{
    /**
     * Build the component string for a given MijnUI component.
     *
     * Handles a special 'delegate-component' case where the component name is passed
     * dynamically via an attribute, enabling runtime component resolution. For all other
     * components, delegates to Laravel's default componentString().
     */
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

    /**
     * Compile opening <mijnui:component-name> tags (non-self-closing).
     *
     * The regex matches "<mijnui:" followed by the component name (letters, hyphens, dots, colons),
     * then captures all attributes. Supported attribute forms:
     *   - @class(...) / @style(...) directives with balanced parentheses (recursive matching)
     *   - {{ $attributes }} spread syntax
     *   - :$variable shorthand for bound attributes
     *   - Standard key="value", key='value', or boolean attributes
     * The negative lookbehind (?<![\/=\-]) prevents matching self-closing or malformed tags.
     */
    public function compileOpeningTags(string $value)
    {
        // Regex breakdown:
        // <\s*mijnui[\:]([\w\-\:\.]*)  — Match "<mijnui:" and capture the component name
        // (?<attributes>...)            — Named group capturing all HTML/Blade attributes
        //   @(?:class|style)(...)       — Match @class() and @style() Blade directives with balanced parens
        //   \{\{\s*\\\$attributes...\}\}— Match {{ $attributes }} spread
        //   (\:\\\$)(\w+)              — Match :$variable shorthand for bound props
        //   [\w\-:.@%]+(=...)?         — Match standard HTML attributes with optional values
        // (?<![\/=\-])>                — Ensure tag ends with > but not /> (not self-closing)
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

            // Extract key-value pairs from the raw attribute string
            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            // The captured component name (e.g., "button", "card.header")
            $component = $matches[1];

            // Transform into a Blade component call under the 'mijnui' namespace
            return $this->componentString('mijnui::' . $component, $attributes);
        }, $value);
    }

    /**
     * Compile self-closing <mijnui:component-name /> tags.
     *
     * Uses the same attribute-matching regex as compileOpeningTags but matches
     * tags ending with "/>". Also supports an inline "slot" attribute: when present,
     * the component is wrapped in an @slot directive for named slot injection.
     */
    public function compileSelfClosingTags(string $value)
    {
        // Same attribute regex as opening tags, but terminates with /> instead of >
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

            // If a "slot" attribute is present, wrap the component in a named @slot directive
            // so it can be injected into a parent component's named slot
            if (isset($attributes['slot'])) {
                $slot = $attributes['slot'];

                unset($attributes['slot']);

                return '@slot(' . $slot . ') ' . $this->componentString('mijnui::' . $component, $attributes) . "\n@endComponentClass##END-COMPONENT-CLASS##" . ' @endslot';
            }

            // Self-closing tags emit the component and immediately close it
            return $this->componentString('mijnui::' . $component, $attributes) . "\n@endComponentClass##END-COMPONENT-CLASS##";
        }, $value);
    }

    /**
     * Compile closing </mijnui:component-name> tags.
     *
     * Matches "</mijnui:" followed by the component name and replaces it with
     * Blade's component-closing marker to end the component scope.
     */
    public function compileClosingTags(string $value)
    {
        // Match closing tags: </mijnui:component-name>
        $pattern = "/<\/\s*mijnui[\:]([\w\-\:\.]*)\s*>/";

        return preg_replace_callback($pattern, function (array $matches) {
            return ' @endComponentClass##END-COMPONENT-CLASS##';
        }, $value);
    }
}
