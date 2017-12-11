<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\FormatterManager;
use CollabCorp\Formatter\Rule;
use CollabCorp\Formatter\RuleAliases\Star;
use Illuminate\Support\ServiceProvider;

class FormatterServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FormatterManager::class, function ($app) {
            return new FormatterManager($app);
        });
        
        $this->app->alias(Formatter::class, 'collabCorp.formatter');
        $this->app->alias(Proxy::class, 'collabCorp.formatter.proxy');

        $this->registerRuleAliases();
    }

    /**
     * Rule aliases are a sort of special character trigger.
     * Meaning, each alias finds the occurences of their alias name,
     * and performs mutations upon accordingly.
     *
     * @return void
     */
    public function registerRuleAliases()
    {
        Rule::alias('*', new Star);
    }
}
