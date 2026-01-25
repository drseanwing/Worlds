<?php

namespace Worlds\Config;

/**
 * View class
 *
 * Implements a simple yet powerful template rendering engine with support
 * for layouts, sections, partials, and variable extraction for clean PHP templates.
 */
class View
{
    /**
     * @var string Base directory for view templates
     */
    private string $viewsPath;

    /**
     * @var array<string, mixed> Data to pass to templates
     */
    private array $data = [];

    /**
     * @var array<string, string> Section content storage
     */
    private array $sections = [];

    /**
     * @var string|null Current section being captured
     */
    private ?string $currentSection = null;

    /**
     * @var string|null Layout template to extend
     */
    private ?string $layout = null;

    /**
     * @var string Content of the child view
     */
    private string $childContent = '';

    /**
     * Create a new View instance
     *
     * @param string|null $viewsPath Base path for views (defaults to src/Views)
     */
    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? dirname(__DIR__) . '/Views';
    }

    /**
     * Render a template with given data
     *
     * Loads a PHP template file, extracts variables into the template scope,
     * and returns the rendered output as a string.
     *
     * @param string $template Template name (e.g., 'entities/show')
     * @param array<string, mixed> $data Variables to pass to template
     * @return string Rendered HTML output
     * @throws \RuntimeException If template file not found
     */
    public function render(string $template, array $data = []): string
    {
        // Store data for use in template
        $this->data = array_merge($this->data, $data);

        // Find the template file
        $templatePath = $this->findTemplate($template);

        // Render the child template
        $this->childContent = $this->renderTemplate($templatePath);

        // If a layout is specified, render it
        if ($this->layout !== null) {
            $layoutPath = $this->findTemplate($this->layout);
            $output = $this->renderTemplate($layoutPath);

            // Reset layout for next render
            $this->layout = null;

            return $output;
        }

        return $this->childContent;
    }

    /**
     * Find a template file by name
     *
     * Searches the views directory for the template file.
     * Supports both .php extension and no extension.
     *
     * @param string $template Template name
     * @return string Full path to template file
     * @throws \RuntimeException If template not found
     */
    private function findTemplate(string $template): string
    {
        // Normalize template path (convert dots to slashes, ensure .php extension)
        $template = str_replace('.', '/', $template);

        // Try with .php extension
        $path = $this->viewsPath . '/' . $template . '.php';

        if (file_exists($path)) {
            return $path;
        }

        // Try without adding .php (in case it's already there)
        $path = $this->viewsPath . '/' . $template;

        if (file_exists($path)) {
            return $path;
        }

        throw new \RuntimeException("Template not found: {$template}");
    }

    /**
     * Render a template file
     *
     * Extracts variables into scope and includes the PHP template file.
     *
     * @param string $templatePath Full path to template file
     * @return string Rendered output
     */
    private function renderTemplate(string $templatePath): string
    {
        // Extract data array to individual variables
        extract($this->data, EXTR_SKIP);

        // Start output buffering
        ob_start();

        // Include the template file
        // The template has access to:
        // - All extracted variables from $this->data
        // - $this (the View instance for helper methods)
        include $templatePath;

        // Get and clean buffer
        return ob_get_clean();
    }

    /**
     * Extend a layout template
     *
     * Called from child templates to specify which layout to use.
     *
     * @param string $layout Layout template name (e.g., 'layouts/base')
     * @return void
     */
    public function extends(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Start a section
     *
     * Begins capturing output for a named section.
     *
     * @param string $name Section name
     * @return void
     */
    public function section(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * End the current section
     *
     * Stops capturing and stores the section content.
     *
     * @return void
     */
    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new \RuntimeException("No section started");
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    /**
     * Yield section content
     *
     * Outputs the content of a named section. If the section doesn't exist,
     * outputs the default content instead.
     *
     * @param string $name Section name
     * @param string $default Default content if section not defined
     * @return void
     */
    public function yield(string $name, string $default = ''): void
    {
        echo $this->sections[$name] ?? $default;
    }

    /**
     * Include a partial template
     *
     * Renders and outputs a sub-template, passing along the parent template's
     * variables plus any additional data.
     *
     * @param string $partial Partial template name (e.g., 'partials/header')
     * @param array<string, mixed> $data Additional variables to pass
     * @return void
     */
    public function include(string $partial, array $data = []): void
    {
        $partialPath = $this->findTemplate($partial);

        // Merge parent data with additional data
        $mergedData = array_merge($this->data, $data);

        // Extract variables for the partial
        extract($mergedData, EXTR_SKIP);

        // Include the partial
        include $partialPath;
    }

    /**
     * Set a data variable
     *
     * @param string $key Variable name
     * @param mixed $value Variable value
     * @return self For method chaining
     */
    public function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Set multiple data variables
     *
     * @param array<string, mixed> $data Variables to set
     * @return self For method chaining
     */
    public function withData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Get the views path
     *
     * @return string Views directory path
     */
    public function getViewsPath(): string
    {
        return $this->viewsPath;
    }

    /**
     * Check if a section has content
     *
     * @param string $name Section name
     * @return bool True if section exists and has content
     */
    public function hasSection(string $name): bool
    {
        return isset($this->sections[$name]) && !empty($this->sections[$name]);
    }

    /**
     * Get the content of a section without outputting it
     *
     * @param string $name Section name
     * @param string $default Default content if section not defined
     * @return string Section content
     */
    public function getSection(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Clear all sections
     *
     * @return void
     */
    public function clearSections(): void
    {
        $this->sections = [];
        $this->currentSection = null;
    }

    /**
     * Static factory method for quick rendering
     *
     * Provides a convenient way to render a template without creating an instance.
     *
     * @param string $template Template name
     * @param array<string, mixed> $data Variables to pass to template
     * @return string Rendered HTML output
     */
    public static function make(string $template, array $data = []): string
    {
        $view = new self();
        return $view->render($template, $data);
    }
}
