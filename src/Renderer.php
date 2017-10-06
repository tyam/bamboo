<?php

namespace tyam\bamboo;

use ArrayAccess;

class Renderer
{
    private $resolve;
    private $sections;
    private $content;
    private $next;
    private $sectionStack;

    public function __construct(Callable $resolve, ArrayAccess $sections, string $content = '')
    {
        $this->resolve = $resolve;
        $this->sections = $sections;
        $this->content = $content;
        $this->next = null;
        $this->sectionStack = [];
    }

    public function render(string $template, array $variables = null): string
    {
        list($path__, $env__) = call_user_func($this->resolve, $template, $variables);
        
        $renderer = $this;
        unset($template, $variables);
        extract($env__);
        ob_start();
        require($path__);
        $output = ob_get_clean();

        if ($this->next) {
            // wrapper has been specified. Then continue wrapper with current output.
            list($template, $variables) = $this->next;
            $next = new Renderer($this->resolve, $this->sections, $output);
            return $next->render($template, $variables);
        } else {
            return $output;
        }
    }

    public function wrap(string $template, array $variables = null)
    {
        if ($this->next) {
            // You can specify wrapper at most once.
            throw new \LogicException('specifying wrapper twice');
        }
        $this->next = [$template, $variables];
    }

    public function content()
    {
        echo $this->content;
    }

    public function section(string $name)
    {
        array_unshift($this->sectionStack, $name);
        ob_start();
    }

    public function endsection(string $name = null)
    {
        // You can specify the section name for sanity-check purpose.
        if (! is_null($name) && $this->sectionStack[0] != $name) {
            throw new \LogicException('unmatched end-section: ' . $name . ', expects: ' . $this->sectionStack[0]);
        }

        $output = ob_get_clean();

        $name = array_shift($this->sectionStack);
        $this->sections[$name] = $output;
    }

    public function yield(string $name)
    {
        if (empty($this->sections[$name])) {
            // do nothing
        } else {
            echo $this->sections[$name];
        }
    }

    public function include(string $template, array $variables = null)
    {
        $renderer = new Renderer($this->resolve, $this->sections);
        echo $renderer->render($template, $variables);
    }
}