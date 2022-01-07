<?php
namespace Socieboy\Forum\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\CommonMarkConverter;
use Socieboy\Forum\Commands\MigrateForumCommand;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishFiles();
        $this->shareGlobalVariables();
        $this->loadViewsFrom(__DIR__ . '/../Views', 'Forum');
        $this->loadTranslationsFrom(__DIR__ . '/../Lang', 'Forum');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::register(\EasySlug\EasySlug\EasySlugServiceProvider::class);

        $this->app->bind(
            'command.forum.table',
            function ($app) {
                return new MigrateForumCommand();
            }
        );

        $this->commands('command.forum.table');
        include __DIR__ . '/../routes.php';
    }

    /**
     * Publish config files for the forum.
     */
    protected function publishFiles()
    {
        $this->publishes(
            [
                __DIR__ . '/../Config/forum.php' => base_path('config/forum.php'),
                __DIR__ . '/../Style/forum'      => base_path('resources/assets/less/forum'),
            ]
        );
    }

    /**
     * Share variables across the views.
     */
    protected function shareGlobalVariables()
    {
        view()->share('template', config('forum.template'));
        view()->share('content', config('forum.content'));

        view()->composer('Forum::Topics.index', function ($view) {
            $view->with('all', config('forum.icons.all'));
        });

        view()->composer(
            [
                'Forum::Conversations.show',
                'Forum::Replies.show',
            ],
            function ($view) {
                $view->with('commonMark', new CommonMarkConverter());
            }
        );
    }
}
