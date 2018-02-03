<?php

namespace Deadlyviruz\Extentions\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            // @module($slug)
            $bladeCompiler->directive('extention', function ($slug) {
                return "<?php if(Extentions::exists({$slug}) && Extentions::isEnabled({$slug})): ?>";
            });
            $bladeCompiler->directive('endextention', function () {
                return '<?php endif; ?>';
            });

        });
    }
}
